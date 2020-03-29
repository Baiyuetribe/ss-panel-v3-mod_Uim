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
 * Represents a guild's store channel.
 *
 * @property string                                                      $id                     The channel ID.
 * @property \CharlotteDunois\Yasmin\Models\Guild                        $guild                  The associated guild.
 * @property int                                                         $createdTimestamp       The timestamp of when this channel was created.
 * @property string                                                      $name                   The channel name.
 * @property bool                                                        $nsfw                   Whether the channel is marked as NSFW or not.
 * @property string|null                                                 $parentID               The ID of the parent channel, or null.
 * @property int                                                         $position               The channel position.
 * @property \CharlotteDunois\Collect\Collection                         $permissionOverwrites   A collection of PermissionOverwrite instances, mapped by their ID.
 *
 * @property \DateTime                                                   $createdAt              The DateTime instance of createdTimestamp.
 * @property \CharlotteDunois\Yasmin\Models\CategoryChannel|null         $parent                 The channel's parent, or null.
 */
class GuildStoreChannel extends ClientBase implements \CharlotteDunois\Yasmin\Interfaces\GuildStoreChannelInterface {
    use \CharlotteDunois\Yasmin\Traits\GuildChannelTrait;
    
    /**
     * The associated guild.
     * @var \CharlotteDunois\Yasmin\Models\Guild
     */
    protected $guild;
    
    /**
     * The channel ID.
     * @var string
     */
    protected $id;
    
    /**
     * The ID of the parent channel, or null.
     * @var string|null
     */
    protected $parentID;
    
    /**
     * The channel name.
     * @var string
     */
    protected $name;
    
    /**
     * Whether the channel is marked as NSFW or not.
     * @var bool
     */
    protected $nsfw;
    
    /**
     * The channel position.
     * @var int
     */
    protected $position;
    
    /**
     * A collection of PermissionOverwrite instances, mapped by their ID.
     * @var \CharlotteDunois\Collect\Collection
     */
    protected $permissionOverwrites;
    
    /**
     * The timestamp of when this channel was created.
     * @var int
     */
    protected $createdTimestamp;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\Models\Guild $guild, array $channel) {
        parent::__construct($client);
        $this->guild = $guild;
        
        $this->id = (string) $channel['id'];
        
        $this->createdTimestamp = (int) \CharlotteDunois\Yasmin\Utils\Snowflake::deconstruct($this->id)->timestamp;
        $this->permissionOverwrites = new \CharlotteDunois\Collect\Collection();
        
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
            case 'createdAt':
                return \CharlotteDunois\Yasmin\Utils\DataHelpers::makeDateTime($this->createdTimestamp);
            break;
            case 'parent':
                return $this->guild->channels->get($this->parentID);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Automatically converts to a mention.
     * @return string
     */
    function __toString() {
        return '<#'.$this->id.'>';
    }
    
    /**
     * @return void
     * @internal
     */
    function _patch(array $channel) {
        $this->name = (string) ($channel['name'] ?? $this->name ?? '');
        $this->nsfw = (bool) ($channel['nsfw'] ?? $this->nsfw ?? false);
        $this->parentID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($channel['parent_id'] ?? $this->parentID ?? null), 'string');
        $this->position = (int) ($channel['position'] ?? $this->position ?? 0);
        
        if(isset($channel['permission_overwrites'])) {
            $this->permissionOverwrites->clear();
            
            foreach($channel['permission_overwrites'] as $permission) {
                $overwrite = new \CharlotteDunois\Yasmin\Models\PermissionOverwrite($this->client, $this, $permission);
                $this->permissionOverwrites->set($overwrite->id, $overwrite);
            }
        }
    }
}
