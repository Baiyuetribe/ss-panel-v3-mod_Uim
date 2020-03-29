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
 * Something all storages implement. The storage also is used as factory.
 */
interface StorageInterface extends \Countable, \Iterator {
    /**
     * Returns the current element. From Iterator interface.
     * @return mixed
     */
    function current();
    
    /**
     * Fetch the key from the current element. From Iterator interface.
     * @return mixed
     */
    function key();
    
    /**
     * Advances the internal pointer. From Iterator interface.
     * @return mixed|false
     */
    function next();
    
    /**
     * Resets the internal pointer. From Iterator interface.
     * @return mixed|false
     */
    function rewind();
    
    /**
     * Checks if current position is valid. From Iterator interface.
     * @return bool
     */
    function valid();
    
    /**
     * Returns all items.
     * @return mixed[]
     */
    function all();
    
    /**
     * Returns the total number of items. From Countable interface.
     * @return int
    */
    function count();
    
    /**
     * Returns a copy of itself. This does not make a copy of the stored data.
     * @return StorageInterface
     */
    function copy();
    
    /**
     * Determines if a given key exists in the collection.
     * @param mixed  $key
     * @return bool
     * @throws \InvalidArgumentException
    */
    function has($key);
    
    /**
     * Returns the item at a given key. If the key does not exist, null is returned.
     * @param mixed  $key
     * @return mixed|null
     * @throws \InvalidArgumentException
    */
    function get($key);
    
    /**
     * Sets a key-value pair.
     * @param mixed  $key
     * @param mixed  $value
     * @return $this
     * @throws \InvalidArgumentException
     */
    function set($key, $value);
    
    /**
     * Removes an item.
     * @param mixed  $key
     * @return $this
    */
    function delete($key);
    
    /**
     * Clears the Storage.
     * @return $this
     */
    function clear();
    
    /**
     * Returns the position of the given value in the storage. Returns null if the given value couldn't be found.
     * @param mixed  $value
     * @return int|null
     */
    function indexOf($value);
    
    /**
     * Filters the storage by a given callback, keeping only those items that pass a given truth test. Returns a new Storage instance.
     * @param callable  $closure
     * @return StorageInterface
    */
    function filter(callable $closure);
    
    /**
     * Returns the first element that passes a given truth test.
     * @param callable|null  $closure
     * @return mixed|null
    */
    function first(?callable $closure = null);
    
    /**
     * Returns the last element that passes a given truth test.
     * @param callable|null  $closure
     * @return mixed|null
    */
    function last(?callable $closure = null);
    
    /**
     * Reduces the collection to a single value, passing the result of each iteration into the subsequent iteration.
     * @param callable   $closure
     * @param mixed|null $carry
     * @return mixed|null|void
    */
    function reduce(callable $closure, $carry = null);
    
    /**
     * Sorts the collection. Returns a new Storage instance.
     * @param callable  $closure  Callback specification: `function ($a, $b): int`
     * @return StorageInterface
    */
    function sort(bool $descending = false, int $options = \SORT_REGULAR);
    
    /**
     * Sorts the collection by key. Returns a new Storage instance.
     * @param bool  $descending
     * @param int   $options
     * @return \CharlotteDunois\Collect\Collection
     */
    function sortKey(bool $descending = false, int $options = \SORT_REGULAR);
    
    /**
     * Sorts the collection using a custom sorting function. Returns a new Storage instance.
     * @param callable  $closure  Callback specification: `function ($a, $b): int`
     * @return \CharlotteDunois\Collect\Collection
     */
    function sortCustom(callable $closure);
    
    /**
     * Sorts the collection by key using a custom sorting function. Returns a new Storage instance.
     * @param callable  $closure  Callback specification: `function ($a, $b): int`
     * @return \CharlotteDunois\Collect\Collection
     */
    function sortCustomKey(callable $closure);
    
    /**
     * Return the maximum value of a given key.
     * @param mixed  $key
     * @return int
    */
    function max($key = '');
    
    /**
     * Return the minimum value of a given key.
     * @param mixed|null  $key
     * @return int
    */
    function min($key = null);
}
