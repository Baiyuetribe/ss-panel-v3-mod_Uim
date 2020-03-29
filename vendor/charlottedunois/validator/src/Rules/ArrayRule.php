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
 * Name: `array` - Type Rule
 *
 * This rule ensures a specific field is an array, or an array with only the specified type. Usage: `array` or `array:TYPE`
 */
class ArrayRule implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        if(!\is_array($value)) {
            return 'formvalidator_make_array';
        }
        
        if(!empty($options)) {
            foreach($value as $val) {
                $type = \gettype($val);
                if($type === 'double') {
                    $type = 'float'; // @codeCoverageIgnore
                }
                
                if($type !== $options) {
                    return array('formvalidator_make_array_subtype', array('{0}' => $options));
                }
            }
        }
        
        return true;
    }
}
