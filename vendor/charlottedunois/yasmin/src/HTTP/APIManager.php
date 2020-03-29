<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\HTTP;

/**
 * Handles the API.
 *
 * @property \CharlotteDunois\Yasmin\Client             $client
 * @property \CharlotteDunois\Yasmin\HTTP\APIEndpoints  $endpoints
 * @internal
 */
class APIManager {
    /**
     * @var \CharlotteDunois\Yasmin\Client
     */
    protected $client;
    
    /**
     * @var \CharlotteDunois\Yasmin\HTTP\APIEndpoints
     */
    protected $endpoints;
    
    /**
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;
    
    /**
     * @var \CharlotteDunois\Yasmin\HTTP\RatelimitBucket[]
     */
    protected $ratelimits = array();
    
    /**
     * Are we globally ratelimited?
     * @var bool
     */
    protected $limited = false;
    
    /**
     * Global rate limit limit.
     * @var int
     */
    protected $limit = 0;
    
    /**
     * Global rate limit remaining.
     * @var int
     */
    protected $remaining = \INF;
    
    /**
     * When can we send again?
     * @var float
     */
    protected $resetTime = 0.0;
    
    /**
     * The queue for our API requests.
     * @var array
     */
    protected $queue = array();
    
    /**
     * The class name of the bucket to use.
     * @var string
     */
    protected $bucketName;
    
    /**
     * Pending promises of buckets setting the ratelimit.
     * @var array
     */
    protected $bucketRatelimitPromises = array();
    
    /**
     * DO NOT initialize this class yourself.
     * @param \CharlotteDunois\Yasmin\Client $client
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client) {
        $this->client = $client;
        $this->endpoints = new \CharlotteDunois\Yasmin\HTTP\APIEndpoints($this);
        
        $this->loop = $this->client->loop;
        
        $this->bucketName = $client->getOption('http.ratelimitbucket.name', \CharlotteDunois\Yasmin\HTTP\RatelimitBucket::class);
    }
    
    /**
     * Default destructor.
     * @internal
     */
    function __destruct() {
        $this->clear();
    }
    
    /**
     * @param string  $name
     * @return bool
     * @throws \Exception
     * @internal
     */
    function __isset($name) {
        try {
            return $this->$name !== null;
        } catch (\RuntimeException $e) {
            if($e->getTrace()[0]['function'] === '__get') {
                return false;
            }
            
            throw $e;
        }
    }
    
    /**
     * @param string  $name
     * @return mixed
     * @throws \RuntimeException
     * @internal
     */
    function __get($name) {
        switch($name) {
            case 'client':
                return $this->client;
            break;
            case 'endpoints':
                return $this->endpoints;
            break;
        }
        
        throw new \RuntimeException('Unknown property '.\get_class($this).'::$'.$name);
    }
    
    /**
     * Clears all buckets and the queue.
     * @return void
     */
    function clear() {
        $this->limited = true;
        $this->resetTime = \INF;
        
        while($item = \array_shift($this->queue)) {
            unset($item);
        }
        
        while($bucket = \array_shift($this->ratelimits)) {
            unset($bucket);
        }
        
        $this->limited = false;
        $this->resetTime = 0;
    }
    
    /**
     * Makes an API request.
     * @param string  $method
     * @param string  $endpoint
     * @param array   $options
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function makeRequest(string $method, string $endpoint, array $options) {
        $request = new \CharlotteDunois\Yasmin\HTTP\APIRequest($this, $method, $endpoint, $options);
        return $this->add($request);
    }
    
    /**
     * Adds an APIRequest to the queue.
     * @param \CharlotteDunois\Yasmin\HTTP\APIRequest  $apirequest
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function add(\CharlotteDunois\Yasmin\HTTP\APIRequest $apirequest) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($apirequest) {
            $apirequest->deferred = new \React\Promise\Deferred();
            $apirequest->deferred->promise()->done($resolve, $reject);
            
            $endpoint = $this->getRatelimitEndpoint($apirequest);
            if(!empty($endpoint)) {
                $this->client->emit('debug', 'Adding request "'.$apirequest->getEndpoint().'" to ratelimit bucket');
                $bucket = $this->getRatelimitBucket($endpoint);
                
                $bucket->push($apirequest);
                $this->queue[] = $bucket;
            } else {
                $this->client->emit('debug', 'Adding request "'.$apirequest->getEndpoint().'" to global queue');
                $this->queue[] = $apirequest;
            }
            
            $this->processFuture();
        }));
    }
    
    /**
     * Unshifts an item into the queue.
     * @param \CharlotteDunois\Yasmin\HTTP\APIRequest|\CharlotteDunois\Yasmin\HTTP\RatelimitBucket  $item
     * @return void
     */
    function unshiftQueue($item) {
        \array_unshift($this->queue, $item);
    }
    
