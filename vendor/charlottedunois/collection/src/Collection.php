<?php
/**
 * Collection
 * Copyright 2016-2018 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Collection/blob/master/LICENSE
**/

namespace CharlotteDunois\Collect;

/**
 * Collection, an util to conventionally store a key-value pair.
 */
class Collection implements \Countable, \Iterator {
    /**
     * @var array
     */
    protected $data = array();
    
    /**
     * I think you are supposed to know what this does.
     * @param array|null  $data
     */
    function __construct(array $data = null) {
        if(!empty($data)) {
            $this->data = $data;
        }
    }
    
    /**
     * @return mixed
     * @internal
     */
    function __debugInfo() {
        return $this->data;
    }
    
    /**
     * Returns the current element.
     * @return mixed
     * @internal
     */
    function current() {
        return \current($this->data);
    }
    
    /**
     * Fetch the key from the current element.
     * @return mixed
     * @internal
     */
    function key() {
        return \key($this->data);
    }
    
    /**
     * Advances the internal pointer.
     * @return mixed|false
     * @internal
     */
    function next() {
        return \next($this->data);
    }
    
    /**
     * Resets the internal pointer.
     * @return mixed|false
     * @internal
     */
    function rewind() {
        return \reset($this->data);
    }
    
    /**
     * Checks if current position is valid.
     * @return bool
     * @internal
     */
    function valid() {
        return (\key($this->data) !== null);
    }
    
    /**
     * Sets a key-value pair.
     * @param mixed  $key
     * @param mixed  $value
     * @return $this
     */
    function set($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }
    
    /**
     * Removes an item from the collection by its key.
     * @param mixed  $key
     * @return $this
     */
    function delete($key) {
        $this->data[$key] = null;
        unset($this->data[$key]);
        return $this;
    }
    
    /**
     * Clears the Collection.
     * @return $this
     */
    function clear() {
        $this->data = array();
        return $this;
    }
    
    /**
     * Returns all items.
     * @return mixed[]
     */
    function all() {
        return $this->data;
    }
    
    /**
     * Breaks the collection into multiple, smaller chunks of a given size. Returns a new Collection.
     * @param int  $numitems
     * @param bool $preserve_keys
     * @return \CharlotteDunois\Collect\Collection
     */
    function chunk(int $numitems, bool $preserve_keys = false) {
        return (new self(\array_chunk($this->data, $numitems, $preserve_keys)));
    }
    
    /**
     * Returns the total number of items in the collection.
     * @return int
     */
    function count() {
        return \count($this->data);
    }
    
    /**
     * Returns a copy of itself.
     * @return \CharlotteDunois\Collect\Collection
     */
    function copy() {
        return (new self($this->data));
    }
    
    /**
     * Compares the collection against another collection or a plain PHP array based on its value. Returns a new Collection.
     * @param mixed[]|\CharlotteDunois\Collect\Collection  $arr
     * @return \CharlotteDunois\Collect\Collection
     */
    function diff($arr) {
        if($arr instanceof self) {
            $arr = $arr->all();
        }
        
        return (new self(\array_diff($this->data, $arr)));
    }
    
    /**
     * Compares the collection against another collection or a plain PHP array based on its key. Returns a new Collection.
     * @param mixed[]|\CharlotteDunois\Collect\Collection  $arr
     * @return \CharlotteDunois\Collect\Collection
     */
    function diffKeys($arr) {
        if($arr instanceof self) {
            $arr = $arr->all();
        }
        
        return (new self(\array_diff_key($this->data, $arr)));
    }
    
    /**
     * Iterates over the items in the collection and passes each item to a given callback. Returning `false` in the callback will stop the processing.
     * @param callable  $closure  Callback specification: `function ($value, $key): bool`
     * @return $this
     */
    function each(callable $closure) {
        foreach($this->data as $key => $val) {
            $feed = $closure($val, $key);
            if($feed === false) {
                break;
            }
        }
        
        return $this;
    }
    
