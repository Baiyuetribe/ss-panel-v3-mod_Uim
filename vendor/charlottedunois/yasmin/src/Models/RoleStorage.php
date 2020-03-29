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
 * Role Storage to store a guild's roles, utilizes Collection.
 */
class RoleStorage extends Storage implements \CharlotteDunois\Yasmin\Interfaces\RoleStorageInterface {
    /**
     * The guild this storage belongs to.
     * @var \CharlotteDunois\Yasmin\Models\Guild
     */
    protected $guild;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\Models\Guild $guild, array $data = null) {
        parent::__construct($client, $data);
        $this->guild = $guild;
        
        $this->baseStorageArgs[] = $this->guild;
    }
    
    /**
     * Resolves given data to a Role.
     * @param \CharlotteDunois\Yasmin\Models\Role|string|int  $role  string/int = role ID
     * @return \CharlotteDunois\Yasmin\Models\Role
     * @throws \InvalidArgumentException
     */
    function resolve($role) {
        if($role instanceof \CharlotteDunois\Yasmin\Models\Role) {
            return $role;
        }
        
        if(\is_int($role)) {
            $role = (string) $role;
        }
        
        if(\is_string($role) && parent::has($role)) {
            return parent::get($role);
        }
        
        throw new \InvalidArgumentException('Unable to resolve unknown role');
    }
    
    /**
     * {@inheritdoc}
     * @param string  $key
     * @return bool
     */
    function has($key) {
        return parent::has($key);
    }
    
    /**
     * {@inheritdoc}
     * @param string  $key
     * @return \CharlotteDunois\Yasmin\Models\Role|null
     */
    function get($key) {
        return parent::get($key);
    }
    
    /**
     * {@inheritdoc}
     * @param string                               $key
     * @param \CharlotteDunois\Yasmin\Models\Role  $value
     * @return $this
     */
    function set($key, $value) {
        parent::set($key, $value);
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * @param string  $key
     * @return $this
     */
    function delete($key) {
        parent::delete($key);
        return $this;
    }
    
    /**
     * Factory to create (or retrieve existing) roles.
     * @param array  $data
     * @return \CharlotteDunois\Yasmin\Models\Role
     * @internal
     */
    function factory(array $data) {
        if(parent::has($data['id'])) {
            $role = parent::get($data['id']);
            $role->_patch($data);
            return $role;
        }
        
        $role = new \CharlotteDunois\Yasmin\Models\Role($this->client, $this->guild, $data);
        $this->set($role->id, $role);
        return $role;
    }
}
