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
 * Represents an emoji - both custom and unicode emojis.
 *
 * @property string|null                                          $id                 The emoji ID, or null for unicode emoji.
 * @property string                                               $name               The emoji name.
 * @property \CharlotteDunois\Yasmin\Models\User|null             $user               The user that created the emoji, or null.
 * @property \CharlotteDunois\Yasmin\Models\Guild|null            $guild              The guild this emoji belongs to, or null.
 * @property int|null                                             $createdTimestamp   The timestamp of when this emoji was created, or null for unicode emoji.
 * @property bool                                                 $animated           Whether this emoji is animated.
 * @property bool                                                 $managed            Is the emoji managed?
 * @property bool                                                 $requireColons      Does the emoji require colons?
 * @property \CharlotteDunois\Collect\Collection                  $roles              A collection of roles that this emoji is active for (empty if all).
 *
 * @property \DateTime|null                                       $createdAt          An DateTime instance of the createdTimestamp, or null for unicode emoji.
 * @property string                                               $identifier         The identifier for the emoji.
 * @property int|string                                           $uid                The used identifier in the system (ID or name, that is).
 */
class Emoji extends ClientBase {
    /**
     * The guild this emoji belongs to, or null.
     * @var \CharlotteDunois\Yasmin\Models\Guild|null
     */
    protected $guild;
    
    /**
     * The emoji ID, or null for unicode emoji.
     * @var string|null
     */
    protected $id;
    
    /**
     * The emoji name.
     * @var string
     */
    protected $name;
    
    /**
     * A collection of roles that this emoji is active for (empty if all).
     * @var \CharlotteDunois\Collect\Collection
     */
    protected $roles;
    
    /**
     * The user that created the emoji, or null.
     * @var \CharlotteDunois\Yasmin\Models\User|null
     */
    protected $user;
    
    /**
     * Does the emoji require colons?
     * @var bool
     */
    protected $requireColons;
    
    /**
     * Is the emoji managed?
     * @var bool
     */
    protected $managed;
    
