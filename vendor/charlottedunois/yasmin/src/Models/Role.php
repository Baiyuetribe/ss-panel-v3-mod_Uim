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
 * Represents a role.
 *
 * @property \CharlotteDunois\Yasmin\Models\Guild        $guild               The guild the role belongs to.
 * @property string                                      $id                  The role ID.
 * @property string                                      $name                The role name.
 * @property int                                         $createdTimestamp    The timestamp of when the role was created.
 * @property int                                         $color               The color of the role.
 * @property bool                                        $hoist               Whether the role gets displayed separately in the member list.
 * @property int                                         $position            The position of the role in the API.
 * @property \CharlotteDunois\Yasmin\Models\Permissions  $permissions         The permissions of the role.
 * @property bool                                        $managed             Whether the role is managed by an integration.
 * @property bool                                        $mentionable         Whether the role is mentionable.
 *
 * @property \DateTime                                   $createdAt           The DateTime instance of createdTimestamp.
 * @property string                                      $hexColor            Returns the hex color of the role color.
 * @property \CharlotteDunois\Collect\Collection         $members             A collection of all (cached) guild members which have the role.
 */
class Role extends ClientBase {
    /**
     * The default discord role colors. Mapped by uppercase string to integer.
     * @var array
     * @source
     */
    const DISCORD_COLORS = array(
        'AQUA' => 1752220,
        'BLUE' => 3447003,
        'GREEN' => 3066993,
        'PURPLE' => 10181046,
        'GOLD' => 15844367,
        'ORANGE' => 15105570,
        'RED' => 15158332,
        'GREY' => 9807270,
        'DARKER_GREY' => 8359053,
        'NAVY' => 3426654,
        'DARK_AQUA' => 1146986,
        'DARK_GREEN' => 2067276,
        'DARK_BLUE' => 2123412,
        'DARK_GOLD' => 12745742,
        'DARK_PURPLE' => 7419530,
        'DARK_ORANGE' => 11027200,
        'DARK_GREY' => 9936031,
        'DARK_RED' => 10038562,
        'LIGHT_GREY' => 12370112,
        'DARK_NAVY' => 2899536
    );
    
    /**
     * The guild the role belongs to.
     * @var \CharlotteDunois\Yasmin\Models\Guild
     */
    protected $guild;
    
    /**
     * The role ID.
     * @var string
     */
    protected $id;
    
    /**
     * The role name.
     * @var string
     */
    protected $name;
    
    /**
     * The color of the role.
     * @var int
     */
    protected $color;
    
    /**
     * Whether the role gets displayed separately in the member list.
     * @var bool
     */
    protected $hoist;
    
    /**
     * The position of the role in the API.
     * @var int
     */
    protected $position;
    
    /**
     * The permissions of the role.
     * @var \CharlotteDunois\Yasmin\Models\Permissions
     */
    protected $permissions;
    
    /**
     * Whether the role is managed by an integration.
     * @var bool
     */
    protected $managed;
    
    /**
     * Whether the role is mentionable.
     * @var bool
     */
    protected $mentionable;
    
