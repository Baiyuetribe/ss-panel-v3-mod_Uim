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
 * Thrown when an error event gets emitted, but not handled (aka no listeners).
 */
class UnhandledErrorException extends \RuntimeException {
    
}
