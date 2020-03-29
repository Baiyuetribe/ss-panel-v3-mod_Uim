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
 * Name: `confirmed`
 *
 * This rule ensures a specific field is confirmed (the fields array contains another field with the same value with the key `$key_FIELDNAME`, FIELDNAME defaults to `confirmation`). Usage: `confirmed` or `confirmed:FIELDNAME`
 */
class Confirmed implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(empty($options)) {
            $options = 'confirmation';
        }
        
        if(!isset($fields[$key.'_'.$options]) || $fields[$key] !== $fields[$key.'_'.$options]) {
            return 'formvalidator_make_confirmed';
        }
        
        return true;
    }
}
