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
 * Something all guild storages implement. The storage also is used as factory.
 */
interface GuildStorageInterface extends StorageInterface {
    /**
     * Returns the current element. From Iterator interface.
     * @return \CharlotteDunois\Yasmin\Models\Guild
     */
    function current();
    
    /**
     * Fetch the key from the current element. From Iterator interface.
     * @return string
     */
    function key();
    
    /**
     * Advances the internal pointer. From Iterator interface.
     * @return \CharlotteDunois\Yasmin\Models\Guild|false
     */
    function next();
    
    /**
     * Resets the internal pointer. From Iterator interface.
     * @return \CharlotteDunois\Yasmin\Models\Guild|false
     */
    function rewind();
    
    /**
     * Checks if current position is valid. From Iterator interface.
     * @return bool
     */
    function valid();
    
    /**
     * Returns all items.
     * @return \CharlotteDunois\Yasmin\Models\Guild[]
     */
    function all();
    
    /**
     * Resolves given data to a guild.
     * @param \CharlotteDunois\Yasmin\Models\Guild|string|int  $guild  string/int = guild ID
     * @return \CharlotteDunois\Yasmin\Models\Guild
     * @throws \InvalidArgumentException
     */
    function resolve($guild);
    
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
     * @return \CharlotteDunois\Yasmin\Models\Guild|null
     * @throws \InvalidArgumentException
    */
    function get($key);
    
    /**
     * Sets a key-value pair.
     * @param string                                $key
     * @param \CharlotteDunois\Yasmin\Models\Guild  $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    function set($key, $value);
    
    /**
     * Factory to create (or retrieve existing) guilds.
     * @param array     $data
     * @param int|null  $shardID
     * @return \CharlotteDunois\Yasmin\Models\Guild
     * @internal
     */
    function factory(array $data, ?int $shardID = null);
}
