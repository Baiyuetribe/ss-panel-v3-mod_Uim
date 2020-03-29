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
 * Represents a single HTTP request.
 * @internal
 */
class APIRequest {
    /**
     * The JSON encode/decode options.
     * @var int|null
     */
    static protected $jsonOptions;
    
    /**
     * The API manager.
     * @var \CharlotteDunois\Yasmin\HTTP\APIManager
     */
    protected $api;
    
    /**
     * The url.
     * @var string
     */
    protected $url;
    
    /**
     * The used deferred.
     * @var \React\Promise\Deferred
     */
    public $deferred;
    
    /**
     * The request method.
     * @var string
     */
    private $method;
    
    /**
     * The endpoint.
     * @var string
     */
    private $endpoint;
    
    /**
     * How many times we've retried.
     * @var int
     */
    protected $retries = 0;
    
    /**
     * Any request options.
     * @var array
     */
    protected $options = array();
    
    /**
     * Creates a new API Request.
     * DO NOT initialize this class yourself.
     * @param \CharlotteDunois\Yasmin\HTTP\APIManager  $api
     * @param string                                   $method
     * @param string                                   $endpoint
     * @param array                                    $options
     */
    function __construct(\CharlotteDunois\Yasmin\HTTP\APIManager $api, string $method, string $endpoint, array $options) {
        $this->api = $api;
        $this->url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::HTTP['url'].'v'.\CharlotteDunois\Yasmin\HTTP\APIEndpoints::HTTP['version'].'/';
        
        $this->method = $method;
        $this->endpoint = \ltrim($endpoint, '/');
        $this->options = $options;
        
        if(self::$jsonOptions === null) {
            self::$jsonOptions = (\PHP_VERSION_ID >= 70300 ? \JSON_THROW_ON_ERROR : 0);
        }
    }
    
    /**
     * Returns the request method.
     * @return string
     */
    function getMethod() {
        return $this->method;
    }
    
    
    /**
     * Returns the endpoint path.
     * @return string
     */
    function getEndpoint() {
        return $this->endpoint;
    }
    
    /**
     * Returns whether this request is to a reaction endpoint.
     * @return bool
     */
    function isReactionEndpoint() {
        return !empty($this->options['reactionRatelimit']);
    }
    
    /**
     * Returns the Guzzle Request.
     * @return \Psr\Http\Message\RequestInterface
     */
    function request() {
        $url = $this->url.$this->endpoint;
        
        $options = array(
            'http_errors' => false,
            'headers' => array(
                'X-RateLimit-Precision' => 'millisecond',
                'User-Agent' => 'DiscordBot (https://github.com/CharlotteDunois/Yasmin, '.\CharlotteDunois\Yasmin\Client::VERSION.')'
            )
        );
        
        if(!empty($this->options['auth'])) {
            $options['headers']['Authorization'] = $this->options['auth'];
        } elseif(empty($this->options['noAuth']) && !empty($this->api->client->token)) {
            $options['headers']['Authorization'] = 'Bot '.$this->api->client->token;
        }
        
        if(!empty($this->options['files']) && \is_array($this->options['files'])) {
            $options['multipart'] = array();
            
            foreach($this->options['files'] as $file) {
                if(!isset($file['data']) && !isset($file['path'])) {
                    continue;
                }
                
                $field = ($file['field'] ?? 'file-'.\bin2hex(\random_bytes(3)));
                $options['multipart'][] = array(
                    'name' => $field,
                    'contents' => (isset($file['data']) ? $file['data'] : \fopen($file['path'], 'r')),
                    'filename' => (isset($file['name']) ? $file['name'] : (isset($file['path']) ? \basename($file['path']) : $field.'.jpg'))
                );
            }
            
            if(!empty($this->options['data'])) {
                $options['multipart'][] = array(
                    'name' => 'payload_json',
                    'contents' => \json_encode($this->options['data'], self::$jsonOptions)
                );
            }
        } elseif(!empty($this->options['data'])) {
            $options['json'] = $this->options['data'];
        }
        
        if(!empty($this->options['querystring'])) {
            $options['query'] = \http_build_query($this->options['querystring'], '', '&', \PHP_QUERY_RFC3986);
        }
        
        if(!empty($this->options['auditLogReason'])) {
            $options['headers']['X-Audit-Log-Reason'] = \rawurlencode(\trim($this->options['auditLogReason']));
        }
        
        $request = new \RingCentral\Psr7\Request($this->method, $url);
        $request->requestOptions = $options;
        
        return $request;
    }
    