    /**
     * Gets the Gateway from the Discord API.
     * @param bool  $bot  Should we use the bot endpoint? Requires token.
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function getGateway(bool $bot = false) {
        return $this->makeRequest('GET', 'gateway'.($bot ? '/bot' : ''), array());
    }
    
    /**
     * Processes the queue on future tick.
     * @return void
     */
    protected function processFuture() {
        $this->loop->futureTick(function () {
            $this->process();
        });
    }
    
    /**
     * Processes the queue delayed, depends on rest time offset.
     * @return void
     */
    protected function processDelayed() {
        $offset = (float) $this->client->getOption('http.restTimeOffset', 0.0);
        if($offset > 0.0) {
            $this->client->addTimer($offset, function () {
                $this->process();
            });
            
            return;
        }
        
        $this->process();
    }
    
    /**
     * Processes the queue.
     * @return void
     */
    protected function process() {
        if($this->limited) {
            if(\microtime(true) < $this->resetTime) {
                $this->client->addTimer(($this->resetTime - \microtime(true)), function () {
                    $this->process();
                });
                
                return;
            }
            
            $this->limited = false;
            $this->remaining = ($this->limit ? $this->limit : \INF);
        }
        
        if(\count($this->queue) === 0) {
            return;
        }
        
        $item = \array_shift($this->queue);
        $this->processItem($item);
    }
    
    /**
     * Processes a queue item.
     * @param \CharlotteDunois\Yasmin\HTTP\APIRequest|\CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface|null  $item
     * @return void
     */
    protected function processItem($item) {
        if($item instanceof \CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface) {
            if($item->isBusy()) {
                $this->queue[] = $item;
                
                foreach($this->queue as $qitem) {
                    if(!($qitem instanceof \CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface) || !$qitem->isBusy()) {
                        $this->processItem($qitem);
                        return;
                    }
                }
                
                return;
            }
            
            $item->setBusy(true);
            $buckItem = $this->extractFromBucket($item);
            
            if(!($buckItem instanceof \React\Promise\ExtendedPromiseInterface)) {
                $buckItem = \React\Promise\resolve($buckItem);
            }
            
            $buckItem->done(function ($req) use ($item) {
                $item->setBusy(false);
                
                if(!($req instanceof \CharlotteDunois\Yasmin\HTTP\APIRequest)) {
                    return;
                }
                
                $this->execute($req);
            }, array($this->client, 'handlePromiseRejection'));
        } else {
            if(!($item instanceof \CharlotteDunois\Yasmin\HTTP\APIRequest)) {
                return;
            }
            
            $this->execute($item);
        }
    }
    
