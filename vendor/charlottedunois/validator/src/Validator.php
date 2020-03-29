<?php
/**
 * Validator
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Validator/blob/master/LICENSE
**/

namespace CharlotteDunois\Validation;

/**
 * The Validator.
 * Type Rules are non-exclusive (that means specifying two type rules means either one is passing).
 */
class Validator {
    /** @var array */
    protected $errors = array();
    
    /** @var array */
    protected $fields = array();
    
    /** @var array */
    protected $rules = array();
    
    /** @var bool */
    protected $strict;
    
    /** @var \CharlotteDunois\Validation\LanguageInterface */
    protected $lang;
    
    /** @var string */
    protected static $defaultLanguage = \CharlotteDunois\Validation\Languages\EnglishLanguage::class;
    
    /** @var \CharlotteDunois\Validation\RuleInterface[] */
    protected static $rulesets;
    
    /** @var \CharlotteDunois\Validation\RuleInterface[] */
    protected static $typeRules = array();
    
    /**
     * Constructor
     * @param  array  $fields
     * @param  array  $rules
     * @param  bool   $strict
     */
    protected function __construct(array $fields, array $rules, bool $strict) {
        $this->fields = $fields;
        $this->rules = $rules;
        $this->strict = $strict;
        
        $lang = static::$defaultLanguage;
        $this->lang = new $lang();
        
        if(static::$rulesets === null) {
            static::initRules();
        }
    }
    
    /**
     * Create a new Validator instance.
     * @param  array  $fields  The fields you wanna run the validation against.
     * @param  array  $rules   The validation rules.
     * @param  bool   $strict  Whether unknown fields make validation fail.
     * @return Validator
     */
    static function make(array $fields, array $rules, bool $strict = false) {
        return (new static($fields, $rules, $strict));
    }
    
    /**
     * Adds a new rule.
     * @param \CharlotteDunois\Validation\RuleInterface  $rule
     * @return void
     * @throws \InvalidArgumentException
     */
    static function addRule(\CharlotteDunois\Validation\RuleInterface $rule) {
        if(static::$rulesets === null) {
            static::initRules();
        }
        
        $class = \get_class($rule);
        $arrname = \explode('\\', $class);
        $name = \array_pop($arrname);
        
        $rname = \str_replace('rule', '', \mb_strtolower($name));
        static::$rulesets[$rname] = $rule;
        
        if(\mb_stripos($name, 'rule') !== false) {
            static::$typeRules[] = $rname;
        }
    }
    
    /**
     * Sets the default language for the Validator.
     * @param string  $language
     * @return void
     * @throws \InvalidArgumentException
     */
    static function setDefaultLanguage(string $language) {
        if(!\class_exists($language, true)) {
            throw new \InvalidArgumentException('Unknown language class');
        } elseif(!\in_array(\CharlotteDunois\Validation\LanguageInterface::class, \class_implements($language), true)) {
            throw new \InvalidArgumentException('Invalid language class (not implementing language interface)');
        }
        
        static::$defaultLanguage = $language;
    }
    
    /**
     * Sets the language for the Validator.
     * @param \CharlotteDunois\Validation\LanguageInterface  $language
     * @return $this
     * @throws \InvalidArgumentException
     */
    function setLanguage(\CharlotteDunois\Validation\LanguageInterface $language) {
        $this->lang = $language;
        return $this;
    }
    
    /**
     * Returns errors.
     * @return array
     */
    function errors() {
        return $this->errors;
    }
    
    /**
     * Determine if the data passes the validation rules.
     * @return bool
     * @throws \RuntimeException
     */
    function passes() {
        return $this->startValidation();
    }
    
    /**
     * Determine if the data fails the validation rules.
     * @return bool
     * @throws \RuntimeException
     */
    function fails() {
        return !($this->startValidation());
    }
    
    /**
     * Determines if the data passes the validation rules, or throws.
     * @param string  $class  Which exception class to throw.
     * @return bool
     * @throws \RuntimeException
     */
    function throw(string $class = '\RuntimeException') {
        return $this->startValidation($class);
    }
    
    /**
     * @return bool
     * @throws \RuntimeException
     */
    protected function startValidation(string $throws = '') {
        $vThrows = !empty($throws);
        $fields = $this->fields;
        
        foreach($this->rules as $key => $rule) {
            $set = \explode('|', $rule);
            
            $exists = \array_key_exists($key, $this->fields);
            $value = ($exists ? $this->fields[$key] : null);
            
            unset($fields[$key]);
            
            $passedLang = false;
            $failedOtherRules = false;
            
            $nullable = false;
            foreach($set as $r) {
                $r = \explode(':', $r, 2);
                if($r[0] === 'nullable') {
                    $nullable = true;
                    continue 1;
                } elseif(!isset(static::$rulesets[$r[0]])) {
                    throw new \RuntimeException('Validation Rule "'.$r[0].'" does not exist');
                }
                
                $return = static::$rulesets[$r[0]]->validate($value, $key, $this->fields, (\array_key_exists(1, $r) ? $r[1] : null), $exists, $this);
                $passed = \is_bool($return);
                
                if(\in_array($r[0], static::$typeRules)) {
                    if($passed) {
                        $passedLang = true;
                    } else {
                        if(!$passedLang) {
                            $passed = false;
                        }
                    }
                } else {
                    if(!$passed) {
                        $failedOtherRules = true;
                    }
                }
                
                if(!$passed) {
                    if(\is_array($return)) {
                        $this->errors[$key] = $this->language($return[0], $return[1]);
                    } else {
                        $this->errors[$key] = $this->language($return);
                    }
                }
            }
            
            if($passedLang && !$failedOtherRules) {
                unset($this->errors[$key]);
            }
            
            if($exists && is_null($value)) {
                if(!$nullable) {
                    $this->errors[$key] = $this->language('formvalidator_make_nullable');
                } elseif($nullable && isset($this->errors[$key])) {
                    unset($this->errors[$key]);
                }
            }
            
            if($vThrows && !empty($this->errors[$key])) {
                throw new $throws($key.' '.\lcfirst($this->errors[$key]));
            }
        }
        
        if($this->strict) {
            foreach($fields as $key => $_) {
                $msg = $this->language('formvalidator_unknown_field');
                
                if($vThrows) {
                    throw new $throws('"'.$key.'" '.\lcfirst($msg));
                }
                
                $this->errors[$key] = $msg;
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Return the error message based on the language key (language based).
     *
     * @param  string  $key
     * @param  array   $replacements
     * @return string
     */
    function language(string $key, array $replacements = array()) {
        return $this->lang->getTranslation($key, $replacements);
    }
    
    protected static function initRules() {
        static::$rulesets = array();
        
        $rules = \glob(__DIR__.'/Rules/*.php');
        foreach($rules as $rule) {
            $name = \basename($rule, '.php');
            if($name === 'Nullable') {
                continue;
            }
            
            $class = '\\CharlotteDunois\\Validation\\Rules\\'.$name;
            static::addRule((new $class()));
        }
    }
}