    /**
     * Executes the request.
     * @param \CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface|null  $ratelimit
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function execute(?\CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface $ratelimit = null) {
        $request = $this->request();
        
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return \CharlotteDunois\Yasmin\Utils\URLHelpers::makeRequest($request, $request->requestOptions)
            ->then(function (?\Psr\Http\Message\ResponseInterface $response) use ($ratelimit) {
                if(!$response) {
                    return -1;
                }
                
                $status = $response->getStatusCode();
                $this->api->client->emit('debug', 'Got response for item "'.$this->endpoint.'" with HTTP status code '.$status);
                
                $this->api->handleRatelimit($response, $ratelimit, $this->isReactionEndpoint());
                
                if($status === 204) {
                    return 0;
                }
                
                $body = self::decodeBody($response);
                
                if($status >= 400) {
                    $error = $this->handleAPIError($response, $body, $ratelimit);
                    if($error === null) {
                        return -1;
                    }
                    
                    throw $error;
                }
                
                return $body;
            });
    }
    
    /**
     * Gets the response body from the response.
     * @param \Psr\Http\Message\ResponseInterface  $response
     * @return mixed
     * @throws \RuntimeException
     * @throws \JsonException
     */
    static function decodeBody(\Psr\Http\Message\ResponseInterface $response) {
        $body = (string) $response->getBody();
        
        $type = $response->getHeader('Content-Type')[0];
        if(\stripos($type, 'text/html') !== false) {
            throw new \RuntimeException('Invalid API response: HTML response body received');
        }
        
        $json = \json_decode($body, true, 512, self::$jsonOptions);
        if($json === null && \json_last_error() !== \JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid API response: '.\json_last_error_msg());
        }
        
        return $json;
    }
    
    /**
     * Handles an API error.
     * @param \Psr\Http\Message\ResponseInterface                               $response
     * @param mixed                                                             $body
     * @param \CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface|null  $ratelimit
     * @return \CharlotteDunois\Yasmin\HTTP\DiscordAPIException|\RuntimeException|null
     */
    protected function handleAPIError(\Psr\Http\Message\ResponseInterface $response, $body, ?\CharlotteDunois\Yasmin\Interfaces\RatelimitBucketInterface $ratelimit = null) {
        $status = $response->getStatusCode();
        
        if($status >= 500) {
            $this->retries++;
            $maxRetries = (int) $this->api->client->getOption('http.requestMaxRetries', 0);
            
            if($maxRetries > 0 && $this->retries > $maxRetries) {
                $this->api->client->emit('debug', 'Giving up on item "'.$this->endpoint.'" after '.$maxRetries.' retries due to HTTP '.$status);
                
                return (new \RuntimeException('Maximum retry of '.$maxRetries.' reached - giving up'));
            }
            
            $this->api->client->emit('debug', 'Delaying unshifting item "'.$this->endpoint.'" due to HTTP '.$status);
            
            $delay = (int) $this->api->client->getOption('http.requestErrorDelay', 30);
            if($this->retries > 2) {
                $delay *= 2;
            }
            
            $this->api->client->addTimer($delay, function () use (&$ratelimit) {
                if($ratelimit !== null) {
                    $this->api->unshiftQueue($ratelimit->unshift($this));
                } else {
                    $this->api->unshiftQueue($this);
                }
            });
            
            return null;
        } elseif($status === 429) {
            $this->api->client->emit('debug', 'Unshifting item "'.$this->endpoint.'" due to HTTP 429');
            
            if($ratelimit !== null) {
                $this->api->unshiftQueue($ratelimit->unshift($this));
            } else {
                $this->api->unshiftQueue($this);
            }
            
            return null;
        }
        
        if($status >= 400 && $status < 500) {
            $error = new \CharlotteDunois\Yasmin\HTTP\DiscordAPIException($this->endpoint, $body);
        } else {
            $error = new \RuntimeException($response->getReasonPhrase());
        }
        
        return $error;
    }
}
