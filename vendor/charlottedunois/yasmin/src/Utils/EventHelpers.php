<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Utils;

/**
 * Event Helper methods.
 */
class EventHelpers {
    /**
     * Waits for a specific type of event to get emitted. Additional filter may be applied to look for a specific event (invoked as `$filter(...$args)`). Resolves with an array of arguments (from the event).
     *
     * Options may be:
     * ```
     * array(
     *     'time' => int, (if the event hasn't been found yet, this will define a timeout (in seconds) after which the promise gets rejected)
     * )
     * ```
     *
     * @param \CharlotteDunois\Events\EventEmitterInterface  $emitter
     * @param string                                         $event
     * @param callable|null                                  $filter
     * @param array                                          $options
     * @return \React\Promise\ExtendedPromiseInterface  This promise is cancellable.
     * @throws \RangeException          The exception the promise gets rejected with, if waiting times out.
     * @throws \OutOfBoundsException    The exception the promise gets rejected with, if the promise gets cancelled.
     */
    static function waitForEvent($emitter, string $event, ?callable $filter = null, array $options = array()) {
        $options['max'] = 1;
        $options['time'] = $options['time'] ?? 0;
        $options['errors'] = array('max');
        
        $collector = new \CharlotteDunois\Yasmin\Utils\Collector($emitter, $event, function (...$a) {
            return [ 0, $a ];
        }, $filter, $options);
        
        return $collector->collect()->then(function (\CharlotteDunois\Collect\Collection $bucket) {
            return $bucket->first();
        });
    }
}
