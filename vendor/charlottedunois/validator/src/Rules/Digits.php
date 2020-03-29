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
 * Name: `digits`
 *
 * This rule ensures a specific field is a numeric value (string) with the specified length. Usage: `digits:LENGTH`
 */
class Digits implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(!\is_numeric($value) || \mb_strlen(((string) $value)) != $options) {
            return array('formvalidator_make_digits', array('{0}' => $options));
        }
        
        return true;
    }
}
