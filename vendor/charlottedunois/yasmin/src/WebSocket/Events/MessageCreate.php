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
 * @see https://discordapp.com/developers/docs/topics/gateway#message-create
 * @internal
 */
class MessageCreate implements \CharlotteDunois\Yasmin\Interfaces\WSEventInterface {
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
            $user = $this->client->users->patch($data['author']);
            
            if(!empty($data['member']) && $channel instanceof \CharlotteDunois\Yasmin\Models\TextChannel && !$channel->getGuild()->members->has($user->id)) {
                $member = $data['member'];
                $member['user'] = array('id' => $user->id);
                $channel->getGuild()->_addMember($member, true);
            }
            
            $message = $channel->_createMessage($data);
            
            if($message->guild && $message->mentions->users->count() > 0 && $message->mentions->users->count() > $message->mentions->members->count()) {
                $promise = array();
                
                foreach($message->mentions->users as $user) {
                    $promise[] = $message->guild->fetchMember($user->id)->then(function (\CharlotteDunois\Yasmin\Models\GuildMember $member) use ($message) {
                        $message->mentions->members->set($member->id, $member);
                    }, function () {
                        // Ignore failure
                    });
                }
                
                $prm = \React\Promise\all($promise);
            } else {
                $prm = \React\Promise\resolve();
            }
            
            $prm->done(function () use ($message) {
                if($message->guild && !($message->member instanceof \CharlotteDunois\Yasmin\Models\GuildMember) && !$message->author->webhook) {
                    return $message->guild->fetchMember($message->author->id)->then(null, function () {
                        // Ignore failure
                    });
                }
                
                $this->client->queuedEmit('message', $message);
            }, array($this->client, 'handlePromiseRejection'));
        }
    }
}
