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
 * Data Helper methods.
 */
class DataHelpers {
    /**
     * Resolves a color to an integer.
     * @param array|int|string  $color
     * @return int
     * @throws \InvalidArgumentException
     */
    static function resolveColor($color) {
        if(\is_int($color)) {
            return $color;
        }
        
        if(!\is_array($color)) {
            return \hexdec(((string) $color));
        }
        
        if(\count($color) < 1) {
            throw new \InvalidArgumentException('Color "'.\var_export($color, true).'" is not resolvable');
        }
        
        return (($color[0] << 16) + (($color[1] ?? 0) << 8) + ($color[2] ?? 0));
    }
    
    /**
     * Makes a DateTime instance from an UNIX timestamp and applies the default timezone.
     * @param int $timestamp
     * @return \DateTime
     */
    static function makeDateTime(int $timestamp) {
        $zone = new \DateTimeZone(\date_default_timezone_get());
        return (new \DateTime('@'.$timestamp))->setTimezone($zone);
    }
    
    /**
     * Turns input into a base64-encoded data URI.
     * @param string  $data
     * @return string
     * @throws \InvalidArgumentException
     */
    static function makeBase64URI(string $data) {
        $img = \getimagesizefromstring($data);
        if(!$img) {
            throw new \InvalidArgumentException('Bad input data');
        }
        
        return 'data:'.$img['mime'].';base64,'.\base64_encode($data);
    }
    
    /**
     * Typecasts the variable to the type, if not null.
     * @param mixed   &$variable
     * @param string  $type
     * @return mixed|null
     * @throws \InvalidArgumentException
     */
    static function typecastVariable($variable, string $type) {
        if($variable === null) {
            return null;
        }
        
        switch($type) {
            case 'array':
                $variable = (array) $variable;
            break;
            case 'bool':
                $variable = (bool) $variable;
            break;
            case 'float':
                $variable = (float) $variable;
            break;
            case 'int':
                $variable = (int) $variable;
            break;
            case 'string':
                $variable = (string) $variable;
            break;
            default:
                throw new \InvalidArgumentException('Unsupported type "'.$type.'"');
            break;
        }
        
        return $variable;
    }
    
    /**
     * Converts the input to the specified options.
     *
     * Options is
     * ```
     * array(
     *     key => null|newKey|array('key' => newKey, 'type' => string, 'parse' => callable)
     * )
     * ```
     * @param array  $input
     * @param array  $options
     * @return array
     */
    static function applyOptions(array $input, array $options) {
        $data = array();
        
        foreach($input as $key => $val) {
            if(!isset($options[$key])) {
                continue;
            }
            
            if(\is_array($options[$key])) {
                if(!empty($options[$key]['parse'])) {
                    $call = $options[$key]['parse'];
                    $val = $call($val);
                }
                
                if(!empty($options[$key]['type'])) {
                    $val = self::typecastVariable($val, $options[$key]['type']);
                }
                
                $key = $options[$key]['key'] ?? $key;
            } elseif(!empty($options[$key])) {
                $key = $options[$key];
            }
            
            $data[$key] = $val;
        }
        
        return $data;
    }
}