    /**
     * The timestamp of when the role was created.
     * @var int
     */
    protected $createdTimestamp;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\Models\Guild $guild, array $role) {
        parent::__construct($client);
        $this->guild = $guild;
        
        $this->id = (string) $role['id'];
        $this->createdTimestamp = (int) \CharlotteDunois\Yasmin\Utils\Snowflake::deconstruct($this->id)->timestamp;
        
        $this->_patch($role);
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
            case 'hexColor':
                return '#'.\dechex($this->color);
            break;
            case 'members':
                if($this->id === $this->guild->id) {
                    return $this->guild->members->copy();
                }
                
                return $this->guild->members->filter(function ($member) {
                    return $member->roles->has($this->id);
                });
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Compares the position from the role to the given role.
     * @param \CharlotteDunois\Yasmin\Models\Role  $role
     * @return int
     */
    function comparePositionTo(\CharlotteDunois\Yasmin\Models\Role $role) {
        if($this->position === $role->position) {
            return $role->id <=> $this->id;
        }
        
        return $this->position <=> $role->position;
    }
    
    /**
     * Edits the role with the given options. Resolves with $this.
     *
     * Options are as following (only one is required):
     *
     * ```
     * array(
     *   'name' => string,
     *   'color' => int|string,
     *   'hoist' => bool,
     *   'position' => int,
     *   'permissions' => int|\CharlotteDunois\Yasmin\Models\Permissions,
     *   'mentionable' => bool
     * )
     * ```
     *
     * @param array  $options
     * @param string $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     * @see \CharlotteDunois\Yasmin\Utils\DataHelpers::resolveColor()
     */
    function edit(array $options, string $reason = '') {
        if(empty($options)) {
            throw new \InvalidArgumentException('Unable to edit role with zero information');
        }
        
        $data = \CharlotteDunois\Yasmin\Utils\DataHelpers::applyOptions($options, array(
            'name' => array('type' => 'string'),
            'color' => array('parse' => array(\CharlotteDunois\Yasmin\Utils\DataHelpers::class, 'resolveColor')),
            'hoist' => array('type' => 'bool'),
            'position' => array('type' => 'int'),
            'permissions' => null,
            'mentionable' => array('type' => 'bool')
        ));
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($data, $reason) {
            $this->client->apimanager()->endpoints->guild->modifyGuildRole($this->guild->id, $this->id, $data, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Deletes the role.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function delete(string $reason = '') {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($reason) {
            $this->client->apimanager()->endpoints->guild->deleteGuildRole($this->guild->id, $this->id, $reason)->done(function () use ($resolve) {
                $resolve();
            }, $reject);
        }));
    }
    
    /**
     * Calculates the positon of the role in the Discord client.
     * @return int
     */
    function getCalculatedPosition() {
        $sorted = $this->guild->roles->sortCustom(function (\CharlotteDunois\Yasmin\Models\Role $a, \CharlotteDunois\Yasmin\Models\Role $b) {
            return $b->comparePositionTo($a);
        });
        
        return $sorted->indexOf($this);
    }
    
    /**
     * Whether the role can be edited by the client user.
     * @return bool
     */
    function isEditable() {
        if($this->managed) {
            return false;
        }
        
        $member = $this->guild->me;
        if(!$member->permissions->has(\CharlotteDunois\Yasmin\Models\Permissions::PERMISSIONS['MANAGE_ROLES'])) {
            return false;
        }
        
        return ($member->getHighestRole()->comparePositionTo($this) > 0);
    }
    
    /**
     * Set the color of the role. Resolves with $this.
     * @param int|string  $color
     * @param string      $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     * @see \CharlotteDunois\Yasmin\Utils\DataHelpers::resolveColor()
     */
    function setColor($color, string $reason = '') {
        return $this->edit(array('color' => $color), $reason);
    }
    
    /**
     * Set whether or not the role should be hoisted. Resolves with $this.
     * @param bool    $hoist
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setHoist(bool $hoist, string $reason = '') {
        return $this->edit(array('hoist' => $hoist), $reason);
    }
    
    /**
     * Set whether the role is mentionable. Resolves with $this.
     * @param bool    $mentionable
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setMentionable(bool $mentionable, string $reason = '') {
        return $this->edit(array('mentionable' => $mentionable), $reason);
    }
    
    /**
     * Set a new name for the role. Resolves with $this.
     * @param string  $name
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setName(string $name, string $reason = '') {
        return $this->edit(array('name' => $name), $reason);
    }
    
    /**
     * Set the permissions of the role. Resolves with $this.
     * @param int|\CharlotteDunois\Yasmin\Models\Permissions  $permissions
     * @param string                                          $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setPermissions($permissions, string $reason = '') {
        return $this->edit(array('permissions' => $permissions), $reason);
    }
    
    /**
     * Set the position of the role. Resolves with $this.
     * @param int     $position
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setPosition(int $position, string $reason = '') {
        return $this->edit(array('position' => $position), $reason);
    }
    
    /**
     * Automatically converts to a mention.
     * @return string
     */
    function __toString() {
        return '<@&'.$this->id.'>';
    }
    
    /**
     * @return void
     * @internal
     */
    function _patch(array $role) {
        $this->name = (string) $role['name'];
        $this->color = (int) $role['color'];
        $this->hoist = (bool) $role['hoist'];
        $this->position = (int) $role['position'];
        $this->permissions = new \CharlotteDunois\Yasmin\Models\Permissions($role['permissions']);
        $this->managed = (bool) $role['managed'];
        $this->mentionable = (bool) $role['mentionable'];
    }
}
