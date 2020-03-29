<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\WebSocket\Handlers;

/**
 * WS Event handler
 * @internal
 */
class Hello implements \CharlotteDunois\Yasmin\Interfaces\WSHandlerInterface {
    /**
     * @var \CharlotteDunois\Yasmin\WebSocket\WSHandler
     */
    protected $wshandler;
    
    function __construct(\CharlotteDunois\Yasmin\WebSocket\WSHandler $wshandler) {
        $this->wshandler = $wshandler;
    }
    
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $packet): void {
        $this->wshandler->wsmanager->client->emit('debug', 'Shard '.$ws->shardID.' connected to Gateway');
        
        $this->wshandler->wsmanager->setLastIdentified(\time());
        $ws->sendIdentify();
        
        $interval = $packet['d']['heartbeat_interval'] / 1000;
        $ws->ratelimits['heartbeatRoom'] = (int) \ceil($ws->ratelimits['total'] / $interval);
        
        $ws->heartbeat = $this->wshandler->wsmanager->client->loop->addPeriodicTimer($interval, function () use (&$ws) {
            $ws->heartbeat();
        });
    }
}
