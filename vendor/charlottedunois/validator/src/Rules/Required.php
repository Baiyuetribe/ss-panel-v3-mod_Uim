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
 * Name: `required`
 *
 * This rule ensures a specific (upload) field is present and not empty.
 */
class Required implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if((!$exists || \is_null($value) || (\is_string($value) === true && \trim($value) === '')) && (!isset($_FILES[$key]) || $_FILES[$key]['error'] != 0)) {
            return 'formvalidator_make_required';
        }
        
        return true;
    }
}
