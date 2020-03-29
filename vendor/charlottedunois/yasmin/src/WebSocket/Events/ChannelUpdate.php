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
 * @see https://discordapp.com/developers/docs/topics/gateway#channel-update
 * @internal
 */
class ChannelUpdate implements \CharlotteDunois\Yasmin\Interfaces\WSEventInterface {
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
        $this->clones = !($clones === true || \in_array('channelUpdate', (array) $clones));
    }
    
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $data): void {
        $channel = $this->client->channels->get($data['id']);
        if($channel instanceof \CharlotteDunois\Yasmin\Interfaces\ChannelInterface) {
            $oldChannel = null;
            if($this->clones) {
                $oldChannel = clone $channel;
            }
            
            $channel->_patch($data);
            
            $prom = array();
            if($channel instanceof \CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface) {
                foreach($channel->getPermissionOverwrites() as $overwrite) {
                    if($overwrite->type === 'member' && $overwrite->target === null) {
                        $prom[] = $channel->getGuild()->fetchMember($overwrite->id)->then(function (\CharlotteDunois\Yasmin\Models\GuildMember $member) use ($overwrite) {
                            $overwrite->_patch(array('target' => $member));
                        }, function () {
                            // Do nothing
                        });
                    }
                }
            }
            
            \React\Promise\all($prom)->done(function () use ($channel, $oldChannel) {
                $this->client->queuedEmit('channelUpdate', $channel, $oldChannel);
            }, array($this->client, 'handlePromiseRejection'));
        }
    }
}
