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
 * @see https://discordapp.com/developers/docs/topics/gateway#guild-role-update
 * @internal
 */
class GuildRoleUpdate implements \CharlotteDunois\Yasmin\Interfaces\WSEventInterface {
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
        $this->clones = !($clones === true || \in_array('roleUpdate', (array) $clones));
    }
    
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $data): void {
        $guild = $this->client->guilds->get($data['guild_id']);
        if($guild) {
            $role = $guild->roles->get($data['role']['id']);
            if($role) {
                $oldRole = null;
                if($this->clones) {
                    $oldRole = clone $role;
                }
                
                $role->_patch($data['role']);
                $this->client->queuedEmit('roleUpdate', $role, $oldRole);
            } else {
                $role = $guild->roles->factory($data['role']);
                $this->client->queuedEmit('roleCreate', $role);
            }
        }
    }
}
