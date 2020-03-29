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
 * Permissions. Something fabulous.
 *
 * @property int  $bitfield  The bitfield value.
 */
class Permissions extends Base {
    /**
     * The value of the bitfield with all permissions granted.
     * @var int
     * @source
     */
    const ALL = 2146958847;
    
    /**
     * Available Permissions in Discord.
     * @var array
     * @source
     */
    const PERMISSIONS = array(
        'CREATE_INSTANT_INVITE' => (1 << 0),
        'KICK_MEMBERS' => (1 << 1),
        'BAN_MEMBERS' => (1 << 2),
        'ADMINISTRATOR' => (1 << 3),
        'MANAGE_CHANNELS' => (1 << 4),
        'MANAGE_GUILD' => (1 << 5),
        'ADD_REACTIONS' => (1 << 6),
        'VIEW_AUDIT_LOG' => (1 << 7),
        'PRIORITY_SPEAKER' => (1 << 8),

        'VIEW_CHANNEL' => (1 << 10),
        'SEND_MESSAGES' => (1 << 11),
        'SEND_TTS_MESSAGES' => (1 << 12),
        'MANAGE_MESSAGES' => (1 << 13),
        'EMBED_LINKS' => (1 << 14),
        'ATTACH_FILES' => (1 << 15),
        'READ_MESSAGE_HISTORY' => (1 << 16),
        'MENTION_EVERYONE' => (1 << 17),
        'USE_EXTERNAL_EMOJIS' => (1 << 18),

        'CONNECT' => (1 << 20),
        'SPEAK' => (1 << 21),
        'MUTE_MEMBERS' => (1 << 22),
        'DEAFEN_MEMBERS' => (1 << 23),
        'MOVE_MEMBERS' => (1 << 24),
        'USE_VAD' => (1 << 25),

        'CHANGE_NICKNAME' => (1 << 26),
        'MANAGE_NICKNAMES' => (1 << 27),
        'MANAGE_ROLES' => (1 << 28),
        'MANAGE_WEBHOOKS' => (1 << 29),
        'MANAGE_EMOJIS' => (1 << 30)
    );
    
    /**
     * The bitfield to remove from channel overwrites to prevent wrong calculated final permissions, as they do nothing.
     * @var int
     */
    const CHANNEL_UNACCESSIBLE_PERMISSIONS = (1 << 3);
    
    /**
     * The bitfield value.
     * @var int
     */
    protected $bitfield;
    
    /**
     * Constructs a new instance.
     * @param int  $permission
     */
    function __construct(int $permission = 0) {
        $this->bitfield = $permission;
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
        
        return parent::__get($name);
    }
    
    /**
     * @return mixed
     * @internal
     */
    function jsonSerialize() {
        return $this->bitfield;
    }
    
    /**
     * Checks if a given permission is granted.
     * @param array|int|string  $permissions
     * @param bool              $checkAdmin
     * @return bool
     * @throws \InvalidArgumentException
     */
    function has($permissions, bool $checkAdmin = true) {
        if(!\is_array($permissions)) {
            $permissions = array($permissions);
        }
        
        if($checkAdmin && ($this->bitfield & self::PERMISSIONS['ADMINISTRATOR']) > 0) {
            return true;
        }
        
        foreach($permissions as $perm) {
            $perm = self::resolve($perm);
            if(($this->bitfield & $perm) !== $perm) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Checks if a given permission is missing.
     * @param array|int|string  $permissions
     * @param bool              $checkAdmin
     * @return bool
     * @throws \InvalidArgumentException
     */
    function missing($permissions, bool $checkAdmin = true) {
        return !$this->has($permissions, $checkAdmin);
    }
    
    /**
     * Adds permissions to these ones.
     * @param int|string  ...$permissions
     * @return $this
     * @throws \InvalidArgumentException
     */
    function add(...$permissions) {
        $total = 0;
        foreach($permissions as $perm) {
            $perm = self::resolve($perm);
            $total |= $perm;
        }
        
        $this->bitfield |= $total;
        return $this;
    }
    
    /**
     * Removes permissions from these ones.
     * @param int|string  ...$permissions
     * @return $this
     * @throws \InvalidArgumentException
     */
    function remove(...$permissions) {
        $total = 0;
        foreach($permissions as $perm) {
            $perm = self::resolve($perm);
            $total |= $perm;
        }
        
        $this->bitfield &= ~$total;
        return $this;
    }
    
    /**
     * Resolves a permission name to number.
     * @param int|string  $permission
     * @return int
     * @throws \InvalidArgumentException
     */
    static function resolve($permission) {
        if(\is_int($permission)) {
            return $permission;
        } elseif(\is_string($permission) && isset(self::PERMISSIONS[$permission])) {
            return self::PERMISSIONS[$permission];
        }
        
        throw new \InvalidArgumentException('Unable to resolve unknown permission');
    }
    
    /**
     * Resolves a permission number to the name. Also checks if a given name is a valid permission.
     * @param int|string  $permission
     * @return string
     * @throws \InvalidArgumentException
     */
    static function resolveToName($permission) {
        if(\is_int($permission)) {
            $index = \array_search($permission, self::PERMISSIONS, true);
            if($index) {
                return $index;
            }
        } elseif(\is_string($permission) && isset(self::PERMISSIONS[$permission])) {
            return $permission;
        }
        
        throw new \InvalidArgumentException('Unable to resolve unknown permission');
    }
}
