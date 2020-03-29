<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\WebSocket\Encoding;

/**
 * Handles WS encoding.
 * @internal
 */
class Json implements \CharlotteDunois\Yasmin\Interfaces\WSEncodingInterface {
    /**
     * Returns encoding name (for gateway query string).
     * @return string
     */
    function getName(): string {
        return 'json';
    }
    
    /**
     * Checks if the system supports it.
     * @return void
     * @throws \RuntimeException
     */
    static function supported(): void {
        // Nothing to check
    }
    
    /**
     * Decodes data.
     * @param string  $data
     * @return mixed
     * @throws \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException
     */
    function decode(string $data) {
        $msg = \json_decode($data, true);
        if($msg === null && \json_last_error() !== \JSON_ERROR_NONE) {
            throw new \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException('The JSON decoder was unable to decode the data. Error: '.\json_last_error_msg());
        }
        
        return $msg;
    }
    
    /**
     * Encodes data.
     * @param mixed  $data
     * @return string
     * @throws \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException
     */
    function encode($data): string {
        $msg = \json_encode($data);
        if($msg === false && \json_last_error() !== \JSON_ERROR_NONE) {
            throw new \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException('The JSON encoder was unable to encode the data. Error: '.\json_last_error_msg());
        }
        
        return $msg;
    }
    
    /**
     * Prepares the data to be sent.
     * @param string  $data
     * @return \Ratchet\RFC6455\Messaging\Message
     */
    function prepareMessage(string $data): \Ratchet\RFC6455\Messaging\Message {
        $frame = new \Ratchet\RFC6455\Messaging\Frame($data, true, \Ratchet\RFC6455\Messaging\Frame::OP_TEXT);
        
        $msg = new \Ratchet\RFC6455\Messaging\Message();
        $msg->addFrame($frame);
        
        return $msg;
    }
}
