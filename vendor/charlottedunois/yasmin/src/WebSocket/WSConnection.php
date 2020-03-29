<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\WebSocket;

/**
 * Handles the WS connection.
 *
 * @property \CharlotteDunois\Yasmin\WebSocket\WSManager             $wsmanager
 * @property \CharlotteDunois\Yasmin\Interfaces\WSEncodingInterface  $encoding
 * @property int[]                                                   $pings
 * @property bool                                                    $ready
 * @property int                                                     $shardID
 * @property int                                                     $status
 *
 * @internal
 */
class WSConnection implements \CharlotteDunois\Events\EventEmitterInterface {
    use \CharlotteDunois\Events\EventEmitterTrait;
    
    /**
     * @var \CharlotteDunois\Yasmin\WebSocket\WSManager
     */
    protected $wsmanager;
    
    /**
     * @var int
     */
    protected $shardID;
    
    /**
     * @var \Ratchet\Client\WebSocket
     */
    protected $ws;
    
    /**
     * @var \CharlotteDunois\Yasmin\Interfaces\WSCompressionInterface
     */
    protected $compressContext;
    
    /**
     * The WS ratelimits.
     * @var array
     */
    public $ratelimits = array(
        'total' => 120,
        'time' => 60,
        'remaining' => 120,
        'timer' => null,
        'dateline' => 0,
        'heartbeatRoom' => 2
    );
    
    /**
     * @var \React\EventLoop\TimerInterface
     */
    public $heartbeat = null;
    
    /**
     * The WS heartbeat.
     * @var array
     */
    public $wsHeartbeat = array(
        'ack' => true,
        'dateline' => 0
    );
    
    /**
     * The WS authentication state.
     * @var bool
     */
    protected $authenticated = false;
    
    /**
     * The WS queue.
     * @var array
     */
    protected $queue = array();
    
    /**
     * The WS queue processing status.
     * @var bool
     */
    protected $running = false;
    
    /**
     * If the connection gets closed, did we expect it?
     * @var bool
     */
    protected $expectedClose = false;
    
    /**
     * Whether we should use the previous sequence for RESUME (for after compress context failure).
     * @var bool
     */
    protected $previous = false;
    
    /**
     * Whether we are ready.
     * @var bool
     */
    protected $ready = false;
    
    /**
     * The timestamp of when we received the last event.
     * @var int
     */
    protected $lastPacketTime;
    
    /**
     * The previous sequence.
     * @var mixed|null
     */
    protected $previousSequence = null;
    
    /**
     * The sequence.
     * @var mixed
     */
    protected $sequence = null;
    
    /**
     * The WS pings in ms.
     * @var int[]
     */
    protected $pings = array();
    
    
    /**
     * WS close codes, sorted by resumable session and ends everything.
     * @var array
     */
    protected $wsCloseCodes = array(
        'end' => array(
            4004, 4010, 4011, 4012
        ),
        'resumable' => array(
            4001, 4002, 4003, 4005, 4008
        )
    );
    
    /**
     * The WS connection status
     * @var int
     */
    protected $status = \CharlotteDunois\Yasmin\Client::WS_STATUS_DISCONNECTED;
    
    /**
     * The Discord Session ID.
     * @var string|null
     */
    protected $wsSessionID;
    
