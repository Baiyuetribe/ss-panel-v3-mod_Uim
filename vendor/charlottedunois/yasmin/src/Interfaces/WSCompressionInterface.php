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
 * Interface for WS compressions. This is used internally.
 */
interface WSCompressionInterface {
    /**
     * Checks if the system supports it.
     * @return void
     * @throws \Exception
     */
    static function supported(): void;
    
    /**
     * Returns compression name (for gateway query string).
     * @return string
     */
    static function getName(): string;
    
    /**
     * Returns a boolean for the OP code 2 IDENTIFY packet 'compress' parameter. The parameter is for payload compression.
     * @return bool
     */
    static function isPayloadCompression(): bool;
    
    /**
     * Initializes the context.
     * @return void
     * @throws \Exception
     */
    function init(): void;
    
    /**
     * Destroys the context.
     * @return void
     */
    function destroy(): void;
    
    /**
     * Decompresses data.
     * @param string  $data
     * @return string
     * @throws \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException
     */
    function decompress(string $data): string;
}
