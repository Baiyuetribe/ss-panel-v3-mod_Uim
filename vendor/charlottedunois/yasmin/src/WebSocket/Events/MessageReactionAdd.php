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
 * @see https://discordapp.com/developers/docs/topics/gateway#message-reaction-add
 * @internal
 */
class MessageReactionAdd implements \CharlotteDunois\Yasmin\Interfaces\WSEventInterface {
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
            $message = $channel->getMessages()->get($data['message_id']);
            $reaction = null;
            
            if($message) {
                $reaction = $message->_addReaction($data);
                $message = \React\Promise\resolve($message);
            } else {
                $message = $channel->fetchMessage($data['message_id']);
            }
            
            $message->done(function (\CharlotteDunois\Yasmin\Models\Message $message) use ($data, $reaction) {
                if($reaction === null) {
                    $id = (!empty($data['emoji']['id']) ? ((string) $data['emoji']['id']) : $data['emoji']['name']);
                    $reaction = $message->reactions->get($id);
                }
                
                $this->client->fetchUser($data['user_id'])->done(function (\CharlotteDunois\Yasmin\Models\User $user) use ($reaction) {
                    $reaction->users->set($user->id, $user);
                    $this->client->queuedEmit('messageReactionAdd', $reaction, $user);
                }, array($this->client, 'handlePromiseRejection'));
            }, function () {
                // Don't handle it
            });
        }
    }
}
