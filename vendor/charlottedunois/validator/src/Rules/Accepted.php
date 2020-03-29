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
 * Name: `accepted`
 *
 * This rule ensures a specific field is accepted (value: `yes`, `on`, `1` or `true`).
 */
class Accepted implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(!\in_array($value, array('yes', 'on', 1, true, '1', 'true'), true)) {
            return 'formvalidator_make_accepted';
        }
        
        return true;
    }
}
