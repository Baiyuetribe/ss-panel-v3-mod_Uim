<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\WebSocket\Events;

/**
 * WS Event
 * @see https://discordapp.com/developers/docs/topics/gateway#message-update
 * @internal
 */
class MessageUpdate implements \CharlotteDunois\Yasmin\Interfaces\WSEventInterface {
    /**
     * The client.
     * @var \CharlotteDunois\Yasmin\Client
     */
    protected $client;
    
    /**
     * Whether we do clones.
     * @var bool
     */
    protected $clones = false;
    
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\WebSocket\WSManager $wsmanager) {
        $this->client = $client;
        
        $clones = $this->client->getOption('disableClones', array());
        $this->clones = !($clones === true || \in_array('messageUpdate', (array) $clones));
    }
    
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $data): void {
        $channel = $this->client->channels->get($data['channel_id']);
        if($channel instanceof \CharlotteDunois\Yasmin\Interfaces\TextChannelInterface) {
            $message = $channel->getMessages()->get($data['id']);
            if($message instanceof \CharlotteDunois\Yasmin\Models\Message) {
                $oldMessage = null;
                if($this->clones) {
                    $oldMessage = clone $message;
                }
                
                $message->_patch($data);
                
                $this->client->queuedEmit('messageUpdate', $message, $oldMessage);
            } else {
                $this->client->queuedEmit('messageUpdateRaw', $channel, $data);
            }
        }
    }
}
