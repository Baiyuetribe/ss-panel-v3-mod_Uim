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
 * Name: `min`
 *
 * This rule ensures a specific field is/has:
 *   - numeric: equal/greater than the specified value
 *   - file: filesize is equal/greater than the specified value (in kibibytes)
 *   - array: equal/more elements than specified value
 *   - string: equal/more characters than specified value
 *
 * Usage: `min:VALUE`
 */
class Min implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(isset($_FILES[$key]) && \file_exists($_FILES[$key]['tmp_name']) && $_FILES[$key]['error'] == 0) {
            $v = \round((\filesize($_FILES[$key]['tmp_name']) / 1024));
        } else {
            if(!$exists) {
                return false;
            }
            
            if(\is_array($value)) {
                $v = \count($value);
            } elseif(\is_numeric($value)) {
                $v = $value;
            } else {
                $v = \mb_strlen($value);
            }
        }
        
        if($v < $options) {
            if(\is_string($value)) {
                return array('formvalidator_make_min_string', array('{0}' => $options));
            } else {
                return array('formvalidator_make_min', array('{0}' => $options));
            }
            
        }
        
        return true;
    }
}
