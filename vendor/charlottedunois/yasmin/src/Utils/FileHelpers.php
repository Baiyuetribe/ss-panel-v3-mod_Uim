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
 * File Helper methods.
 */
class FileHelpers {
    /**
     * @var \React\EventLoop\LoopInterface
     */
    protected static $loop;
    
    /**
     * @var \React\Filesystem\FilesystemInterface|null
     */
    protected static $filesystem;
    
    /**
     * Sets the Event Loop.
     * @param \React\EventLoop\LoopInterface  $loop
     * @return void
     * @internal
     */
    static function setLoop(\React\EventLoop\LoopInterface $loop) {
        self::$loop = $loop;
        
        if(self::$filesystem === null) {
            $adapters = \React\Filesystem\Filesystem::getSupportedAdapters();
            if(!empty($adapters)) {
                self::$filesystem = \React\Filesystem\Filesystem::create($loop);
            }
        }
    }
    
    /**
     * Returns the stored React Filesystem instance, or null.
     * @return \React\Filesystem\FilesystemInterface|false|null
     */
    static function getFilesystem() {
        return self::$filesystem;
    }
    
    /**
     * Sets the React Filesystem instance, or disables it.
     * @param \React\Filesystem\FilesystemInterface|null  $filesystem
     * @return void
     */
    static function setFilesystem(?\React\Filesystem\FilesystemInterface $filesystem) {
        if($filesystem === null) {
            $filesystem = false;
        }
        
        self::$filesystem = $filesystem;
    }
    
    /**
     * Resolves filepath and URL into file data - returns it if it's neither. Resolves with a string.
     * @param string  $file
     * @return \React\Promise\ExtendedPromiseInterface
     */
    static function resolveFileResolvable(string $file) {
        $rfile = @\realpath($file);
        if($rfile) {
            if(self::$filesystem) {
                return self::$filesystem->getContents($file);
            }
            
            return \React\Promise\resolve(\file_get_contents($rfile));
        } elseif(\filter_var($file, FILTER_VALIDATE_URL)) {
            return \CharlotteDunois\Yasmin\Utils\URLHelpers::resolveURLToData($file);
        }
        
        return \React\Promise\reject(new \RuntimeException('Given file is not resolvable'));
    }
}
