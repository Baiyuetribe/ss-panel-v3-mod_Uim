<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Interfaces;

/**
 * Something all presence storages implement. The storage also is used as factory.
 */
interface PresenceStorageInterface extends StorageInterface {
    /**
     * Returns the current element. From Iterator interface.
     * @return \CharlotteDunois\Yasmin\Models\Presence
     */
    function current();
    
    /**
     * Fetch the key from the current element. From Iterator interface.
     * @return string
     */
    function key();
    
    /**
     * Advances the internal pointer. From Iterator interface.
     * @return \CharlotteDunois\Yasmin\Models\Presence|false
     */
    function next();
    
    /**
     * Resets the internal pointer. From Iterator interface.
     * @return \CharlotteDunois\Yasmin\Models\Presence|false
     */
    function rewind();
    
    /**
     * Checks if current position is valid. From Iterator interface.
     * @return bool
     */
    function valid();
    
    /**
     * Returns all items.
     * @return \CharlotteDunois\Yasmin\Models\Presence[]
     */
    function all();
    
    /**
     * Resolves given data to a presence.
     * @param \CharlotteDunois\Yasmin\Models\Presence|\CharlotteDunois\Yasmin\Models\User|string|int  $presence  string/int = user ID
     * @return \CharlotteDunois\Yasmin\Models\Presence
     * @throws \InvalidArgumentException
     */
    function resolve($presence);
    
    /**
     * Determines if a given key exists in the collection.
     * @param string  $key
     * @return bool
     * @throws \InvalidArgumentException
    */
    function has($key);
    
    /**
     * Returns the item at a given key. If the key does not exist, null is returned.
     * @param string  $key
     * @return \CharlotteDunois\Yasmin\Models\Presence|null
     * @throws \InvalidArgumentException
    */
    function get($key);
    
    /**
     * Sets a key-value pair.
     * @param string                                   $key
     * @param \CharlotteDunois\Yasmin\Models\Presence  $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    function set($key, $value);
    
    /**
     * Factory to create presences.
     * @param array  $data
     * @return \CharlotteDunois\Yasmin\Models\Presence
     * @internal
     */
    function factory(array $data);
}
