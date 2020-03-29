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
 * WS Handler interface.
 * @internal
 */
interface WSHandlerInterface {
    /**
     * Constructor.
     */
    function __construct(\CharlotteDunois\Yasmin\WebSocket\WSHandler $wshandler);
    
    /**
     * Handles packets.
     * @return void
     */
    function handle(\CharlotteDunois\Yasmin\WebSocket\WSConnection $ws, $packet): void;
}
