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
 * Represents a guild's voice channel.
 *
 * @property string                                               $id                     The ID of the channel.
 * @property int                                                  $createdTimestamp       The timestamp of when this channel was created.
 * @property string                                               $name                   The name of the channel.
 * @property int                                                  $bitrate                The bitrate of the channel.
 * @property \CharlotteDunois\Yasmin\Models\Guild                 $guild                  The guild the channel is in.
 * @property \CharlotteDunois\Collect\Collection                  $members                Holds all members which currently are in the voice channel. ({@see \CharlotteDunois\Yasmin\Models\GuildMember})
 * @property string|null                                          $parentID               The ID of the parent channel, or null.
 * @property int                                                  $position               The position of the channel.
 * @property \CharlotteDunois\Collect\Collection                  $permissionOverwrites   A collection of PermissionOverwrite instances, mapped by their ID.
 * @property int                                                  $userLimit              The maximum amount of users allowed in the channel - 0 means unlimited.
 *
 * @property bool                                                 $full                   Checks if the voice channel is full.
 * @property \CharlotteDunois\Yasmin\Models\CategoryChannel|null  $parent                 Returns the channel's parent, or null.
 */
class VoiceChannel extends ClientBase implements \CharlotteDunois\Yasmin\Interfaces\GuildVoiceChannelInterface {
    use \CharlotteDunois\Yasmin\Traits\GuildChannelTrait;
    
    /**
     * The guild the channel is in.
     * @var \CharlotteDunois\Yasmin\Models\Guild
     */
    protected $guild;
    
    /**
     * The ID of the channel.
     * @var string
     */
    protected $id;
    
    /**
     * The timestamp of when this channel was created.
     * @var int
     */
    protected $createdTimestamp;
    
    /**
     * The name of the channel.
     * @var string
     */
    protected $name;
    
    /**
     * The bitrate of the channel.
     * @var int
     */
    protected $bitrate;
    
    /**
     * Holds all members which currently are in the voice channel.
     * @var \CharlotteDunois\Collect\Collection
     */
    protected $members;
    
    /**
     * The ID of the parent channel, or null.
     * @var string|null
     */
    protected $parentID;
    
    /**
     * The position of the channel.
     * @var int
     */
    protected $position;
    
    /**
     * A collection of PermissionOverwrite instances, mapped by their ID.
     * @var \CharlotteDunois\Collect\Collection
     */
    protected $permissionOverwrites;
    
    /**
     * The maximum amount of users allowed in the channel - 0 means unlimited.
     * @var int
     */
    protected $userLimit;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\Models\Guild $guild, array $channel) {
        parent::__construct($client);
        $this->guild = $guild;
        
        $this->id = (string) $channel['id'];
        $this->members = new \CharlotteDunois\Collect\Collection();
        $this->permissionOverwrites = new \CharlotteDunois\Collect\Collection();
        
        $this->createdTimestamp = (int) \CharlotteDunois\Yasmin\Utils\Snowflake::deconstruct($this->id)->timestamp;
        
        $this->_patch($channel);
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
            case 'full':
                return ($this->userLimit > 0 && $this->userLimit <= $this->members->count());
            break;
            case 'parent':
                return $this->guild->channels->get($this->parentID);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Whether the client user can speak in this channel.
     * @return bool
     */
    function canSpeak() {
        return $this->permissionsFor($this->guild->me)->has(\CharlotteDunois\Yasmin\Models\Permissions::PERMISSIONS['SPEAK']);
    }
    
    /**
     * Sets the bitrate of the channel. Resolves with $this.
     * @param int     $bitrate
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setBitrate(int $bitrate, string $reason = '') {
        return $this->edit(array('bitrate' => $bitrate), $reason);
    }
    
    /**
     * Sets the user limit of the channel. Resolves with $this.
     * @param int     $userLimit
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setUserLimit(int $userLimit, string $reason = '') {
        return $this->edit(array('userLimit' => $userLimit), $reason);
    }
    
    /**
     * Automatically converts to the name.
     * @return string
     */
    function __toString() {
        return $this->name;
    }
    
    /**
     * @return void
     * @internal
     */
    function _patch(array $channel) {
        $this->name = (string) ($channel['name'] ?? $this->name ?? '');
        $this->bitrate = (int) ($channel['bitrate'] ?? $this->bitrate ?? 0);
        $this->parentID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($channel['parent_id'] ?? $this->parentID ?? null), 'string');
        $this->position = (int) ($channel['position'] ?? $this->position ?? 0);
        $this->userLimit = (int) ($channel['user_limit'] ?? $this->userLimit ?? 0);
        
        if(isset($channel['permission_overwrites'])) {
            $this->permissionOverwrites->clear();
            
            foreach($channel['permission_overwrites'] as $permission) {
                $overwrite = new \CharlotteDunois\Yasmin\Models\PermissionOverwrite($this->client, $this, $permission);
                $this->permissionOverwrites->set($overwrite->id, $overwrite);
            }
        }
    }
}
