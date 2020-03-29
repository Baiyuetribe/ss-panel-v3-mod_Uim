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
 * Name: `callback`
 *
 * This rule ensures that a given callable (= callback) has the required signature. This class exposes a helper method to achieve a string for the rule.
 * But the string can be manually forged, too.
 *
 * The rule value gets separated from the rule name with a colon `:` and each parameter type (prefix type with `?` for nullable, suffix type with `?` for optional (= parameter with default value))
 * gets then listed, separated by comma. The return type is specified at the end separated by a `=`,
 * e.g. `callback:string,?int,float?=bool` is equal to `function (string $a, ?int $b, float $c = 0.0): bool`.
 *
 * Less callback parameters than the callback specification has is allowed, but more parameters (superfluos parameters) are a violation of the specification.
 */
class Callback implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     * @throws \LogicException
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        if(!$exists) {
            return true;
        }
        
        if(!\is_callable($value)) {
            return 'formvalidator_make_callable';
        }
        
        if(empty($options)) {
            throw new \LogicException('Invalid options value given for callback rule');
        }
        
        /** @var \ReflectionMethod|\ReflectionFunction  $prototype */
        
        if(\is_array($value)) {
            $prototype = new \ReflectionMethod($value[0], $value[1]);
        } else {
            $prototype = new \ReflectionFunction($value);
        }
        
        $options = \explode('=', $options, 2);
        
        if(!empty($options[0])) {
            $params = \explode(',', $options[0]);
            
            /** @var \ReflectionParameter  $param */
            foreach($prototype->getParameters() as $pos => $param) {
                $type = $param->getType();
                
                if(!isset($params[$pos])) {
                    return array('formvalidator_make_callback_param_superfluos', array('{0}' => $pos));
                } elseif($params[$pos] === '') {
                    continue; // "mixed" type - wildcard
                } elseif(($type === null && $params[$pos] !== '') || \trim($params[$pos], '?') !== $type->getName()) {
                    return array('formvalidator_make_callback_param', array('{0}' => $pos, '{1}' => \trim($params[$pos], '?')));
                } elseif($params[$pos][0] === '?' && !$type->allowsNull()) {
                    return array('formvalidator_make_callback_param_nullable', array('{0}' => $pos));
                } elseif(\substr($params[$pos], -1) === '?' && !$param->isOptional()) {
                    return array('formvalidator_make_callback_param_optional', array('{0}' => $pos));
                }
            }
        }
        
        if(!empty($options[1])) {
            $return = $prototype->getReturnType();
            
            if($return === null) {
                return array('formvalidator_make_callback_return', array('{0}' => $options[1]));
            } elseif(($return->allowsNull() ? '?' : '').$return->getName() !== $options[1]) {
                return array('formvalidator_make_callback_return_type', array('{0}' => $options[1], '{1}' => $return->getName()));
            }
        }
        
        return true;
    }
    
    /**
     * Turns a callable into a callback signature.
     * @param callable  $callable
     * @return string
     * @throws \InvalidArgumentException
     */
    static function prototype(callable $callable) {
        /** @var \ReflectionMethod|\ReflectionFunction  $prototype */
        
        if(\is_array($callable)) {
            $prototype = new \ReflectionMethod($callable[0], $callable[1]);
        } else {
            $prototype = new \ReflectionFunction($callable);
        }
        
        $signature = '';
        
        /** @var \ReflectionParameter  $param */
        foreach($prototype->getParameters() as $param) {
            $type = $param->getType();
            $signature .= ($type->allowsNull() ? '?' : '').$type->getName().($param->isOptional() ? '?' : '').',';
        }
        
        $signature = \substr($signature, 0, -1);
        
        $return = $prototype->getReturnType();
        if($return !== null) {
            $signature .= '='.($return->allowsNull() ? '?' : '').$return->getName();
        }
        
        if(empty($signature)) {
            throw new \InvalidArgumentException('Given callable has no signature to build');
        }
        
        return $signature;
    }
}
