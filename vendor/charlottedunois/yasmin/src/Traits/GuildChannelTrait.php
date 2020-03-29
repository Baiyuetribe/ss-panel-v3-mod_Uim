<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Traits;

/**
 * The trait all guild channels use.
 */
trait GuildChannelTrait {
    /**
     * Creates an invite. Resolves with an instance of Invite.
     *
     * Options are as following (all are optional).
     *
     * ```
     * array(
     *    'maxAge' => int,
     *    'maxUses' => int, (0 = unlimited)
     *    'temporary' => bool,
     *    'unique' => bool
     * )
     * ```
     *
     * @param array $options
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Invite
     */
    function createInvite(array $options = array()) {
        $data = array(
            'max_uses' => ($options['maxUses'] ?? 0),
            'temporary' => ($options['temporary'] ?? false),
            'unique' => ($options['unique'] ?? false)
        );
        
        if(isset($options['maxAge'])) {
            $data['max_age'] = $options['maxAge'];
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($data) {
            $this->client->apimanager()->endpoints->channel->createChannelInvite($this->id, $data)->done(function ($data) use ($resolve) {
                $invite = new \CharlotteDunois\Yasmin\Models\Invite($this->client, $data);
                $resolve($invite);
            }, $reject);
        }));
    }
    
    /**
     * Clones a guild channel. Resolves with an instance of GuildChannelInterface.
     * @param string  $name             Optional name for the new channel, otherwise it has the name of this channel.
     * @param bool    $withPermissions  Whether to clone the channel with this channel's permission overwrites
     * @param bool    $withTopic        Whether to clone the channel with this channel's topic.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface
     */
    function clone(string $name = null, bool $withPermissions = true, bool $withTopic = true, string $reason = '') {
        $data = array(
            'name' => (!empty($name) ? ((string) $name) : $this->name),
            'type' => \CharlotteDunois\Yasmin\Models\ChannelStorage::getTypeForChannel($this)
        );
        
        if($withPermissions) {
            $data['permission_overwrites'] = \array_values($this->permissionOverwrites->all());
        }
        
        if($withTopic) {
            $data['topic'] = $this->topic;
        }
        
        if($this->parentID) {
            $data['parent_id'] = $this->parentID;
        }
        
        if($this instanceof \CharlotteDunois\Yasmin\Interfaces\VoiceChannelInterface) {
            $data['bitrate'] = $this->bitrate;
            $data['user_limit'] = $this->userLimit;
        } else {
            $data['nsfw'] = $this->nsfw;
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($data, $reason) {
            $this->client->apimanager()->endpoints->guild->createGuildChannel($this->guild->id, $data, $reason)->done(function ($data) use ($resolve) {
                $channel = $this->guild->channels->factory($data, $this->guild);
                $resolve($channel);
            }, $reject);
        }));
    }
     
    /**
     * Edits the channel. Resolves with $this.
     *
     * Options are as following (at least one is required).
     *
     * ```
     * array(
     *    'name' => string,
     *    'position' => int,
     *    'topic' => string, (text channels only)
     *    'nsfw' => bool, (text channels only)
     *    'bitrate' => int, (voice channels only)
     *    'userLimit' => int, (voice channels only)
     *    'slowmode' => int, (text channels only, 0-21600)
     *    'parent' => \CharlotteDunois\Yasmin\Models\CategoryChannel|string, (string = channel ID)
     *    'permissionOverwrites' => \CharlotteDunois\Collect\Collection|array (an array or Collection of PermissionOverwrite instances or permission overwrite arrays)
     * )
     * ```
     *
     * @param array   $options
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function edit(array $options, string $reason = '') {
        if(empty($options)) {
            throw new \InvalidArgumentException('Unable to edit channel with zero information');
        }
        
        $data = \CharlotteDunois\Yasmin\Utils\DataHelpers::applyOptions($options, array(
            'name' => array('type' => 'string'),
            'position' => array('type' => 'int'),
            'topic' => array('type' => 'string'),
            'nsfw' => array('type' => 'bool'),
            'bitrate' => array('type' => 'int'),
            'userLimit' => array('key' => 'user_limit', 'type' => 'int'),
            'slowmode' => array('key' => 'rate_limit_per_user', 'type' => 'int'),
            'parent' => array('key' => 'parent_id', 'parse' => function ($val) {
                return ($val instanceof \CharlotteDunois\Yasmin\Models\CategoryChannel ? $val->id : $val);
            }),
            'permissionOverwrites' => array('key' => 'permission_overwrites', 'parse' => function ($val) {
                if($val instanceof \CharlotteDunois\Collect\Collection) {
                    $val = $val->all();
                }
                
                return \array_values($val);
            })
        ));
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($data, $reason) {
            $this->client->apimanager()->endpoints->channel->modifyChannel($this->id, $data, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Deletes the channel.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function delete(string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($reason) {
            $this->client->apimanager()->endpoints->channel->deleteChannel($this->id, $reason)->done(function () use ($resolve) {
                $resolve();
            }, $reject);
        }));
    }
    
    /**
     * Fetches all invites of this channel. Resolves with a Collection of Invite instances, mapped by their code.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Invite
     */
    function fetchInvites() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            $this->client->apimanager()->endpoints->channel->getChannelInvites($this->id)->done(function ($data) use ($resolve) {
                $collection = new \CharlotteDunois\Collect\Collection();
                
                foreach($data as $invite) {
                    $inv = new \CharlotteDunois\Yasmin\Models\Invite($this->client, $invite);
                    $collection->set($inv->code, $inv);
                }
                
                $resolve($collection);
            }, $reject);
        }));
    }
    
    /**
     * Whether the permission overwrites match the parent channel (permissions synced).
     * @return bool
     * @throws \BadMethodCallException
     */
    function isPermissionsLocked() {
        if($this->parentID === null) {
            throw new \BadMethodCallException('This channel does not have a parent');
        }
        
        $parent = $this->parent;
        
        if($parent->permissionOverwrites->count() !== $this->permissionOverwrites->count()) {
            return false;
        }
        
        return !((bool) $this->permissionOverwrites->first(function ($perm) use ($parent) {
            $permp = $parent->permissionOverwrites->get($perm->id);
            return (!$permp || $perm->allowed->bitfield !== $permp->allowed->bitfield || $perm->denied->bitfield !== $permp->denied->bitfield);
        }));
    }
    
    /**
     * Returns the permissions for the given member.
     * @param \CharlotteDunois\Yasmin\Models\GuildMember|string  $member
     * @return \CharlotteDunois\Yasmin\Models\Permissions
     * @throws \InvalidArgumentException
     * @see https://discordapp.com/developers/docs/topics/permissions#permission-overwrites
     */
    function permissionsFor($member) {
        $member = $this->guild->members->resolve($member);
        
        if($member->id === $this->guild->ownerID) {
            return (new \CharlotteDunois\Yasmin\Models\Permissions(\CharlotteDunois\Yasmin\Models\Permissions::ALL));
        }
        
        $permissions = $member->permissions;
        
        if($permissions->has('ADMINISTRATOR')) {
            return (new \CharlotteDunois\Yasmin\Models\Permissions(\CharlotteDunois\Yasmin\Models\Permissions::ALL));
        }
        
        $overwrites = $this->overwritesFor($member);
        
        if($overwrites['everyone']) {
            $permissions->remove(($overwrites['everyone']->deny->bitfield & ~\CharlotteDunois\Yasmin\Models\Permissions::CHANNEL_UNACCESSIBLE_PERMISSIONS));
            $permissions->add(($overwrites['everyone']->allow->bitfield & ~\CharlotteDunois\Yasmin\Models\Permissions::CHANNEL_UNACCESSIBLE_PERMISSIONS));
        }
        
        foreach($overwrites['roles'] as $role) {
            $permissions->remove(($role->deny->bitfield & ~\CharlotteDunois\Yasmin\Models\Permissions::CHANNEL_UNACCESSIBLE_PERMISSIONS));
            $permissions->add(($role->allow->bitfield & ~\CharlotteDunois\Yasmin\Models\Permissions::CHANNEL_UNACCESSIBLE_PERMISSIONS));
        }
        
        if($overwrites['member']) {
            $permissions->remove(($overwrites['member']->deny->bitfield & ~\CharlotteDunois\Yasmin\Models\Permissions::CHANNEL_UNACCESSIBLE_PERMISSIONS));
            $permissions->add(($overwrites['member']->allow->bitfield & ~\CharlotteDunois\Yasmin\Models\Permissions::CHANNEL_UNACCESSIBLE_PERMISSIONS));
        }
        
        return $permissions;
    }
    
    /**
     * Returns the permissions overwrites for the given member as an associative array.
     *
     * ```
     * array(
     *     'everyone' => \CharlotteDunois\Yasmin\Models\PermissionOverwrite|null,
     *     'member' => \CharlotteDunois\Yasmin\Models\PermissionOverwrite|null,
     *     'roles' => \CharlotteDunois\Yasmin\Models\PermissionOverwrite[]
     * )
     * ```
     *
     * @param \CharlotteDunois\Yasmin\Models\GuildMember|string  $member
     * @return array
     * @throws \InvalidArgumentException
     */
    function overwritesFor($member) {
        $member = $this->guild->members->resolve($member);
        
        $everyoneOverwrites = null;
        $memberOverwrites = null;
        $rolesOverwrites = array();
        
        foreach($this->permissionOverwrites as $overwrite) {
            if($overwrite->id === $this->guild->id) {
                $everyoneOverwrites = $overwrite;
            } elseif($overwrite->id === $member->id) {
                $memberOverwrites = $overwrite;
            } elseif($member->roles->has($overwrite->id)) {
                $rolesOverwrites[] = $overwrite;
            }
        }
        
        return array(
            'everyone' => $everyoneOverwrites,
            'member' => $memberOverwrites,
            'roles' => $rolesOverwrites
        );
    }
    
    /**
     * Overwrites the permissions for a member or role in this channel. Resolves with an instance of PermissionOverwrite.
     * @param \CharlotteDunois\Yasmin\Models\GuildMember|\CharlotteDunois\Yasmin\Models\Role|string  $memberOrRole  The member or role.
     * @param \CharlotteDunois\Yasmin\Models\Permissions|int                                         $allow         Which permissions should be allowed?
     * @param \CharlotteDunois\Yasmin\Models\Permissions|int                                         $deny          Which permissions should be denied?
     * @param string                                                                                 $reason        The reason for this.
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     * @see \CharlotteDunois\Yasmin\Models\PermissionOverwrite
     */
    function overwritePermissions($memberOrRole, $allow, $deny = 0, string $reason = '') {
        [ $memberOrRole, $options ] = $this->validateOverwritePermissions($memberOrRole, $allow, $deny);
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($memberOrRole, $options, $reason) {
            $this->client->apimanager()->endpoints->channel->editChannelPermissions($this->id, $memberOrRole, $options, $reason)->done(function () use ($memberOrRole, $options, $resolve, $reject) {
                $options['id'] = $memberOrRole;
                
                if($options['type'] === 'member') {
                    $fetch = $this->guild->fetchMember($options['id']);
                } else {
                    $fetch = \React\Promise\resolve();
                }
                
                $fetch->done(function () use ($options, $resolve) {
                    if($options['allow'] instanceof \CharlotteDunois\Yasmin\Models\Permissions) {
                        $options['allow'] = $options['allow']->bitfield;
                    }
                    
                    if($options['deny'] instanceof \CharlotteDunois\Yasmin\Models\Permissions) {
                        $options['deny'] = $options['deny']->bitfield;
                    }
                    
                    $overwrite = new \CharlotteDunois\Yasmin\Models\PermissionOverwrite($this->client, $this, $options);
                    $this->permissionOverwrites->set($overwrite->id, $overwrite);
                    
                    $resolve($overwrite);
                }, $reject);
            }, $reject);
        }));
    }
    
    /**
     * Locks in the permission overwrites from the parent channel. Resolves with $this.
     * @param string  $reason  The reason for this.
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \BadMethodCallException
     */
    function lockPermissions(string $reason = '') {
        if(!$this->parent) {
            throw new \BadMethodCallException('This channel does not have a parent');
        }
        
        $overwrites = \array_values($this->parent->permissionOverwrites->map(function ($overwrite) {
            return array(
                'id' => $overwrite->id,
                'type' => $overwrite->type,
                'allow' => $overwrite->allow->bitfield,
                'deny' => $overwrite->deny->bitfield
            );
        })->all());
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($overwrites, $reason) {
            $promises = array();
            
            $overwritesID = \array_column($overwrites, 'id');
            foreach($this->permissionOverwrites as $perm) {
                if(!\in_array($perm->id, $overwritesID)) {
                    $promises[] = $perm->delete($reason);
                }
            }
            
            foreach($overwrites as $overwrite) {
                $promises[] = $this->client->apimanager()->endpoints->channel->editChannelPermissions($this->id, $overwrite['id'], $overwrite, $reason);
            }
            
            \React\Promise\all($promises)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Sets the name of the channel. Resolves with $this.
     * @param string  $name
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setName(string $name, string $reason = '') {
        return $this->edit(array('name' => $name), $reason);
    }
    
    /**
     * Sets the nsfw flag of the channel. Resolves with $this.
     * @param bool    $nsfw
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setNSFW(bool $nsfw, string $reason = '') {
        return $this->edit(array('nsfw' => $nsfw), $reason);
    }
    
    /**
     * Sets the parent of the channel. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\CategoryChannel|string  $parent  An instance of CategoryChannel or the channel ID.
     * @param string                                                 $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setParent($parent, string $reason = '') {
        return $this->edit(array('parent' => $parent), $reason);
    }
    
    /**
     * Sets the permission overwrites of the channel. Resolves with $this.
     * @param \CharlotteDunois\Collect\Collection|array  $permissionOverwrites  An array or Collection of PermissionOverwrite instances or permission overwrite arrays.
     * @param string                                     $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setPermissionOverwrites($permissionOverwrites, string $reason = '') {
        return $this->edit(array('permissionOverwrites' => $permissionOverwrites), $reason);
    }
    
    /**
     * Sets the position of the channel. Resolves with $this.
     * @param int     $position
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setPosition(int $position, string $reason = '') {
        if($position < 0) {
            throw new \InvalidArgumentException('Position can not be below 0');
        }
        
        $newPositions = array();
        $newPositions[] = array('id' => $this->id, 'position' => $position);
        
        $count = $this->guild->channels->count();
        $channels = $this->guild->channels->sortCustom(function ($a, $b) {
            if($a->position === $b->position) {
                return $a->id <=> $b->id;
            }
            
            return $a->position <=> $b->position;
        })->values()->all();
        
        $pos = 0;
        for($i = 0; $i < $count; $i++) {
            if($pos === $position) {
                $pos++;
            }
            
            $channel = $channels[$i];
            
            if($pos === $channel->position) {
                continue;
            }
            
            $newPositions[] = array('id' => $channel, 'position' => $pos);
            $pos++;
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($newPositions, $reason) {
            $this->client->apimanager()->endpoints->guild->modifyGuildChannelPositions($this->guild->id, $newPositions, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Sets the topic of the channel. Resolves with $this.
     * @param string  $topic
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setTopic(string $topic, string $reason = '') {
        return $this->edit(array('topic' => $topic), $reason);
    }
    
    /**
     * Validates the given overwritePermissions arguments.
     * @param \CharlotteDunois\Yasmin\Models\GuildMember|\CharlotteDunois\Yasmin\Models\Role|string  $memberOrRole  The member or role.
     * @param \CharlotteDunois\Yasmin\Models\Permissions|int                                         $allow         Which permissions should be allowed?
     * @param \CharlotteDunois\Yasmin\Models\Permissions|int                                         $deny          Which permissions should be denied?
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function validateOverwritePermissions($memberOrRole, $allow, $deny = 0) {
        $options = array();
        
        if($memberOrRole instanceof \CharlotteDunois\Yasmin\Models\GuildMember) {
            $memberOrRole = $memberOrRole->id;
            $options['type'] = 'member';
        } elseif($memberOrRole instanceof \CharlotteDunois\Yasmin\Models\Role) {
            $memberOrRole = $memberOrRole->id;
            $options['type'] = 'role';
        } else {
            try {
                $memberOrRole = $this->guild->roles->resolve($memberOrRole)->id;
                $options['type'] = 'role';
            } catch (\InvalidArgumentException $e) {
                $options['type'] = 'member';
            }
        }
        
        if(!\is_int($allow) && !($allow instanceof \CharlotteDunois\Yasmin\Models\Permissions)) {
            throw new \InvalidArgumentException('Allow has to be an int or an instance of Permissions');
        }
        
        if(!\is_int($deny) && !($deny instanceof \CharlotteDunois\Yasmin\Models\Permissions)) {
            throw new \InvalidArgumentException('Deny has to be an int or an instance of Permissions');
        }
        
        if(\json_encode($allow) === \json_encode($deny)) {
            throw new \InvalidArgumentException('Allow and deny must have different permissions');
        }
        
        $options['allow'] = $allow;
        $options['deny'] = $deny;
        
        return array($memberOrRole, $options);
    }
}
