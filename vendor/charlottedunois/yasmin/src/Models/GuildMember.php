<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Models;

/**
 * Represents a guild member.
 *
 * @property string                                                         $id               The ID of the member.
 * @property string|null                                                    $nickname         The nickname of the member, or null.
 * @property bool                                                           $deaf             Whether the member is server deafened.
 * @property bool                                                           $mute             Whether the member is server muted.
 * @property \CharlotteDunois\Yasmin\Models\Guild                           $guild            The guild this member belongs to.
 * @property int                                                            $joinedTimestamp  The timestamp of when this member joined.
 * @property bool                                                           $selfDeaf         Whether the member is locally deafened.
 * @property bool                                                           $selfMute         Whether the member is locally muted.
 * @property \CharlotteDunois\Collect\Collection                            $roles            A Collection of all roles the member has, mapped by their ID. ({@see \CharlotteDunois\Yasmin\Models\Role})
 * @property bool                                                           $suppress         Whether you suppress the member.
 * @property string|null                                                    $voiceChannelID   The ID of the voice channel the member is in, or null.
 * @property string                                                         $voiceSessionID   The voice session ID, or null.
 *
 * @property string                                                         $displayName      The displayed name.
 * @property \DateTime                                                      $joinedAt         An DateTime instance of joinedTimestamp.
 * @property \CharlotteDunois\Yasmin\Models\Permissions                     $permissions      The permissions of the member, only taking roles into account.
 * @property \CharlotteDunois\Yasmin\Models\Presence|null                   $presence         The presence of the member in this guild, or null.
 * @property \CharlotteDunois\Yasmin\Models\User|null                       $user             The User instance of the member. This should never be null, unless you fuck up.
 * @property \CharlotteDunois\Yasmin\Models\VoiceChannel|null               $voiceChannel     The voice channel the member is in, if connected to voice, or null.
 */
class GuildMember extends ClientBase {
    /**
     * The guild this member belongs to.
     * @var \CharlotteDunois\Yasmin\Models\Guild
     */
    protected $guild;
    
    /**
     * The ID of the member.
     * @var string
     */
    protected $id;
    
    /**
     * The nickname of the member, or null.
     * @var string|null
     */
    protected $nickname;
    
    /**
     * Whether the member is server deafened.
     * @var bool
     */
    protected $deaf = false;
    
    /**
     * Whether the member is server muted.
     * @var bool
     */
    protected $mute = false;
    
    /**
     * Whether the member is locally deafened.
     * @var bool
     */
    protected $selfDeaf = false;
    
    /**
     * Whether the member is locally muted.
     * @var bool
     */
    protected $selfMute = false;
    
    /**
     * Whether you suppress the member.
     * @var bool
     */
    protected $suppress = false;
    
    /**
     * The ID of the voice channel the member is in, or null.
     * @var string|null
     */
    protected $voiceChannelID;
    
    /**
     * The voice session ID, or null.
     * @var string|null
     */
    protected $voiceSessionID;
    
    /**
     * The timestamp of when this member joined.
     * @var int
     */
    protected $joinedTimestamp;
    