    /**
     * Returns true if all elements pass the given truth test.
     * @param callable $closure  Callback specification: `function ($value, $key): bool`
     * @return bool
     */
    function every(callable $closure) {
        foreach($this->data as $key => $val) {
            if(!$closure($val, $key)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Returns all items in the collection except for those with the specified keys. Returns a new Collection.
     * @param mixed[]  $keys
     * @return \CharlotteDunois\Collect\Collection
     */
    function except(array $keys) {
        $new = array();
        foreach($this->data as $key => $val) {
            if(!\in_array($key, $keys, true)) {
                $new[$key] = $val;
            }
        }
        
        return (new self($new));
    }
    
    /**
     * Filters the collection by a given callback, keeping only those items that pass a given truth test. Returns a new Collection.
     * @param callable  $closure  Callback specification: `function ($value, $key): bool`
     * @return \CharlotteDunois\Collect\Collection
     */
    function filter(callable $closure) {
        $new = array();
        foreach($this->data as $key => $val) {
            $feed = (bool) $closure($val, $key);
            if($feed) {
                $new[$key] = $val;
            }
        }
        
        return (new self($new));
    }
    
    /**
     * Returns the first element in the collection that passes a given truth test.
     * @param callable|null  $closure  Callback specification: `function ($value, $key): bool`
     * @return mixed|null
     */
    function first(callable $closure = null) {
        if($closure === null) {
            if(empty($this->data)) {
                return null;
            }
            
            $keys = \array_keys($this->data);
            return ($this->data[$keys[0]] ?? null);
        }
        
        foreach($this->data as $key => $val) {
            $feed = (bool) $closure($val, $key);
            if($feed) {
                return $val;
            }
        }
        
        return null;
    }
    
    /**
     * Flattens a multi-dimensional collection into a single dimension. Returns a new Collection.
     * @param int  $depth
     * @return \CharlotteDunois\Collect\Collection
     */
    function flatten(int $depth = 0) {
        $data = $this->flattenDo($this->data, $depth);
        return (new self($data));
    }
    
    /**
     * Returns the item for a given key. If the key does not exist, null is returned.
     * @param mixed  $key
     * @return mixed|null
     */
    function get($key) {
        return ($this->data[$key] ?? null);
    }
    
    /**
     * Groups the collection's items by a given key. Returns a new Collection.
     * @param callable|mixed  $column  Callback specification: `function ($value, $key): mixed`
     * @return \CharlotteDunois\Collect\Collection
     */
    function groupBy($column) {
        if($column === null || $column === '') {
            return $this;
        }
        
        $new = array();
        foreach($this->data as $key => $val) {
            if(\is_callable($column)) {
                $key = $column($val, $key);
            } elseif(\is_array($val)) {
                $key = $val[$column];
            } elseif(\is_object($val)) {
                $key = $val->$column;
            }
            
            $new[$key][] = $val;
        }
        
        return (new self($new));
    }
    
    /**
     * Determines if a given key exists in the collection.
     * @param mixed  $key
     * @return bool
     */
    function has($key) {
        return isset($this->data[$key]);
    }
    
    /**
     * Joins the items in a collection. Its arguments depend on the type of items in the collection.
     * If the collection contains arrays or objects, you should pass the key of the attributes you wish to join, and the "glue" string you wish to place between the values.
     * @param mixed  $col
     * @param string $glue
     * @return string
     * @throws \BadMethodCallException
     */
    function implode($col, string $glue = ', ') {
        $data = '';
        
        foreach($this->data as $key => $val) {
            if(\is_array($val)) {
                if(!isset($val[$col])) {
                    throw new \BadMethodCallException('Specified key "'.$col.'" does not exist on array');
                }
                
                $data .= $glue.$val[$col];
            } elseif(\is_object($val)) {
                if(!isset($val->$col)) {
                    throw new \BadMethodCallException('Specified key "'.$col.'" does not exist on object');
                }
                
                $data .= $glue.$val->$col;
            } else {
                $data .= $glue.$val;
            }
        }
        
        return \substr($data, \strlen($glue));
    }
    
    /**
     * Returns the position of the given value in the collection. Returns null if the given value couldn't be found.
     * @param mixed  $value
     * @return int|null
     */
    function indexOf($value) {
        $i = 0;
        
        foreach($this->data as $val) {
            if($val === $value) {
                return $i;
            }
            
            $i++;
        }
        
        return null;
    }
    
    /**
     * Removes any values that are not present in the given array or collection. Returns a new Collection.
     * @param mixed[]|\CharlotteDunois\Collect\Collection  $arr
     * @return \CharlotteDunois\Collect\Collection
     */
    function intersect($arr) {
        if($arr instanceof self) {
            $arr = $arr->all();
        }
        
        return (new self(\array_intersect($this->data, $arr)));
    }
    
    /**
     * Returns all of the collection's keys. Returns a new Collection.
     * @return \CharlotteDunois\Collect\Collection
     */
    function keys() {
        return (new self(\array_keys($this->data)));
    }
    
    /**
     * Returns the last element in the collection that passes a given truth test.
     * @param callable|null  $closure  Callback specification: `function ($value, $key): bool`
     * @return mixed|null
     */
    function last(callable $closure = null) {
        if($closure === null) {
            if(empty($this->data)) {
                return null;
            }
            
            $keys = \array_keys($this->data);
            return $this->data[$keys[(\count($keys) - 1)]];
        }
        
        $data = null;
        foreach($this->data as $key => $val) {
            $feed = $closure($val, $key);
            if($feed) {
                $data = $val;
            }
        }
        
        return $data;
    }
    
    /**
     * Iterates through the collection and passes each value to the given callback. The callback is free to modify the item and return it, thus forming a new collection of modified items.
     * @param callable|null  $closure  Callback specification: `function ($value, $key): mixed`
     * @return \CharlotteDunois\Collect\Collection
     */
    function map(callable $closure) {
        $keys = \array_keys($this->data);
        $items = \array_map($closure, $this->data, $keys);
        
        return (new self(\array_combine($keys, $items)));
    }
    
    /**
     * Return the maximum value of a given key.
     * @param mixed|null  $key
     * @return int
     */
    function max($key = null) {
        if($key !== null) {
            $data = \array_column($this->data, $key);
        } else {
            $data = $this->data;
        }
        
        return \max($data);
    }
    
    /**
     * Return the minimum value of a given key.
     * @param mixed|null  $key
     * @return int
     */
    function min($key = null) {
        if($key !== null) {
            $data = \array_column($this->data, $key);
        } else {
            $data = $this->data;
        }
        
        return \min($data);
    }
    
    /**
     * Merges the given collection into this collection, resulting in a new collection.
     * Any string key in the given collection matching a string key in this collection will overwrite the value in this collection.
     * @param \CharlotteDunois\Collect\Collection  $collection
     * @return \CharlotteDunois\Collect\Collection
     */
    function merge(\CharlotteDunois\Collect\Collection $collection) {
        return (new self(\array_merge($this->data, $collection->all())));
    }
    
    /**
     * Creates a new collection consisting of every n-th element.
     * @param int  $nth
     * @param int  $offset
     * @return \CharlotteDunois\Collect\Collection
     * @throws \InvalidArgumentException
     */
    function nth(int $nth, int $offset = 0) {
        if($nth <= 0) {
            throw new \InvalidArgumentException('nth must be a non-zero positive integer');
        }
        
        $new = array();
        $size = \count($this->data);
        
        for($i = $offset; $i < $size; $i += $nth) {
            $new[] = $this->data[$i];
        }
        
        return (new self($new));
    }
    
    /**
     * Returns the items in the collection with the specified keys. Returns a new Collection.
     * @param mixed[]  $keys
     * @return \CharlotteDunois\Collect\Collection
     */
    function only(array $keys) {
        $new = array();
        foreach($this->data as $key => $val) {
            if(\in_array($key, $keys, true)) {
                $new[$key] = $val;
            }
        }
        
        return (new self($new));
    }
    
    /**
     * Partitions the collection into two collections where the first collection contains the items that passed and the second contains the items that failed.
     * @param callable  $closure  Callback specification: `function ($value, $key): bool`
     * @return \CharlotteDunois\Collect\Collection[]
     */
    function partition(callable $closure) {
        $collection1 = new self();
        $collection2 = new self();
        
        foreach($this->data as $key => $val) {
            if($closure($val, $key)) {
                $collection1->set($key, $val);
            } else {
                $collection2->set($key, $val);
            }
        }
        
        return array($collection1, $collection2);
    }
    
    /**
     * Return the values from a single column in the input array. Returns a new Collection.
     * @param mixed    $key
     * @param mixed    $index
     * @return \CharlotteDunois\Collect\Collection
     */
    function pluck($key, $index = null) {
        $data = array();
        $i = 0;
        
        foreach($this->data as $v) {
            $k = ($index ?
                    (\is_array($v) ?
                        (\array_key_exists($index, $v) ? $v[$index] : $i)
                    : (\is_object($v) ?
                            (\property_exists($v, $index) ?
                                $v->$index : $i)
                        : $i))
                    : $i);
            
            if(\is_array($v) && \array_key_exists($key, $v)) {
                $data[$k] = $v[$key];
            } elseif(\is_object($v) && \property_exists($v, $key)) {
                $data[$k] = $v->$key;
            }
            
            $i++;
        }
        
        return (new self($data));
    }
    
    /**
     * Returns one random item, or multiple random items inside a Collection, from the Collection. Returns a new Collection.
     * @param int  $num
     * @return \CharlotteDunois\Collect\Collection
     */
    function random(int $num = 1) {
        $rand = \array_rand($this->data, $num);
        if(!\is_array($rand)) {
            $rand = array($rand);
        }
        
        $col = new self();
        foreach($rand as $key) {
            $col->set($key, $this->data[$key]);
        }
        
        return $col;
    }
    
    /**
     * Reduces the collection to a single value, passing the result of each iteration into the subsequent iteration.
     * @param callable   $closure  Callback specification: `function ($carry, $value): mixed`
     * @param mixed|null $carry
     * @return mixed|null|void
     */
    function reduce(callable $closure, $carry = null) {
        foreach($this->data as $val) {
            $carry = $closure($carry, $val);
        }
        
        return $carry;
    }
    
    /**
     * Reverses the order of the collection's items. Returns a new Collection.
     * @param bool $preserve_keys
     * @return \CharlotteDunois\Collect\Collection
     */
    function reverse(bool $preserve_keys = false) {
        return (new self(\array_reverse($this->data, $preserve_keys)));
    }
    
    /**
     * Searches the collection for the given value and returns its key if found. If the item is not found, false is returned.
     * @param mixed   $needle
     * @param bool    $strict
     * @return mixed|bool
     */
    function search($needle, bool $strict = true) {
        return \array_search($needle, $this->data, $strict);
    }
    
    /**
     * Randomly shuffles the items in the collection. Returns a new Collection.
     * @return \CharlotteDunois\Collect\Collection
     */
    function shuffle() {
        $data = $this->data;
        \shuffle($data);
        
        return (new self($data));
    }
    
    /**
     * Returns a slice of the collection starting at the given index. Returns a new Collection.
     * @param int      $offset
     * @param int      $limit
     * @param bool     $preserve_keys
     * @return \CharlotteDunois\Collect\Collection
     */
    function slice(int $offset, int $limit = null, bool $preserve_keys = false) {
        $data = $this->data;
        return (new self(\array_slice($data, $offset, $limit, $preserve_keys)));
    }
    
    /**
     * Returns true if at least one element passes the given truth test.
     * @param callable $closure  Callback specification: `function ($value, $key): bool`
     * @return bool
     */
    function some(callable $closure) {
        foreach($this->data as $key => $val) {
            if($closure($val, $key)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Sorts the collection, using sort behaviour flags. Returns a new Collection.
     * @param bool  $descending
     * @param int   $options
     * @return \CharlotteDunois\Collect\Collection
     */
    function sort(bool $descending = false, int $options = \SORT_REGULAR) {
        $data = $this->data;
        
        if($descending) {
            \arsort($data, $options);
        } else {
            \asort($data, $options);
        }
        
        return (new self($data));
    }
    
    /**
     * Sorts the collection by key, using sort behaviour flags. Returns a new Collection.
     * @param bool  $descending
     * @param int   $options
     * @return \CharlotteDunois\Collect\Collection
     */
    function sortKey(bool $descending = false, int $options = \SORT_REGULAR) {
        $data = $this->data;
        
        if($descending) {
            \krsort($data, $options);
        } else {
            \ksort($data, $options);
        }
        
        return (new self($data));
    }
    
    /**
     * Sorts the collection using a custom sorting function. Returns a new Collection.
     * @param callable  $closure  Callback specification: `function ($a, $b): int`
     * @return \CharlotteDunois\Collect\Collection
     */
    function sortCustom(callable $closure) {
        $data = $this->data;
        \uasort($data, $closure);
        
        return (new self($data));
    }
    
    /**
     * Sorts the collection by key using a custom sorting function. Returns a new Collection.
     * @param callable  $closure  Callback specification: `function ($a, $b): int`
     * @return \CharlotteDunois\Collect\Collection
     */
    function sortCustomKey(callable $closure) {
        $data = $this->data;
        \uksort($data, $closure);
        
        return (new self($data));
    }
    
    /**
     * Returns all of the unique items in the collection. Returns a new Collection.
     * @param mixed|null  $key
     * @param int         $options
     * @return \CharlotteDunois\Collect\Collection
     * @throws \BadMethodCallException
     */
    function unique($key, $options = \SORT_REGULAR) {
        if($key === null) {
            return (new self(\array_unique($this->data, $options)));
        }
        
        $exists = array();
        return $this->filter(function ($item) use ($key, &$exists) {
            if(\is_array($item)) {
                if(!isset($item[$key])) {
                    throw new \BadMethodCallException('Specified key "'.$key.'" does not exist on array');
                }
                
                $id = $item[$key];
            } elseif(\is_object($item)) {
                if(!isset($item->$key)) {
                    throw new \BadMethodCallException('Specified key "'.$key.'" does not exist on object');
                }
                
                $id = $item->$key;
            } else {
                $id = $item;
            }
            
            if(\in_array($id, $exists, true)) {
                return false;
            }
            
            $exists[] = $id;
            return true;
        });
    }
    
    /**
     * Returns a new collection with the keys reset to consecutive integers.
     * @return \CharlotteDunois\Collect\Collection
     */
    function values() {
        return (new self(\array_values($this->data)));
    }
    
    /**
     * @param array  $array
     * @param int    $depth
     * @param int    $inDepth
     * @return array
     * @internal
     */
    protected function flattenDo(array $array, int $depth, int $inDepth = 0) {
        $data = array();
        foreach($array as $val) {
            if(\is_array($val) && ($depth == 0 || $depth > $inDepth)) {
                $data = \array_merge($data, $this->flattenDo($val, $depth, ($inDepth + 1)));
            } elseif($val instanceof self) {
                $data = \array_merge($data, $val->flatten($depth)->all());
            } else {
                $data[] = $val;
            }
        }
        
        return $data;
    }
}
