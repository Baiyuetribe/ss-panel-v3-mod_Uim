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
 * Name: `dateformat`
 *
 * This rule ensures a specific field is a date in a specific format. Usage: `dateformat:FORMAT`
 */
class DateFormat implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        $dt = \date_parse_from_format($options, $value);
        if(!$dt || $dt['error_count'] > 0) {
            return array('formvalidator_make_date_format', array('{0}' => $options));
        }
        
        return true;
    }
}
