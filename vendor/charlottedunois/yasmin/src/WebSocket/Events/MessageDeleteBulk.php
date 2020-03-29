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
 * @see https://discordapp.com/developers/docs/topics/gateway#message-delete-bulk
 * @internal
 */
class MessageDeleteBulk implements \CharlotteDunois\Yasmin\Interfaces\WSEventInterface {
    /**
     * The client.
     * @var \CharlotteDunois\Yasmin\Client
     */
    protected $client;
    
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\WebSocket\WSManager $wsmanager) {
        $this->client = $client;
    }
    
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $data): void {
        $channel = $this->client->channels->get($data['channel_id']);
        if($channel instanceof \CharlotteDunois\Yasmin\Interfaces\TextChannelInterface) {
            $messages = new \CharlotteDunois\Collect\Collection();
            $messagesRaw = array();
            
            foreach($data['ids'] as $id) {
                $message = $channel->getMessages()->get($id);
                if($message instanceof \CharlotteDunois\Yasmin\Models\Message) {
                    $channel->getMessages()->delete($message->id);
                    $messages->set($message->id, $message);
                } else {
                    $messagesRaw[] = $id;
                }
            }
            
            if($messages->count() > 0) {
                $this->client->queuedEmit('messageDeleteBulk', $messages);
            }
            
            if(\count($messagesRaw) > 0) {
                $this->client->queuedEmit('messageDeleteBulkRaw', $channel, $messagesRaw);
            }
        }
    }
}
