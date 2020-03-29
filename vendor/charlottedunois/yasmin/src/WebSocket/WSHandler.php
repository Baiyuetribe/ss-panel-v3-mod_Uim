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
 * Handles WS messages.
 *
 * @property \CharlotteDunois\Yasmin\WebSocket\WSManager  $wsmanager
 * @internal
 */
class WSHandler {
    /**
     * The WS manager.
     * @var \CharlotteDunois\Yasmin\WebSocket\WSManager
     */
    protected $wsmanager;
    
    /**
     * The handlers for WS messages, mapped by name.
     * @var \CharlotteDunois\Yasmin\Interfaces\WSHandlerInterface[]
     */
    protected $handlers = array();
    
    /**
     * DO NOT initialize this class yourself.
     * @param \CharlotteDunois\Yasmin\WebSocket\WSManager  $wsmanager
     */
    function __construct(\CharlotteDunois\Yasmin\WebSocket\WSManager $wsmanager) {
        $this->wsmanager = $wsmanager;
        
        $this->register(\CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['DISPATCH'], \CharlotteDunois\Yasmin\WebSocket\Handlers\Dispatch::class);
        $this->register(\CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['HEARTBEAT'], \CharlotteDunois\Yasmin\WebSocket\Handlers\Heartbeat::class);
        $this->register(\CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['RECONNECT'], \CharlotteDunois\Yasmin\WebSocket\Handlers\Reconnect::class);
        $this->register(\CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['INVALID_SESSION'], \CharlotteDunois\Yasmin\WebSocket\Handlers\InvalidSession::class);
        $this->register(\CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['HELLO'], \CharlotteDunois\Yasmin\WebSocket\Handlers\Hello::class);
        $this->register(\CharlotteDunois\Yasmin\WebSocket\WSManager::OPCODES['HEARTBEAT_ACK'], \CharlotteDunois\Yasmin\WebSocket\Handlers\HeartbeatAck::class);
    }
    
    function __get($name) {
        if(\property_exists($this, $name)) {
            return $this->$name;
        }
        
        throw new \RuntimeException('Undefined property: '.\get_class($this).'::$'.$name);
    }
    
    /**
     * Returns a WS handler.
     * @return \CharlotteDunois\Yasmin\Interfaces\WSHandlerInterface
     */
    function getHandler(int $name) {
        if(isset($this->handlers[$name])) {
            return $this->handlers[$name];
        }
        
        throw new \Exception('Unable to find handler');
    }
    
    /**
     * Handles a message.
     * @return void
     */
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $message) {
        $packet = $this->wsmanager->encoding->decode($message);
        $this->wsmanager->client->emit('raw', $packet);
        
        if(isset($packet['s'])) {
            $ws->setSequence($packet['s']);
        }
        
        $this->wsmanager->emit('debug', 'Shard '.$ws->shardID.' received WS packet with OP code '.$packet['op']);
        
        if(isset($this->handlers[$packet['op']])) {
            $this->handlers[$packet['op']]->handle($ws, $packet);
        }
    }
    
    /**
     * Registers a handler.
     * @return void
     * @throws \RuntimeException
     */
    function register(int $op, string $class) {
        if(!\in_array('CharlotteDunois\Yasmin\Interfaces\WSHandlerInterface', \class_implements($class))) {
            throw new \RuntimeException('Specified handler class does not implement interface');
        }
        
        $this->handlers[$op] = new $class($this);
    }
}