    /**
     * Extracts an item from a ratelimit bucket.
     * @param \CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface  $item
     * @return \CharlotteDunois\Yasmin\HTTP\APIRequest|bool|\React\Promise\ExtendedPromiseInterface
     */
    protected function extractFromBucket(\CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface $item) {
        if($item->size() > 0) {
            $meta = $item->getMeta();
            
            if($meta instanceof \React\Promise\ExtendedPromiseInterface) {
                /** @noinspection PhpIncompatibleReturnTypeInspection */
                return $meta->then(function ($data) use (&$item) {
                    if(!$data['limited']) {
                        $this->client->emit('debug', 'Retrieved item from bucket "'.$item->getEndpoint().'"');
                        return $item->shift();
                    }
                    
                    $this->queue[] = $item;
                    
                    $this->client->addTimer(($data['resetTime'] - \microtime(true)), function () {
                        $this->process();
                    });
                }, function ($error) use (&$item) {
                    $this->queue[] = $item;
                    $this->client->emit('error', $error);
                    
                    $this->process();
                    return false;
                });
            } else {
                if(!$meta['limited']) {
                    $this->client->emit('debug', 'Retrieved item from bucket "'.$item->getEndpoint().'"');
                    return $item->shift();
                }
                
                $this->queue[] = $item;
                
                $this->client->addTimer(($meta['resetTime'] - \microtime(true)), function () {
                    $this->process();
                });
            }
        }
        
        return false;
    }
    
    /**
     * Executes an API Request.
     * @param \CharlotteDunois\Yasmin\HTTP\APIRequest  $item
     * @return void
     */
    protected function execute(\CharlotteDunois\Yasmin\HTTP\APIRequest $item) {
        $endpoint = $this->getRatelimitEndpoint($item);
        $ratelimit = null;
        
        if(!empty($endpoint)) {
            $ratelimit = $this->getRatelimitBucket($endpoint);
            $ratelimit->setBusy(true);
        }
        
        $this->client->emit('debug', 'Executing item "'.$item->getEndpoint().'"');
        
        $item->execute($ratelimit)->then(function ($data) use ($item) {
            if($data === 0) {
                $item->deferred->resolve();
            } elseif($data !== -1) {
                $item->deferred->resolve($data);
            }
        }, function ($error) use ($item) {
            $this->client->emit('debug', 'Request for item "'.$item->getEndpoint().'" failed with '.($error instanceof \Throwable ? 'exception '.\get_class($error) : 'error '.$error));
            $item->deferred->reject($error);
        })->then(null, function ($error) {
            $this->client->handlePromiseRejection($error);
        })->done(function () use ($ratelimit, $endpoint) {
            if($ratelimit instanceof \CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface) {
                if(isset($this->bucketRatelimitPromises[$endpoint])) {
                    $this->bucketRatelimitPromises[$endpoint]->done(function () use ($ratelimit) {
                        $ratelimit->setBusy(false);
                        $this->processDelayed();
                    });
                } else {
                    $ratelimit->setBusy(false);
                    $this->processDelayed();
                }
            } else {
                $this->processDelayed();
            }
        });
    }
    
    /**
     * Turns an endpoint path to the ratelimit path.
     * @param \CharlotteDunois\Yasmin\HTTP\APIRequest  $request
     * @return string
     */
    function getRatelimitEndpoint(\CharlotteDunois\Yasmin\HTTP\APIRequest $request) {
        $endpoint = $request->getEndpoint();
        
        if($request->isReactionEndpoint()) {
            \preg_match('/channels\/(\d+)\/messages\/(\d+)\/reactions\/.*/', $endpoint, $matches);
            return 'channels/'.$matches[1].'/messages/'.$matches[2].'/reactions';
        }
        
        $firstPart = \substr($endpoint, 0, (\strpos($endpoint, '/') ?: \strlen($endpoint)));
        $majorRoutes = array('channels', 'guilds', 'webhooks');
        
        if(!\in_array($firstPart, $majorRoutes, true)) {
            return $firstPart;
        }
        
        \preg_match('/((?:.*?)\/(?:\d+))(?:\/messages\/((?:bulk(?:-|_)delete)|(?:\d+)){0,1})?/', $endpoint, $matches);
        
        if(\is_numeric(($matches[2] ?? null)) && $request->getMethod() === 'DELETE') {
            return 'delete@'.$matches[0];
        } elseif(\stripos(($matches[2] ?? ''), 'bulk') !== false) {
            return $matches[0];
        }
        
        return ($matches[1] ?? $endpoint);
    }
    
