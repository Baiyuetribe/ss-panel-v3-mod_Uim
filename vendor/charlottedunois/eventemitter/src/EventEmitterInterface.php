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
 * The event emitter interface.
 */
interface EventEmitterInterface {
    /**
     * Attach a listener to an event.
     * @param string    $event
     * @param callable  $listener
     * @return $this
     */
    function on(string $event, callable $listener);
    
    /**
     * Attach a listener to an event, for exactly once.
     * @param string    $event
     * @param callable  $listener
     * @return $this
     */
    function once(string $event, callable $listener);
    
    /**
     * Remove specified listener from an event.
     * @param string    $event
     * @param callable  $listener
     * @return $this
     */
    function removeListener(string $event, callable $listener);
    
    /**
     * Remove all listeners from an event (or all listeners).
     * @param string|null  $event
     * @return $this
     */
    function removeAllListeners(?string $event = null);
    
    /**
     * Get listeners for a specific events, or all listeners.
     * @param string|null  $event
     * @return array
     */
    function listeners(?string $event = null);
    
    /**
     * Emits an event, catching all exceptions and emitting an error event for these exceptions.
     * @param string  $event
     * @param mixed   ...$arguments
     * @return void
     */
    function emit(string $event, ...$arguments);
}