    /**
     * The timestamp of when this emoji was created, or null for unicode emoji.
     * @var int
     */
    protected $createdTimestamp;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, ?\CharlotteDunois\Yasmin\Models\Guild $guild, array $emoji) {
        parent::__construct($client);
        
        $this->id = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($emoji['id'] ?? null), 'string');
        $this->createdTimestamp = ($this->id ? ((int) \CharlotteDunois\Yasmin\Utils\Snowflake::deconstruct($this->id)->timestamp) : null);
        
        $this->guild = ($this->id ? $guild : null);
        $this->roles = new \CharlotteDunois\Collect\Collection();
        
        $this->_patch($emoji);
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
                if($this->id !== null && $this->createdTimestamp !== null) {
                    return \CharlotteDunois\Yasmin\Utils\DataHelpers::makeDateTime($this->createdTimestamp);
                }
                
                return null;
            break;
            case 'identifier':
                if($this->id !== null) {
                    return $this->name.':'.$this->id;
                }
                
                return \rawurlencode($this->name);
            break;
            case 'uid':
                return ($this->id ?? $this->name);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Adds a role to the list of roles that can use this emoji. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\Role|string  $role
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \BadMethodCallException  Throws on unicode emojis.
     */
    function addRestrictedRole($role) {
        $roles = $this->roles->map(function ($role) {
            return $role->id;
        })->all();
        $roles[] = $role;
        
        return $this->edit(array('roles' => \array_values($roles)));
    }
    
    /**
     * Adds multiple roles to the list of roles that can use this emoji. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\Role|string  ...$role
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \BadMethodCallException  Throws on unicode emojis.
     */
    function addRestrictedRoles(...$role) {
        $roles = $this->roles->map(function ($role) {
            return $role->id;
        })->all();
        
        foreach($role as $r) {
            $roles[] = ($r instanceof \CharlotteDunois\Yasmin\Models\Role ? $r->id : $r);
        }
        
        return $this->edit(array('roles' => \array_values($roles)));
    }
    
    /**
     * Edits the emoji. Resolves with $this.
     *
     * Options are as following (at least one required):
     *
     * ```
     * array(
     *   'name' => string,
     *   'roles' => array<string|\CharlotteDunois\Yasmin\Models\Role>|\CharlotteDunois\Collect\Collection<string|\CharlotteDunois\Yasmin\Models\Role>
     * )
     * ```
     *
     * @param array   $options
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \BadMethodCallException  Throws on unicode emojis.
     */
    function edit(array $options, string $reason = '') {
        if($this->id === null) {
            throw new \BadMethodCallException('Unable to edit an unicode emoji');
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($options, $reason) {
            if(!empty($options['roles'])) {
                if($options['roles'] instanceof \CharlotteDunois\Collect\Collection) {
                    $options['roles'] = $options['roles']->all();
                }
                
                $options['roles'] = \array_map(function ($role) {
                    if($role instanceof \CharlotteDunois\Yasmin\Models\Role) {
                        return $role->id;
                    }
                    
                    return $role;
                }, $options['roles']);
            }
            
            $this->client->apimanager()->endpoints->emoji->modifyGuildEmoji($this->guild->id, $this->id, $options, $reason)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Deletes the emoji.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \BadMethodCallException  Throws on unicode emojis.
     */
    function delete(string $reason = '') {
        if($this->id === null) {
            throw new \BadMethodCallException('Unable to delete a non-guild emoji');
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($reason) {
            $this->client->apimanager()->endpoints->emoji->deleteGuildEmoji($this->guild->id, $this->id, $reason)->done(function () use ($resolve) {
                $resolve();
            }, $reject);
        }));
    }
    
    /**
     * Get the image URL of the custom emoji.
     * @return string
     * @throws \BadMethodCallException  Throws on unicode emojis.
     */
    function getImageURL() {
        if($this->id === null) {
            throw new \BadMethodCallException('Unable to get image url of a non-guild emoji');
        }
        
        return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['emojis'], $this->id, ($this->animated ? 'gif' : 'png'));
    }
    
    /**
     * Removes a role from the list of roles that can use this emoji. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\Role|string  $role
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \BadMethodCallException  Throws on unicode emojis.
     */
    function removeRestrictedRole($role) {
        if($this->roles->count() === 0) {
            return \React\Promise\resolve($this);
        }
        
        $roles = $this->roles->map(function ($role) {
            return $role->id;
        })->all();
        
        $key = \array_search(($role instanceof \CharlotteDunois\Yasmin\Models\Role ? $role->id : $role), $roles, true);
        if($key !== false) {
            unset($roles[$key]);
        }
        
        return $this->edit(array('roles' => $roles));
    }
    
    /**
     * Removes multiple roles from the list of roles that can use this emoji. Resolves with $this.
     * @param \CharlotteDunois\Yasmin\Models\Role|string  ...$role
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \BadMethodCallException  Throws on unicode emojis.
     */
    function removeRestrictedRoles(...$role) {
        if($this->roles->count() === 0) {
            return \React\Promise\resolve($this);
        }
        
        $roles = $this->roles->map(function ($role) {
            return $role->id;
        })->all();
        
        foreach($role as $r) {
            $id = ($r instanceof \CharlotteDunois\Yasmin\Models\Role ? $r->id : $r);
            $key = \array_search($id, $roles, true);
            if($key !== false) {
                unset($roles[$key]);
            }
        }
        
        return $this->edit(array('roles' => $roles));
    }
    
    /**
     * Sets the new name of the emoji. Resolves with $this.
     * @param string  $name
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \BadMethodCallException  Throws on unicode emojis.
     */
    function setName(string $name, string $reason = '') {
        return $this->edit(array('name' => $name), $reason);
    }
    
    /**
     * Automatically converts to a mention.
     * @return string
     */
    function __toString() {
        if(!$this->requireColons) {
            return $this->name;
        }
        
        return '<'.($this->animated ? 'a' : '').':'.$this->name.':'.$this->id.'>';
    }
    
    /**
     * @return void
     * @internal
     */
    function _patch(array $emoji) {
        $this->name = (string) $emoji['name'];
        $this->user = (!empty($emoji['user']) ? $this->client->users->patch($emoji['user']) : null);
        $this->animated = (bool) ($emoji['animated'] ?? false);
        $this->managed = (bool) ($emoji['managed'] ?? false);
        $this->requireColons = (($emoji['require_colons'] ?? true) && $this->id !== null);
        
        if(isset($emoji['roles'])) {
            $this->roles->clear();
            
            foreach($emoji['roles'] as $role) {
                if($this->guild->roles->has($role)) {
                    $r = $this->guild->roles->get($role);
                    $this->roles->set($r->id, $r);
                }
            }
        }
        
        if($this->id !== null) {
            $this->client->emojis->set($this->id, $this);
        }
    }
}
