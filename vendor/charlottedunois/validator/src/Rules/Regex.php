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
 * Name: `regex`
 *
 * This rule ensures a specific field passed the regex validation. Usage: `regex:REGEX_WITH_DELIMITERS`
 */
class Regex implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(\preg_match($options, $value) === 0) {
            return 'formvalidator_make_regex';
        }
        
        return true;
    }
}
