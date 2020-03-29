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
 * @see https://discordapp.com/developers/docs/topics/gateway#typing-start
 * @internal
 */
class TypingStart implements \CharlotteDunois\Yasmin\Interfaces\WSEventInterface {
    /**
     * The client.
     * @var \CharlotteDunois\Yasmin\Client
     */
    protected $client;
    
    /**
     * Whether we saw the client going ready.
     * @var bool
     */
    protected $ready = false;
    
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\WebSocket\WSManager $wsmanager) {
        $this->client = $client;
        
        $this->client->once('ready', function () {
            $this->ready = true;
        });
    }
    
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $data): void {
        if(!$this->ready) {
            return;
        }
        
        $channel = $this->client->channels->get($data['channel_id']);
        if($channel instanceof \CharlotteDunois\Yasmin\Interfaces\TextChannelInterface) {
            $user = $this->client->users->get($data['user_id']);
            if(!$user) {
                $user = $this->client->fetchUser($data['user_id']);
            } else {
                $user = \React\Promise\resolve($user);
            }
            
            $user->done(function (\CharlotteDunois\Yasmin\Models\User $user) use ($channel, $data) {
                if(!empty($data['member']) && $channel instanceof \CharlotteDunois\Yasmin\Models\TextChannel && !$channel->getGuild()->members->has($user->id)) {
                    $member = $data['member'];
                    $member['user'] = array('id' => $user->id);
                    $channel->getGuild()->_addMember($member, true);
                }
                
                if($channel->_updateTyping($user, $data['timestamp'])) {
                    $this->client->queuedEmit('typingStart', $channel, $user);
                }
            }, array($this->client, 'handlePromiseRejection'));
        }
    }
}
