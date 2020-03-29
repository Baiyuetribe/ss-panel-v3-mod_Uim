<?php
/**
 * Validator
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Validator/blob/master/LICENSE
**/

namespace CharlotteDunois\Validation\Rules;

/**
 * Name: `dimensions`
 *
 * This rule ensures a specific (upload) field contains an image with the required dimensions. The following options exist: `min_width`, `min_height`, `width`, `height`, `max_width`, `max_height`, `ratio`.
 * Multiple options can be used using comma separators. Usage: `dimensions:OPTION=VALUE`
 */
class Dimensions implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(isset($_FILES[$key])) {
            if(!\file_exists($_FILES[$key]['tmp_name'])) {
                return 'formvalidator_make_invalid_file';
            }
            
            $size = \getimagesize($_FILES[$key]['tmp_name']);
        } else {
            if(!$exists) {
                return false;
            }
            
            $size = @\getimagesizefromstring($value);
        }
        
        if(!$size) {
            return 'formvalidator_make_invalid_file';
        }
        
        $n = \explode(',', $options);
        foreach($n as $x) {
            $k = \explode('=', $x);
            switch($k[0]) {
                case 'min_width': // @codeCoverageIgnore
                    if($k[1] > $size[0]) {
                        return array('formvalidator_make_min_width', array('{0}' => $options));
                    }
                break;
                case 'min_height': // @codeCoverageIgnore
                    if($k[1] > $size[1]) {
                        return array('formvalidator_make_min_height', array('{0}' => $options));
                    }
                break;
                case 'width': // @codeCoverageIgnore
                    if($k[1] != $size[0]) {
                        return array('formvalidator_make_width', array('{0}' => $options));
                    }
                break;
                case 'height': // @codeCoverageIgnore
                    if($k[1] != $size[1]) {
                        return array('formvalidator_make_height', array('{0}' => $options));
                    }
                break;
                case 'max_width': // @codeCoverageIgnore
                    if($k[1] < $size[0]) {
                        return array('formvalidator_make_max_width', array('{0}' => $options));
                    }
                break;
                case 'max_height': // @codeCoverageIgnore
                    if($k[1] < $size[1]) {
                        return array('formvalidator_make_max_height', array('{0}' => $options));
                    }
                break;
                case 'ratio': // @codeCoverageIgnore
                    if(\mb_strpos($k[1], '/') !== false) {
                        $k[1] = \explode('/', $k[1]);
                        $k[1] = $k[1][0] / $k[1][1];
                    }
                    
                    if(\number_format(($size[0] / $size[1]), 1) != \number_format($k[1], 1)) {
                        return array('formvalidator_make_ratio', array('{0}' => $options));
                    }
                break;
            }
        }
        
        return true;
    }
}