    /**
     * A Collection of all roles the member has, mapped by their ID.
     * @var \CharlotteDunois\Collect\Collection
     */
    protected $roles;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\Models\Guild $guild, array $member) {
        parent::__construct($client);
        $this->guild = $guild;
        
        $this->id = (string) $member['user']['id'];
        $this->client->users->patch($member['user']);
        
        $this->roles = new \CharlotteDunois\Collect\Collection();
        $this->joinedTimestamp = (new \DateTime((!empty($member['joined_at']) ? $member['joined_at'] : 'now')))->getTimestamp();
        
        $this->_patch($member);
    }
    
    /**
     * {@inheritdoc}
     * @return mixed
     * @throws \RuntimeException
     * @internal
     */
    function __get($name) {
        if(\property_exists($this, $name)) {
            return $this->$name;
        }
        
        switch($name) {
            case 'displayName':
                return ($this->nickname ?? $this->user->username);
            break;
            case 'joinedAt':
                return \CharlotteDunois\Yasmin\Utils\DataHelpers::makeDateTime($this->joinedTimestamp);
            break;
            case 'permissions':
                if($this->id === $this->guild->ownerID) {
                    return (new \CharlotteDunois\Yasmin\Models\Permissions(\CharlotteDunois\Yasmin\Models\Permissions::ALL));
                }
                
                $permissions = 0;
                foreach($this->roles as $role) {
                    $permissions |= $role->permissions->bitfield;
                }
                
                return (new \CharlotteDunois\Yasmin\Models\Permissions($permissions));
            break;
            case 'presence':
                return $this->guild->presences->get($this->id);
            break;
            case 'user':
                return $this->client->users->get($this->id);
            break;
            case 'voiceChannel':
                return $this->guild->channels->get($this->voiceChannelID);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Adds a role to the guild member. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\Role|string   $role    A role instance or role ID.
     * @param string                                       $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function addRole($role, string $reason = '') {
        if($role instanceof \CharlotteDunois\Yasmin\Models\Role) {
            $role = $role->id;
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($role, $reason) {
            $this->client->apimanager()->endpoints->guild->addGuildMemberRole($this->guild->id, $this->id, $role, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Adds roles to the guild member. Resolves with $this.
     * @param array|\CharlotteDunois\Collect\Collection   $roles    A collection or array of Role instances or role IDs.
     * @param string                                      $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function addRoles($roles, string $reason = '') {
        if($roles instanceof \CharlotteDunois\Collect\Collection) {
            $roles = $roles->all();
        }
        
        $roles = \array_merge($this->roles->all(), $roles);
        return $this->edit(array('roles' => $roles), $reason);
    }
    
    /**
     * Bans the guild member.
     * @param int     $days     Number of days of messages to delete (0-7).
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function ban(int $days = 0, string $reason = '') {
        return $this->guild->ban($this, $days, $reason);
    }
    
    /**
     * Edits the guild member. Resolves with $this.
     *
     * Options are as following (only one required):
     *
     * ```
     * array(
     *   'nick' => string,
     *   'roles' => array|\CharlotteDunois\Collect\Collection, (of role instances or role IDs)
     *   'deaf' => bool,
     *   'mute' => bool,
     *   'channel' => \CharlotteDunois\Yasmin\Models\VoiceChannel|string|null (will move the member to that channel, if member is connected to voice)
     * )
     * ```
     *
     * @param array   $options
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function edit(array $options, string $reason = '') {
        $data = \CharlotteDunois\Yasmin\Utils\DataHelpers::applyOptions($options, array(
            'nick' => array('type' => 'string'),
            'roles' => array('parse' => function ($val) {
                if($val instanceof \CharlotteDunois\Collect\Collection) {
                    $val = $val->all();
                }
                
                return \array_unique(\array_map(function ($role) {
                    if($role instanceof \CharlotteDunois\Yasmin\Models\Role) {
                        return $role->id;
                    }
                    
                    return $role;
                }, $val));
            }),
            'deaf' => array('type' => 'bool'),
            'mute' => array('type' => 'bool'),
            'channel' => array('parse' => function ($val) {
                return ($val !== null ? $this->guild->channels->resolve($val)->getId() : null);
            })
        ));
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($data, $reason) {
            $this->client->apimanager()->endpoints->guild->modifyGuildMember($this->guild->id, $this->id, $data, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Gets the role the member is displayed with.
     * @return \CharlotteDunois\Yasmin\Models\Role
     */
    function getColorRole() {
        $roles = $this->roles->filter(function ($role) {
            return $role->color;
        });
        
        if($roles->count() === 0) {
            return null;
        }
        
        return $roles->reduce(function ($prev, $role) {
            if($prev === null) {
                return $role;
            }
            
            return ($role->comparePositionTo($prev) > 0 ? $role : $prev);
        });
    }
    
    /**
     * Gets the displayed color of the member.
     * @return int|null
     */
    function getDisplayColor() {
        $colorRole = $this->getColorRole();
        if($colorRole !== null && $colorRole->color > 0) {
            return $colorRole->color;
        }
        
        return null;
    }
    
    /**
     * Gets the displayed color of the member as hex string.
     * @return string|null
     */
    function getDisplayHexColor() {
        $colorRole = $this->getColorRole();
        if($colorRole !== null && $colorRole->color > 0) {
            return $colorRole->hexColor;
        }
        
        return null;
    }
    
    /**
     * Gets the highest role of the member.
     * @return \CharlotteDunois\Yasmin\Models\Role
     */
    function getHighestRole() {
        return $this->roles->reduce(function ($prev, $role) {
            if($prev === null) {
                return $role;
            }
            
            return ($role->comparePositionTo($prev) > 0 ? $role : $prev);
        });
    }
    
    /**
     * Gets the role the member is hoisted with.
     * @return \CharlotteDunois\Yasmin\Models\Role|null
     */
    function getHoistRole() {
        $roles = $this->roles->filter(function ($role) {
            return $role->hoist;
        });
        
        if($roles->count() === 0) {
            return null;
        }
        
        return $roles->reduce(function ($prev, $role) {
            if($prev === null) {
                return $role;
            }
            
            return ($role->comparePositionTo($prev) > 0 ? $role : $prev);
        });
    }
    
    /**
     * Whether the member can be banned by the client user.
     * @return bool
     */
    function isBannable() {
        if($this->id === $this->guild->ownerID || $this->id === $this->client->user->id) {
            return false;
        }
        
        $member = $this->guild->me;
        if(!$member->permissions->has(\CharlotteDunois\Yasmin\Models\Permissions::PERMISSIONS['BAN_MEMBERS'])) {
            return false;
        }
        
        return ($member->getHighestRole()->comparePositionTo($this->getHighestRole()) > 0);
    }
    
    /**
     * Whether the member can be kicked by the client user.
     * @return bool
     */
    function isKickable() {
        if($this->id === $this->guild->ownerID || $this->id === $this->client->user->id) {
            return false;
        }
        
        $member = $this->guild->me;
        if(!$member->permissions->has(\CharlotteDunois\Yasmin\Models\Permissions::PERMISSIONS['KICK_MEMBERS'])) {
            return false;
        }
        
        return ($member->getHighestRole()->comparePositionTo($this->getHighestRole()) > 0);
    }
    
    /**
     * Kicks the guild member.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function kick(string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($reason) {
            $this->client->apimanager()->endpoints->guild->removeGuildMember($this->guild->id, $this->id, $reason)->done(function () use ($resolve) {
                $resolve();
            }, $reject);
        }));
    }
    
    /**
     * Returns permissions for a member in a guild channel, taking into account roles and permission overwrites.
     * @param \CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface|string  $channel
     * @return \CharlotteDunois\Yasmin\Models\Permissions
     * @throws \InvalidArgumentException
     */
    function permissionsIn($channel) {
        $channel = $this->guild->channels->resolve($channel);
        if(!($channel instanceof \CharlotteDunois\Yasmin\Interfaces\GuildChannelInterface)) {
            throw new \InvalidArgumentException('Given channel is not a guild channel');
        }
        
        return $channel->permissionsFor($this);
    }
    
    /**
     * Removes a role from the guild member. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\Role|string   $role    A role instance or role ID.
     * @param string                                       $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function removeRole($role, string $reason = '') {
        if($role instanceof \CharlotteDunois\Yasmin\Models\Role) {
            $role = $role->id;
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($role, $reason) {
            $this->client->apimanager()->endpoints->guild->removeGuildMemberRole($this->guild->id, $this->id, $role, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Removes roles from the guild member. Resolves with $this.
     * @param \CharlotteDunois\Collect\Collection|array   $roles    A collection or array of role instances (or role IDs).
     * @param string                                      $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function removeRoles($roles, string $reason = '') {
        if($roles instanceof \CharlotteDunois\Collect\Collection) {
            $roles = $roles->all();
        }
        
        $roles = \array_filter($this->roles->all(), function ($role) use ($roles) {
            return (!\in_array($role, $roles, true) && !\in_array(((string) $role->id), $roles, true));
        });
        
        return $this->edit(array('roles' => $roles), $reason);
    }
    
    /**
     * Deafen/undeafen a guild member. Resolves with $this.
     * @param bool    $deaf
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setDeaf(bool $deaf, string $reason = '') {
        return $this->edit(array('deaf' => $deaf), $reason);
    }
    
    /**
     * Mute/unmute a guild member. Resolves with $this.
     * @param bool    $mute
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setMute(bool $mute, string $reason = '') {
        return $this->edit(array('mute' => $mute), $reason);
    }
    
    /**
     * Set the nickname of the guild member. Resolves with $this.
     * @param string  $nickname
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setNickname(string $nickname, string $reason = '') {
        if($this->id === $this->client->user->id) {
            return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($nickname) {
                $this->client->apimanager()->endpoints->guild->modifyCurrentNick($this->guild->id, $this->id, $nickname)->done(function () use ($resolve) {
                    $resolve($this);
                }, $reject);
            }));
        }
        
        return $this->edit(array('nick' => $nickname), $reason);
    }
    
    /**
     * Sets the roles of the guild member. Resolves with $this.
     * @param \CharlotteDunois\Collect\Collection|array<\CharlotteDunois\Yasmin\Models\Role>   $roles    A collection or array of role instances (or role IDs).
     * @param string                                                                                $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setRoles($roles, string $reason = '') {
        return $this->edit(array('roles' => $roles), $reason);
    }
    
    /**
     * Moves the guild member to the given voice channel, if connected to voice. Resolves with $this.
     * If the member is connected to a voice channel and the new channel is null,
     * then the member will be disconnected.
     * @param \CharlotteDunois\Yasmin\Models\VoiceChannel|string|null  $channel
     * @param string                                                   $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setVoiceChannel($channel, string $reason = '') {
        return $this->edit(array('channel' => $channel), $reason);
    }
    
    /**
     * The internal hook for cloning.
     * @return void
     * @internal
     */
    function __clone() {
        $this->roles = clone $this->roles;
    }
    
    /**
     * Automatically converts to a mention.
     * @return string
     */
    function __toString() {
        return '<@'.(!empty($this->nickname) ? '!' : '').$this->id.'>';
    }
    
    /**
     * @return void
     * @internal
     */
    function _setVoiceState(array $voice) {
        $this->voiceChannelID = (!empty($voice['channel_id']) ? ((string) $voice['channel_id']) : null);
        $this->voiceSessionID = (string) $voice['session_id'];
        $this->deaf = (bool) $voice['deaf'];
        $this->mute = (bool) $voice['mute'];
        $this->selfDeaf = (bool) $voice['self_deaf'];
        $this->selfMute = (bool) $voice['self_mute'];
        $this->suppress = (bool) $voice['suppress'];
    }
    
    /**
     * @return void
     * @internal
     */
    function _patch(array $data) {
        if(empty($data['nick'])) {
            $this->nickname = null;
        } elseif($data['nick'] !== $this->nickname) {
            $this->nickname = $data['nick'];
        }
        
        $this->deaf = (bool) ($data['deaf'] ?? false);
        $this->mute = (bool) ($data['mute'] ?? false);
        
        if(isset($data['roles'])) {
            $this->roles->clear();
            $this->roles->set($this->guild->id, $this->guild->roles->get($this->guild->id));
            
            foreach($data['roles'] as $role) {
                if($this->guild->roles->has($role)) {
                    $grole = $this->guild->roles->get($role);
                    $this->roles->set($grole->id, $grole);
                }
            }
        }
    }
}
