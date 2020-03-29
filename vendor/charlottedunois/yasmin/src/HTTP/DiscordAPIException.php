<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\HTTP;

/**
 * Represents an error from the Discord HTTP API.
 */
class DiscordAPIException extends \CharlotteDunois\Yasmin\DiscordException {
    /**
     * The path of the request relative to the HTTP endpoint.
     * @var string
     */
    public $path;
    
    /**
     * Constructor.
     * @param string $path
     * @param array  $error
     */
    function __construct($path, array $error) {
        $this->path = $path;
        $flattened = \implode('\n', self::flattenErrors(($error['errors'] ?? $error)));
        
        parent::__construct((!empty($error['message']) && !empty($flattened) ? $error['message'].\PHP_EOL.$flattened : ($error['message'] ?? $flattened)), (int) ($error['code'] ?? 0));
    }
    
    /**
     * Flattens an errors object returned from the API into an array.
     * @param array   $obj  Discord error object
     * @param string  $key  Used internally to determine key names of nested fields
     * @return string[]
     * @internal
     */
    static function flattenErrors($obj, $key = '') {
        $messages = array();
        
        foreach($obj as $k => $val) {
            if($k === 'message') {
                continue;
            }
            
            $newKey = $k;
            if($key) {
                if(\is_numeric($k)) {
                    $newKey = $key.'.'.$k;
                } else {
                    $newKey = $key.'['.$k.']';
                }
            }
            
            if(isset($val['_errors'])) {
                $messages[] = $newKey.': '.\implode(' ', \array_map(function ($element) {
                    return $element['message'];
                }, $val['_errors']));
            } else if(isset($val['code']) || isset($val['message'])) {
                $messages[] = \trim(($val['code'] ?? '').': '.($val['message'] ?? ''));
            } else if(\is_array($val)) {
                $messages = \array_merge($messages, self::flattenErrors($val, $newKey));
            } else {
                $messages[] = $val;
            }
        }

        return $messages;
    }
}
