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
 * Our Event Emitter Error Trait. This one throws an exception if there is not an error event listener when an error event gets emitted.
 */
trait EventEmitterErrorTrait {
    use EventEmitterTrait;
    
    /**
     * Emits an event, catching all exceptions and emitting an error event for these exceptions.
     * @param string  $event
     * @param mixed   ...$arguments
     * @return void
     * @throws \CharlotteDunois\Events\UnhandledErrorException  Thrown when an error event goes unhandled.
     */
    function emit(string $event, ...$arguments) {
        $errorEmpty = ($event === 'error');
        
        if(!empty($this->listeners[$event])) {
            $errorEmpty = false;
            
            foreach($this->listeners[$event] as $listener) {
                $listener(...$arguments);
            }
        }
        
        if(!empty($this->onceListeners[$event])) {
            $errorEmpty = false;
            
            $listeners = $this->onceListeners[$event];
            unset($this->onceListeners[$event]);
            
            foreach($listeners as $listener) {
                $listener(...$arguments);
            }
        }
        
        if($errorEmpty) {
            throw new \CharlotteDunois\Events\UnhandledErrorException('Unhandled error event', 0, (($arguments[0] ?? null) instanceof \Throwable ? $arguments[0] : null));
        }
    }
}
