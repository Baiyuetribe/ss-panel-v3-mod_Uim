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
 * Name: `date`
 *
 * This rule ensures a specific field is a date.
 */
class Date implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(\strtotime($value) === false) {
            return 'formvalidator_make_date';
        }
        
        return true;
    }
}
