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
 * Name: `image`
 *
 * This rule ensures a specific upload field is an image. Usage: `image:FIELD_NAME`
 */
class Image implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(isset($_FILES[$key])) {
            if(!\file_exists($_FILES[$key]['tmp_name'])) {
                return 'formvalidator_make_image';
            }
            
            $size = \getimagesize($_FILES[$key]['tmp_name']);
        } else {
            if(!$exists) {
                return false;
            }
            
            $size = @\getimagesizefromstring($value);
        }
        
        if(!$size) {
            return 'formvalidator_make_image';
        }
        
        return true;
    }
}
