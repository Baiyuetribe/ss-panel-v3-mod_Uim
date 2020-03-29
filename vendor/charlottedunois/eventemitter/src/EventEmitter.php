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
 * Our Event Emitter, equivalent to Node.js' Event Emitter.
 */
class EventEmitter implements EventEmitterInterface {
    use EventEmitterTrait;
}
