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
 * @see https://discordapp.com/developers/docs/topics/gateway#presence-update
 * @internal
 */
class PresenceUpdate implements \CharlotteDunois\Yasmin\Interfaces\WSEventInterface {
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
    
    /**
     * Whether we ignore events from unknown users.
     * @var bool
     */
    protected $ignoreUnknown = false;
    
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\WebSocket\WSManager $wsmanager) {
        $this->client = $client;
        
        $clones = $this->client->getOption('disableClones', array());
        $this->clones = !($clones === true || \in_array('presenceUpdate', (array) $clones));
        $this->ignoreUnknown = (bool) $this->client->getOption('ws.presenceUpdate.ignoreUnknownUsers', false);
    }
    
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $data): void {
        $user = $this->client->users->get($data['user']['id']);
        
        if(($data['status'] ?? null) === 'offline' && $user === null) {
            return;
        }
        
        if($user === null) {
            if($this->ignoreUnknown) {
                return;
            }
            
            $user = $this->client->fetchUser($data['user']['id']);
        } else {
            if(\count($data['user']) > 1 && $user->_shouldUpdate($data['user'])) {
                $oldUser = null;
                if($this->clones) {
                    $oldUser = clone $user;
                }
                
                $user->_patch($data['user']);
                
                $this->client->queuedEmit('userUpdate', $user, $oldUser);
                return;
            }
            
            $user = \React\Promise\resolve($user);
        }
        
        $user->done(function (\CharlotteDunois\Yasmin\Models\User $user) use ($data) {
            $guild = $this->client->guilds->get($data['guild_id']);
            if($guild) {
                $presence = $guild->presences->get($user->id);
                $oldPresence = null;
                
                if($presence) {
                    if($data['status'] === 'offline' && $presence->status === 'offline') {
                        return;
                    }
                    
                    if($this->clones) {
                        $oldPresence = clone $presence;
                    }
                    
                    $presence->_patch($data);
                } else {
                    $presence = $guild->presences->factory($data);
                }
                
                $this->client->queuedEmit('presenceUpdate', $presence, $oldPresence);
            }
        }, array($this->client, 'handlePromiseRejection'));
    }
}
