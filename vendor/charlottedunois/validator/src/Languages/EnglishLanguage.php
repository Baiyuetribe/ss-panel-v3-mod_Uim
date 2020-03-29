<?php
/**
 * Validator
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Validator/blob/master/LICENSE
**/

namespace CharlotteDunois\Validation\Languages;

/**
 * The English language translations.
 * @codeCoverageIgnore
 */
class EnglishLanguage implements \CharlotteDunois\Validation\LanguageInterface {
    /**
     * The translations.
     * @var string[]
     */
    protected $translations = array(
        'formvalidator_unknown_field' => 'Is an unknown field',
        'formvalidator_make_accepted' => 'Is not accepted',
        'formvalidator_make_active_url' => 'Is not an active URL',
        'formvalidator_make_after' => 'Is not bigger / after than {0}',
        'formvalidator_make_alpha' => 'Does not contain alphabetical characters',
        'formvalidator_make_alpha_dash' => 'Does not contain alphabetic, -, and _ characters',
        'formvalidator_make_alpha_num' => 'Does not contain alphanumeric characters',
        'formvalidator_make_anon_function' => 'Is not an anonymous function',
        'formvalidator_make_array' => 'Is not an array',
        'formvalidator_make_array_subtype' => 'Is not an array of {0} values',
        'formvalidator_make_before' => 'Is smaller / before than {0}',
        'formvalidator_make_between' => 'Is not between {0} and {1}',
        'formvalidator_make_boolean' => 'Is not a boolean value',
        'formvalidator_make_callable' => 'Is not a callable',
        'formvalidator_make_callback_param_nullable' => 'Callback parameter on position {0} is not nullable',
        'formvalidator_make_callback_param_optional' => 'Callback parameter on position {0} is not optional',
        'formvalidator_make_callback_param' => 'Callback parameter on position {0} has not the expected type {1}',
        'formvalidator_make_callback_param_superfluos' => 'Callback has more parameters than the callback spec, starting with parameter on position {0}',
        'formvalidator_make_callback_return' => 'Callback has not the expected return type {0}',
        'formvalidator_make_callback_return_type' => 'Callback has not the expected return type {0}, has {1}',
        'formvalidator_make_class' => 'Is not a class or class name',
        'formvalidator_make_class_objectonly' => 'Is not a class instance',
        'formvalidator_make_class_stringonly' => 'Is not a class name',
        'formvalidator_make_class_inheritance' => 'Is not a class which {0} extends or implements',
        'formvalidator_make_confirmed' => 'Not verified',
        'formvalidator_make_date' => 'Is not a valid date',
        'formvalidator_make_date_format' => 'Is not a valid date in format {0}',
        'formvalidator_make_different' => 'Same as field {0}',
        'formvalidator_make_digits' => 'Is not a number or has not {0} digits',
        'formvalidator_make_digits_between' => 'Is not a number or not between {0}',
        'formvalidator_make_invalid_file' => 'Does not contain a valid (or no at all) file',
        'formvalidator_make_min_width' => 'Is less wide than {0} px',
        'formvalidator_make_min_height' => 'Is less than {0} px',
        'formvalidator_make_width' => 'Is not {0} px wide',
        'formvalidator_make_height' => 'Is not {0} px high',
        'formvalidator_make_max_width' => 'Is wider than {0} px',
        'formvalidator_make_max_height' => 'Is higher than {0} px',
        'formvalidator_make_ratio' => 'Does not match the ratio {0}',
        'formvalidator_make_distinct' => 'Is not unique',
        'formvalidator_make_email' => 'Is not an e-mail address',
        'formvalidator_make_filled' => 'Is empty',
        'formvalidator_make_float' => 'Is not a float',
        'formvalidator_make_image' => 'No image',
        'formvalidator_make_in' => 'Does not contain any of the following: {0}',
        'formvalidator_make_integer' => 'Is not an integer',
        'formvalidator_make_ip' => 'Is not an IP address',
        'formvalidator_make_json' => 'Is not a valid JSON string',
        'formvalidator_make_lowercase' => 'Is not all lowercase',
        'formvalidator_make_max' => 'Is greater than {0}',
        'formvalidator_make_max_string' => 'Is longer than {0} characters',
        'formvalidator_make_mimetypes' => 'Does not match the MIME type: {0}',
        'formvalidator_make_mimes' => 'Does not match the ending: {0}',
        'formvalidator_make_min' => 'Is less than {0}',
        'formvalidator_make_min_string' => 'Is shorter than {0} characters',
        'formvalidator_make_no_whitespace' => 'Contains whitespaces',
        'formvalidator_make_nullable' => 'Is NULL',
        'formvalidator_make_numeric' => 'Is not numeric',
        'formvalidator_make_present' => 'Is not present',
        'formvalidator_make_regex' => 'Does not match the defined pattern',
        'formvalidator_make_required' => 'Does not exist or is empty',
        'formvalidator_make_same' => 'Is not equal to {0}',
        'formvalidator_make_size' => 'Does not match {0}',
        'formvalidator_make_string' => 'Is not a string',
        'formvalidator_make_uppercase' => 'Is not all uppercase',
        'formvalidator_make_url' => 'Is not a URL'
    );
    
    /**
     * Get a translation string, denoted by key. Replace the `$replacements` keys by their values in that string.
     * @param string  $key
     * @param array   $replacements
     * @return string  If not found, it must return the key.
     */
    function getTranslation(string $key, array $replacements = array()) {
        if(isset($this->translations[$key])) {
            $lang = $this->translations[$key];
            
            if(!empty($replacements)) {
                foreach($replacements as $key => $val) {
                    $lang = \str_replace($key, $val, $lang);
                }
            }
            
            return $lang;
        }
        
        return $key;
    }
}
