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
 * @see https://discordapp.com/developers/docs/topics/gateway#guild-delete
 * @internal
 */
class GuildDelete implements \CharlotteDunois\Yasmin\Interfaces\WSEventInterface {
    /**
     * The client.
     * @var \CharlotteDunois\Yasmin\Client
     */
    protected $client;
    
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\WebSocket\WSManager $wsmanager) {
        $this->client = $client;
    }
    
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $data): void {
        $guild = $this->client->guilds->get($data['id']);
        if($guild) {
            foreach($guild->channels as $channel) {
                if($channel instanceof \CharlotteDunois\Yasmin\Interfaces\TextChannelInterface) {
                    $channel->stopTyping(true);
                }
            }
            
            if(!empty($data['unavailable'])) {
                $guild->_patch(array('unavailable' => true));
                $this->client->queuedEmit('guildUnavailable', $guild);
            } else {
                foreach($guild->channels as $channel) {
                    $this->client->channels->delete($channel->getId());
                }
                
                foreach($guild->emojis as $emoji) {
                    $this->client->emojis->delete($emoji->id);
                }
                
                $this->client->guilds->delete($guild->id);
                $this->client->queuedEmit('guildDelete', $guild);
            }
        }
    }
}