    /**
     * Gets the ratelimit bucket for the specific endpoint.
     * @param string $endpoint
     * @return \CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface
     */
    protected function getRatelimitBucket(string $endpoint) {
        if(empty($this->ratelimits[$endpoint])) {
            $bucket = $this->bucketName;
            $this->ratelimits[$endpoint] = new $bucket($this, $endpoint);
        }
        
        return $this->ratelimits[$endpoint];
    }
    
    /**
     * Extracts ratelimits from a response.
     * @param \Psr\Http\Message\ResponseInterface  $response
     * @return mixed[]
     * @throws \Throwable
     */
    function extractRatelimit(\Psr\Http\Message\ResponseInterface $response) {
        $limit = ($response->hasHeader('X-RateLimit-Limit') ? ((int) $response->getHeader('X-RateLimit-Limit')[0]) : null);
        $remaining = ($response->hasHeader('X-RateLimit-Remaining') ? ((int) $response->getHeader('X-RateLimit-Remaining')[0]) : null);
        $resetTime = $this->extractRatelimitResetTime($response);
        
        return \compact('limit', 'remaining', 'resetTime');
    }
    
    /**
     * Handles ratelimits.
     * @param \Psr\Http\Message\ResponseInterface                               $response
     * @param \CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface|null  $ratelimit
     * @param bool                                                              $isReactionEndpoint
     * @return void
     * @throws \Throwable
     */
    function handleRatelimit(\Psr\Http\Message\ResponseInterface $response, ?\CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface $ratelimit = null, bool $isReactionEndpoint = false) {
        $ctime = \microtime(true);
        [ 'limit' => $limit, 'remaining' => $remaining, 'resetTime' => $resetTime ] = $this->extractRatelimit($response);
        
        if($isReactionEndpoint && !empty($resetTime)) {
            $resetTime = (float) \bcadd($ctime, '0.25', 3);
        }
        
        $global = false;
        if($response->hasHeader('X-RateLimit-Global')) {
            $global = true;
            
            $this->limit = $limit ?? $this->limit;
            $this->remaining = $remaining ?? $this->remaining;
            $this->resetTime = $resetTime ?? $this->resetTime;
            
            if($this->remaining === 0 && $this->resetTime > $ctime) {
                $this->limited = true;
                $this->client->emit('debug', 'Global ratelimit encountered, continuing in '.($this->resetTime - $ctime).' seconds');
            } else {
                $this->limited = false;
            }
        } elseif($ratelimit !== null) {
            $set = $ratelimit->handleRatelimit($limit, $remaining, $resetTime);
            if($set instanceof \React\Promise\ExtendedPromiseInterface) {
                $this->bucketRatelimitPromises[$ratelimit->getEndpoint()] = $set;
            }
        }
        
        $this->loop->futureTick(function () use ($ratelimit, $global, $limit, $remaining, $resetTime) {
            $this->client->emit('ratelimit', array(
                'endpoint' => ($ratelimit !== null ? $ratelimit->getEndpoint() : 'global'),
                'global' => $global,
                'limit' => $limit,
                'remaining' => $remaining,
                'resetTime' => $resetTime
            ));
        });
    }
    
    /**
     * Returns the ratelimit reset time.
     * @param \Psr\Http\Message\ResponseInterface  $response
     * @return float|null
     * @throws \Throwable
     */
    protected function extractRatelimitResetTime(\Psr\Http\Message\ResponseInterface $response): ?float {
        if($response->hasHeader('Retry-After')) {
            $retry = (int) $response->getHeader('Retry-After')[0];
            $retryTime = \bcdiv($retry, 1000, 3);
            
            return ((float) \bcadd(\microtime(true), $retryTime, 3));
        } elseif($response->hasHeader('X-RateLimit-Reset')) {
            $date = (new \DateTime(($response->getHeader('Date')[0] ?? 'now')))->getTimestamp();
            $reset = $response->getHeader('X-RateLimit-Reset')[0];
            
            $resetTime = \bcsub($reset, $date, 3);
            return ((float) \bcadd(\microtime(true), $resetTime, 3));
        }
        
        return null;
    }
}
