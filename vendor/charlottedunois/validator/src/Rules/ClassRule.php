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
 * Name: `class` - Type Rule
 *
 * This rule ensures a specific field is a string containing a valid class name or a class instance.
 * The options value ensures the class is either of that type, or extending it or implementing it.
 *
 * You can ensure that only class names get passed by appending `=string`, or only objects by `=object`.
 *
 * Usage: `class:CLASS_NAME` or `class:CLASS_NAME=string` or `class:CLASS_NAME=object`
 */
class ClassRule implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return false;
        }
        
        $is_string = \is_string($value);
        $is_object = \is_object($value);
        
        if(!$is_string && !$is_object) {
            return 'formvalidator_make_class';
        }
        
        $options = \explode('=', $options);
        $class = \ltrim($options[0], '\\');
        
        if(!empty($options[1]) && $options[1] === 'string' && !$is_string) {
            return 'formvalidator_make_class_stringonly';
        }
        
        if(!empty($options[1]) && $options[1] === 'object' && !$is_object) {
            return 'formvalidator_make_class_objectonly';
        }
        
        if($is_string && !\class_exists($value)) {
            return 'formvalidator_make_class';
        }
        
        if(!\is_a($value, $class, true) && !\is_subclass_of($value, $class, true)) {
            return array('formvalidator_make_class_inheritance', array('{0}' => $class));
        }
        
        return true;
    }
}