    /**
     * DO NOT initialize this class yourself.
     * @param \CharlotteDunois\Yasmin\WebSocket\WSManager  $wsmanager
     * @param int                                          $shardID
     * @param string                                       $compression
     */
    function __construct(\CharlotteDunois\Yasmin\WebSocket\WSManager $wsmanager, int $shardID, string $compression) {
        $this->wsmanager = $wsmanager;
        $this->shardID = $shardID;
        $this->compressContext = new $compression();
        
        $this->on('self.ws.ready', function () {
            $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_CONNECTED;
        });
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
     */
    function __get($name) {
        if(\property_exists($this, $name)) {
            return $this->$name;
        }
        
        throw new \RuntimeException('Undefined property: '.\get_class($this).'::$'.$name);
    }
    
    /**
     * Disconnects.
     * @return void
     */
    function destroy() {
        $this->disconnect();
    }
    
    /**
     * Connects to the gateway url. Resolves with $this.
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \RuntimeException
     */
    function connect(bool $reconnect = false) {
        if($this->ws) {
            return \React\Promise\resolve();
        }
        
        if(!$this->wsmanager->gateway) {
            throw new \RuntimeException('Unable to connect to unknown gateway');
        }
        
        if(($this->wsmanager->lastIdentify ?? 0) > (\time() - 5)) {
            return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
                $this->wsmanager->client->addTimer((5 - (\time() - $this->wsmanager->lastIdentify)), function () use ($resolve, $reject) {
                    $this->connect()->done($resolve, $reject);
                });
            }));
        }
        
        $compress = \explode('\\', \get_class($this->compressContext));
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' using compress context '.\array_pop($compress));
        $compress = null;
        
        $ready = false;
        $this->ready = false;
        
        $this->expectedClose = false;
        
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' connecting to WS '.$this->wsmanager->gateway);
        
        if($this->status < \CharlotteDunois\Yasmin\Client::WS_STATUS_CONNECTING || $this->status > \CharlotteDunois\Yasmin\Client::WS_STATUS_RECONNECTING) {
            $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_CONNECTING;
        }
        
        $deferred = new \React\Promise\Deferred();
        
        $connector = $this->wsmanager->connector;
        $connector($this->wsmanager->gateway)->done(function (\Ratchet\Client\WebSocket $conn) use (&$ready, $deferred, $reconnect) {
            $this->initWS($conn, $ready, $reconnect, $deferred);
        }, function (\Throwable $error) use ($deferred) {
            $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_DISCONNECTED;
            $this->wsmanager->client->emit('error', $error);
            
            if($this->ws) {
                $this->ws->close(1006);
            }
            
            $this->renewConnection(true)->done(function () use ($deferred) {
                $deferred->resolve($this);
            }, function (\Throwable $e) use ($deferred) {
                $deferred->reject($e);
            });
        });
        
        return $deferred->promise();
    }
    
    /**
     * Closes the WS connection.
     * @return void
     */
    function disconnect(int $code = 1000, string $reason = '') {
        if(!$this->ws) {
            return;
        }
        
        $this->processQueue();
        
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' disconnecting from WS');
        
        $this->expectedClose = true;
        $this->ws->close($code, $reason);
    }
    
    /**
     * Closes the WS connection.
     * @return void
     */
    function reconnect(bool $resumable = true) {
        if(!$this->ws) {
            return;
        }
        
        if(!$resumable) {
            $this->wsSessionID = null;
        }
        
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' disconnecting from WS in order to reconnect');
        $this->ws->close(4000, 'Reconnect required');
    }
    
    /**
     * Closes the WS connection.
     * @return \React\Promise\ExtendedPromiseInterface
     */
    protected function renewConnection(bool $forceNewGateway = true) {
        if($forceNewGateway) {
            $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_CONNECTING; // distinguish between HTTP call and WS reconnect
            
            $prom = $this->wsmanager->client->apimanager()->getGateway()->then(function ($url) {
                $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_RECONNECTING;
                return $this->wsmanager->connectShard($this->shardID, $url['url'], $this->wsmanager->gatewayQS);
            });
        } else {
            $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_RECONNECTING;
            $prom = $this->wsmanager->connectShard($this->shardID);
        }
        
        return $prom->then(null, function ($error) use ($forceNewGateway) {
            $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_DISCONNECTED;
            
            if($error instanceof \Throwable) {
                $error = \str_replace(array("\r", "\n"), '', $error->getMessage());
            }
            
            $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' errored ('.$error.') on making new login after failed connection attempt... retrying in 30 seconds');
            
            return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($forceNewGateway) {
                $this->wsmanager->client->addTimer(30, function () use ($forceNewGateway, $resolve, $reject) {
                    $this->renewConnection($forceNewGateway)->done($resolve, $reject);
                });
            }));
        });
    }
    
    /**
     * @param array $packet
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \RuntimeException
     */
    function send(array $packet) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($packet) {
            if($this->status !== \CharlotteDunois\Yasmin\Client::WS_STATUS_NEARLY && $this->status !== \CharlotteDunois\Yasmin\Client::WS_STATUS_CONNECTED) {
                return $reject(new \RuntimeException('Unable to send WS message before a WS connection is established'));
            }
            
            $this->queue[] = array('packet' => $packet, 'resolve' => $resolve, 'reject' => $reject);
            
            if(!$this->running) {
                $this->processQueue();
            }
        }));
    }
    
    /**
     * Processes the WS queue.
     * @return void
     */
    function processQueue() {
         if($this->running) {
             return;
         } elseif($this->ratelimits['remaining'] === 0) {
             return;
         } elseif(\count($this->queue) === 0) {
             return;
         }
         
         $this->running = true;
         
         while($this->ratelimits['remaining'] > 0 && \count($this->queue) > 0) {
             $element = \array_shift($this->queue);
             $this->ratelimits['remaining']--;
             
             if(!$this->ws) {
                 $element['reject'](new \RuntimeException('No WS connection'));
                 break;
             }
             
             $this->_send($element['packet']);
             $element['resolve']();
         }
         
         $this->running = false;
    }
    
    /**
     * Set authenticated.
     * @return void
     */
    function setAuthenticated(bool $state) {
        $this->authenticated = $state;
    }
    
    /**
     * Get the session ID.
     * @return string|null
     */
    function getSessionID() {
        return $this->wsSessionID;
    }
    
    /**
     * Set the session ID.
     * @return void
     */
    function setSessionID(?string $id) {
        $this->wsSessionID = $id;
    }
    
    /**
     * Sets the sequence.
     * @return void
     */
    function setSequence($sequence) {
        $this->previousSequence = $this->sequence;
        $this->sequence = $sequence;
    }
    
    /**
     * Sends an IDENTIFY or RESUME payload, depending on ws session ID.
     * @return void
     */
    function sendIdentify() {
        $this->authenticated = false;
        
        $op = \CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['IDENTIFY'];
        if(empty($this->wsSessionID)) {
            $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' sending IDENTIFY packet to WS');
        } else {
            $op = \CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['RESUME'];
            $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' sending RESUME packet to WS');
        }
        
        $packet = array(
            'op' => $op,
            'd' => array(
                'token' => $this->wsmanager->client->token,
                'properties' => array(
                    '$os' => \php_uname('s'),
                    '$browser' => 'Yasmin '.\CharlotteDunois\Yasmin\Client::VERSION,
                    '$device' => 'Yasmin '.\CharlotteDunois\Yasmin\Client::VERSION
                ),
                'compress' => $this->compressContext->isPayloadCompression(),
                'large_threshold' => ((int) $this->wsmanager->client->getOption('ws.largeThreshold', 250)),
                'shard' => array(
                    $this->shardID,
                    ((int) $this->wsmanager->client->getOption('shardCount', 1))
                )
            )
        );
        
        $presence = (array) $this->wsmanager->client->getOption('ws.presence', array());
        if(\is_array($presence) && !empty($presence)) {
            $packet['d']['presence'] = $presence;
        }
        
        if($op === \CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['RESUME']) {
            $packet['d']['session_id'] = $this->wsSessionID;
            $packet['d']['seq'] = ($this->previous && $this->previousSequence !== null ? $this->previousSequence : $this->sequence);
        }
        
        $this->_send($packet);
    }
    
    /**
     * Sends a heartbeat.
     * @return void
     */
    function heartbeat() {
        if(!$this->wsHeartbeat['ack']) {
            return $this->heartFailure();
        }
        
        if(!$this->authenticated) {
            return; // Do not heartbeat if unauthenticated
        }
        
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' sending WS heartbeat at sequence '.$this->sequence);
        
        $this->wsHeartbeat['ack'] = false;
        $this->wsHeartbeat['dateline'] = microtime(true);
        
        $this->_send(array(
            'op' => \CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['HEARTBEAT'],
            'd' => $this->sequence
        ));
    }
    
    /**
     * Handles heart failures.
     * @return void
     */
    function heartFailure() {
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' has WS heart failure');
        $this->disconnect(1006, 'No heartbeat ack');
    }
    
    /**
     * Handles heartbeat ack.
     * @return void
     */
    function _pong($end) {
        $time = \ceil(($end - $this->wsHeartbeat['dateline']) * 1000);
        $this->pings[] = (int) $time;
        
        $pings = \count($this->pings);
        if($pings > 3) {
            $this->pings = \array_slice($this->pings, ($pings - 3));
        }
        
        $this->wsHeartbeat['ack'] = true;
        $this->wsHeartbeat['dateline'] = 0;
        
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' received WS heartbeat ACK');
    }
    
    /**
     * Direct ws send method. DO NOT USE.
     * @return void
     */
    function _send(array $packet) {
        if(!$this->ws) {
            $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' tried sending a WS packet with no WS connection');
            return;
        }
        
        $data = $this->wsmanager->encoding->encode($packet);
        $msg = $this->wsmanager->encoding->prepareMessage($data);
        
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' sending WS packet with OP code '.$packet['op']);
        $this->ws->send($msg);
    }
    
    /**
     * Initializes the websocet.
     * @return void
     */
    protected function initWS(\Ratchet\Client\WebSocket $conn, bool &$ready, bool $reconnect, \React\Promise\Deferred $deferred) {
        $this->ws = $conn;
        
        $this->compressContext->init();
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' initialized compress context for shard ');
        
        $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_NEARLY;
        
        $this->emit('open');
        $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' connected to WS');
        
        $ratelimits = &$this->ratelimits;
        $ratelimits['timer'] = $this->wsmanager->client->loop->addPeriodicTimer($ratelimits['time'], function () use ($ratelimits) {
            $ratelimits['remaining'] = $ratelimits['total'] - $ratelimits['heartbeatRoom']; // Let room in WS ratelimit for X heartbeats per X seconds.
        });
        
        $this->once('self.ready', $this->initWSSelfReady($ready, $reconnect, $deferred));
        $this->once('self.error', $this->initWSSelfError($ready, $deferred));
        
        $this->ws->on('message', $this->initWSMessage($ready, $deferred));
        $this->ws->on('error', $this->initWSError($ready, $deferred));
        $this->ws->on('close', $this->initWSClose($deferred));
    }
    
    /**
     * Returns the handler for `self.ready` event.
     * @param bool                     $ready
     * @param bool                     $reconnect
     * @param \React\Promise\Deferred  $deferred
     * @return \Closure
     */
    protected function initWSSelfReady(bool &$ready, bool $reconnect, \React\Promise\Deferred &$deferred) {
        return (function () use (&$ready, $reconnect, $deferred) {
            $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_CONNECTED;
            
            if($reconnect && $this->wsmanager->client->user !== null) {
                $this->wsmanager->client->user->setPresence($this->wsmanager->client->user->clientPresence);
            }
            
            $ready = true;
            $this->ready = true;
            
            $deferred->resolve($this);
        });
    }
    
    /**
     * Returns the handler for `self.error` event.
     * @param bool                     $ready
     * @param \React\Promise\Deferred  $deferred
     * @return \Closure
     */
    protected function initWSSelfError(bool &$ready, \React\Promise\Deferred &$deferred) {
        return (function ($error) use (&$ready, $deferred) {
            if(!$ready) {
                $this->disconnect();
                $deferred->reject(new \Exception($error));
            }
        });
    }
    
    /**
     * Returns the handler for `message` event.
     * @param bool                     $ready
     * @param \React\Promise\Deferred  $deferred
     * @return \Closure
     */
    protected function initWSMessage(bool &$ready, \React\Promise\Deferred &$deferred) {
        return (function (\Ratchet\RFC6455\Messaging\Message $message) use (&$ready, $deferred) {
            $message = $message->getPayload();
            if(!$message) {
                return;
            }
            
            try {
                $message = $this->compressContext->decompress($message);
                
                if($this->previous) {
                    $this->previous = false;
                }
            } catch (\CharlotteDunois\Yasmin\DiscordException $e) {
                $this->previous = true;
                $this->wsmanager->client->emit('error', $e);
                
                if(!$ready) {
                    return $deferred->reject($e);
                }
                
                $this->ws->close(4000, 'Zlib decompression error');
                return;
            }
            
            $this->lastPacketTime = \microtime(true);
            
            try {
                $this->wsmanager->wshandler->handle($this, $message);
            } catch (\CharlotteDunois\Yasmin\DiscordException $e) {
                $this->wsmanager->client->emit('error', $e);
            }
        });
    }
    
    /**
     * Returns the handler for `error` event.
     * @param bool                     $ready
     * @param \React\Promise\Deferred  $deferred
     * @return \Closure
     */
    protected function initWSError(bool &$ready, \React\Promise\Deferred &$deferred) {
        return (function (\Throwable $error) use (&$ready, $deferred) {
            if(!$ready) {
                return $deferred->reject($error);
            }
            
            $this->wsmanager->client->emit('error', $error);
        });
    }
    
    /**
     * Returns the handler for `close` event.
     * @param \React\Promise\Deferred  $deferred
     * @return \Closure
     */
    protected function initWSClose(\React\Promise\Deferred &$deferred) {
        return (function (int $code, string $reason) use ($deferred) {
            if($this->ws !== null) {
                $this->ws->removeAllListeners();
            }
            
            if($this->ratelimits['timer']) {
                $this->wsmanager->client->loop->cancelTimer($this->ratelimits['timer']);
            }
            
            if($this->heartbeat) {
                $this->wsmanager->client->loop->cancelTimer($this->heartbeat);
                $this->heartbeat = null;
            }
            
            $this->ratelimits['remaining'] = $this->ratelimits['total'] - $this->ratelimits['heartbeatRoom'];
            $this->ratelimits['timer'] = null;
            
            $this->authenticated = false;
            $this->wsHeartbeat['ack'] = true;
            
            $this->compressContext->destroy();
            $this->wsmanager->client->emit('debug', 'Shard '.$this->shardID.' destroyed compress context');
            
            $this->ws = null;
            
            if($this->status <= \CharlotteDunois\Yasmin\Client::WS_STATUS_CONNECTED) {
                $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_DISCONNECTED;
            }
            
            $this->emit('close', $code, $reason);
            
            if(\in_array($code, $this->wsCloseCodes['end'])) {
                return $deferred->reject(new \RuntimeException(\CharlotteDunois\Yasmin\WebSocket\WSManager::WS_CLOSE_CODES[$code]));
            }
            
            if($code === 1000 && $this->expectedClose) {
                $this->queue = array();
                
                $this->wsSessionID = null;
                $this->status = \CharlotteDunois\Yasmin\Client::WS_STATUS_IDLE;
                
                return;
            }
            
            if($code === 1000 || ($code >= 4000 && !\in_array($code, $this->wsCloseCodes['resumable']))) {
                $this->wsSessionID = null;
            }
            
            $this->renewConnection(false);
        });
    }
}
