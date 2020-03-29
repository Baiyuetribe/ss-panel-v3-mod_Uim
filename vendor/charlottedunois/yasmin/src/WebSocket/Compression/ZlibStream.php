<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\WebSocket\Compression;

/**
 * Handles WS compression.
 * @internal
 */
class ZlibStream implements \CharlotteDunois\Yasmin\Interfaces\WSCompressionInterface {
    /**
     * @var resource
     */
    protected $context;
    
    /**
     * Checks if the system supports it.
     * @return void
     * @throws \Exception
     */
    static function supported(): void {
        if(!\function_exists('\inflate_init')) {
            throw new \RuntimeException('Zlib is not supported by this PHP installation');
        }
    }
    
    /**
     * Returns compression name (for gateway query string).
     * @return string
     */
    static function getName(): string {
        return 'zlib-stream';
    }
    
    /**
     * Returns a boolean for the OP code 2 IDENTIFY packet 'compress' parameter. The parameter is for payload compression.
     * @return bool
     */
    static function isPayloadCompression(): bool {
        return false;
    }
    
    /**
     * Initializes the context.
     * @return void
     * @throws \RuntimeException
     */
    function init(): void {
        $this->context = \inflate_init(\ZLIB_ENCODING_DEFLATE);
        if(!$this->context) {
            throw new \RuntimeException('Unable to initialize Zlib Inflate');
        }
    }
    
    /**
     * Destroys the context.
     * @return void
     */
    function destroy(): void {
        $this->context = null;
    }
    
    /**
     * Decompresses data.
     * @param string  $data
     * @return string
     * @throws \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException
     */
    function decompress(string $data): string {
        if(!$this->context) {
            throw new \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException('No inflate context initialized');
        }
        
        $uncompressed = \inflate_add($this->context, $data);
        if($uncompressed === false) {
            throw new \CharlotteDunois\Yasmin\WebSocket\DiscordGatewayException('The inflate context was unable to decompress the data');
        }
        
        return $uncompressed;
    }
}
