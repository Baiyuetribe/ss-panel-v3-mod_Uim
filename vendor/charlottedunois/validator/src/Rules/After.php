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
 * Name: `after`
 *
 * This rule ensures a specific field is a time after the specified time. Usage: `after:VALUE`
 */
class After implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(!\is_string($value) || \strtotime($options) > \strtotime($value)) {
            return array('formvalidator_make_after', array('{0}' => $options));
        }
        
        return true;
    }
}
