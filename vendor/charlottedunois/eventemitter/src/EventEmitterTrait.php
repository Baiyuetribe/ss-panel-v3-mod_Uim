<?php
/**
 * EventEmitter
 * Copyright 2018-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/EventEmitter/blob/master/LICENSE
*/

namespace CharlotteDunois\Events;

/**
 * Our Event Emitter Trait.
 */
trait EventEmitterTrait {
    /**
     * @var array
     * @internal
     */
    protected $listeners = array();
    
    /**
     * @var array
     * @internal
     */
    protected $onceListeners = array();
    
    /**
     * Attach a listener to an event.
     * @param string    $event
     * @param callable  $listener
     * @return $this
     */
    function on(string $event, callable $listener) {
        if(!isset($this->listeners[$event])) {
            $this->listeners[$event] = array();
        }
        
        $this->listeners[$event][] = $listener;
        
        return $this;
    }
    
    /**
     * Attach a listener to an event, for exactly once.
     * @param string    $event
     * @param callable  $listener
     * @return $this
     */
    function once(string $event, callable $listener) {
        if(!isset($this->onceListeners[$event])) {
            $this->onceListeners[$event] = array();
        }
        
        $this->onceListeners[$event][] = $listener;
        
        return $this;
    }
    
    /**
     * Remove specified listener from an event.
     * @param string    $event
     * @param callable  $listener
     * @return $this
     */
    function removeListener(string $event, callable $listener) {
        if(isset($this->listeners[$event])) {
            $index = \array_search($listener, $this->listeners[$event], true);
            if($index !== false) {
                unset($this->listeners[$event][$index]);
                if(\count($this->listeners[$event]) === 0) {
                    unset($this->listeners[$event]);
                }
            }
        }
        
        if(isset($this->onceListeners[$event])) {
            $index = \array_search($listener, $this->onceListeners[$event], true);
            if($index !== false) {
                unset($this->onceListeners[$event][$index]);
                if(\count($this->onceListeners[$event]) === 0) {
                    unset($this->onceListeners[$event]);
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Remove all listeners from an event (or all listeners).
     * @param string|null  $event
     * @return $this
     */
    function removeAllListeners(?string $event = null) {
        if($event !== null) {
            unset($this->listeners[$event]);
        } else {
            $this->listeners = [];
        }
        
        if($event !== null) {
            unset($this->onceListeners[$event]);
        } else {
            $this->onceListeners = [];
        }
        
        return $this;
    }

    /**
     * Get listeners for a specific events, or all listeners.
     * @param string|null  $event
     * @return array
     */
    function listeners(?string $event = null) {
        if($event === null) {
            $events = array();
            $eventNames = \array_unique(\array_merge(\array_keys($this->listeners), \array_keys($this->onceListeners)));
            
            foreach($eventNames as $eventName) {
                $events[$eventName] = \array_merge(
                    (isset($this->listeners[$eventName]) ? $this->listeners[$eventName] : array()),
                    (isset($this->onceListeners[$eventName]) ? $this->onceListeners[$eventName] : array())
                );
            }
            
            return $events;
        }
        
        return \array_merge(
            (isset($this->listeners[$event]) ? $this->listeners[$event] : array()),
            (isset($this->onceListeners[$event]) ? $this->onceListeners[$event] : array())
        );
    }
    
    /**
     * Emits an event, catching all exceptions and emitting an error event for these exceptions.
     * @param string  $event
     * @param mixed   ...$arguments
     * @return void
     */
    function emit(string $event, ...$arguments) {
        if(!empty($this->listeners[$event])) {
            foreach($this->listeners[$event] as $listener) {
                $listener(...$arguments);
            }
        }
        
        if(!empty($this->onceListeners[$event])) {
            $listeners = $this->onceListeners[$event];
            unset($this->onceListeners[$event]);
            
            foreach($listeners as $listener) {
                $listener(...$arguments);
            }
        }
    }
}
