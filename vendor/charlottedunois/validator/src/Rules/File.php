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
 * Name: `file`
 *
 * This rule ensures a specific field is a (successful)  file upload. Usage: `file:FIELD_NAME`
 */
class File implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!isset($_FILES[$key]) || !\file_exists($_FILES[$key]['tmp_name']) || $_FILES[$key]['error'] != 0) {
            return 'formvalidator_make_invalid_file';
        }
        
        return true;
    }
}
