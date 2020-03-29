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
 * Interface for WS encodings. This is used internally.
 */
interface WSEncodingInterface {
    /**
     * Returns encoding name (for gateway query string).
     * @return string
     */
    function getName(): string;
    
    /**
     * Checks if the system supports it.
     * @return void
     * @throws \RuntimeException
     */
    static function supported(): void;
    
    /**
     * Decodes data.
     * @param string  $data
     * @return mixed
     * @throws \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException
     */
    function decode(string $data);
    
    /**
     * Encodes data.
     * @param mixed  $data
     * @return string
     * @throws \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException
     */
    function encode($data): string;
    
    /**
     * Prepares the data to be sent.
     * @param string  $data
     * @return \Ratchet\RFC6455\Messaging\Message
     */
    function prepareMessage(string $data): \Ratchet\RFC6455\Messaging\Message;
}
