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
 * Name: `in`
 *
 * This rule ensures a specific field is one of the specified values (comma separated). Usage: `in:VALUE_1,VALUE_2,...`
 */
class In implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        $n = \explode(',', $options);
        if(!\in_array($value, $n)) {
            return array('formvalidator_make_in', array('{0}' => $options));
        }
        
        return true;
    }
}
