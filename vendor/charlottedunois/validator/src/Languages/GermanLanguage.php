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
 * The German language translations.
 * @codeCoverageIgnore
 */
class GermanLanguage implements \CharlotteDunois\Validation\LanguageInterface {
    /**
     * The translations.
     * @var string[]
     */
    protected $translations = array(
        'formvalidator_unknown_field' => 'Ist ein unbekanntes Feld',
        'formvalidator_make_accepted' => 'Ist nicht akzeptiert',
        'formvalidator_make_active_url' => 'Ist keine aktive URL',
        'formvalidator_make_after' => 'Ist nicht grösser/nachher als {0}',
        'formvalidator_make_alpha' => 'Enthält nicht alphabetische Zeichen',
        'formvalidator_make_alpha_dash' => 'Enthält nicht alphabetische, - und _  zeichen',
        'formvalidator_make_alpha_num' => 'Enthält nicht alphanumerische Zeichen',
        'formvalidator_make_anon_function' => 'Ist nicht keine anonyme Funktion',
        'formvalidator_make_array' => 'Ist kein Array',
        'formvalidator_make_array_subtype' => 'Ist kein Array von {0} values',
        'formvalidator_make_before' => 'Ist kleiner/vorher als {0}',
        'formvalidator_make_between' => 'Ist nicht zwischen {0} und {1}',
        'formvalidator_make_boolean' => 'Ist kein booleanischer Wert',
        'formvalidator_make_callable' => 'Ist kein callable',
        'formvalidator_make_callback_param_nullable' => 'Callback Parameter auf der Position {0} ist nicht nullable',
        'formvalidator_make_callback_param_optional' => 'Callback Parameter auf der Position {0} ist nicht optional',
        'formvalidator_make_callback_param' => 'Callback Parameter auf der Position {0} hat nicht den erwarteten Typ {1}',
        'formvalidator_make_callback_param_superfluos' => 'Callback hat mehr Parameter als die Callback Spezifikation, fängt an mit Parameter auf der Position {0}',
        'formvalidator_make_callback_return' => 'Callback hat nicht den erwarteten Rückgabe-Typ {0}',
        'formvalidator_make_callback_return_type' => 'Callback hat nicht den erwarteten Rückgabe-Typ {0}, hat aber {1}',
        'formvalidator_make_class' => 'Ist kein Klasse oder Klasse Name',
        'formvalidator_make_class_objectonly' => 'Ist kein Klasse Instance',
        'formvalidator_make_class_stringonly' => 'Ist kein Klasse Name',
        'formvalidator_make_class_inheritance' => 'Ist keine Klasse die {0} erweitert oder implementiert',
        'formvalidator_make_confirmed' => 'Ist nicht bestätigt',
        'formvalidator_make_date' => 'Ist kein gültiges Datum',
        'formvalidator_make_date_format' => 'Ist kein gültiges Datum im Format {0}',
        'formvalidator_make_different' => 'Ist gleich wie das Feld {0}',
        'formvalidator_make_digits' => 'Ist keine Zahl oder hat nicht {0} Stellen',
        'formvalidator_make_digits_between' => 'Ist keine Zahl oder nicht zwischen {0}',
        'formvalidator_make_invalid_file' => 'Enthält keine gültige (oder überhaupt keine) Datei',
        'formvalidator_make_min_width' => 'Ist weniger breit als {0}px',
        'formvalidator_make_min_height' => 'Ist weniger hoch als {0}px',
        'formvalidator_make_width' => 'Ist nicht {0}px breit',
        'formvalidator_make_height' => 'Ist nicht {0}px hoch',
        'formvalidator_make_max_width' => 'Ist breiter als {0}px',
        'formvalidator_make_max_height' => 'Ist höher als {0}px',
        'formvalidator_make_ratio' => 'Entspricht nicht dem Verhältnis {0}',
        'formvalidator_make_distinct' => 'Ist nicht einzigartig',
        'formvalidator_make_email' => 'Ist keine E-Mail-Adresse',
        'formvalidator_make_filled' => 'Ist leer',
        'formvalidator_make_float' => 'Ist kein Float',
        'formvalidator_make_image' => 'Ist kein Bild',
        'formvalidator_make_in' => 'Enthält keiner der folgenden Werten: {0}',
        'formvalidator_make_integer' => 'Ist kein Integer',
        'formvalidator_make_ip' => 'Ist keine IP-Adresse',
        'formvalidator_make_json' => 'Ist kein gültiger JSON-String',
        'formvalidator_make_max' => 'Ist grösser als {0}',
        'formvalidator_make_max_string' => 'Ist länger als {0} Zeichen',
        'formvalidator_make_mimetypes' => 'Entspricht nicht dem MIME-Typ: {0}',
        'formvalidator_make_mimes' => 'Entspricht nicht der Endung: {0}',
        'formvalidator_make_min' => 'Ist kleiner als {0}',
        'formvalidator_make_min_string' => 'Ist kürzer als {0} Zeichen',
        'formvalidator_make_nullable' => 'Ist NULL',
        'formvalidator_make_numeric' => 'Ist nicht numerisch',
        'formvalidator_make_present' => 'Ist nicht präsent',
        'formvalidator_make_regex' => 'Entspricht nicht dem vorgegebenen Muster',
        'formvalidator_make_required' => 'Ist nicht vorhanden oder ist leer',
        'formvalidator_make_same' => 'Ist nicht gleich {0}',
        'formvalidator_make_size' => 'Entspricht nicht {0}',
        'formvalidator_make_string' => 'Ist kein String',
        'formvalidator_make_url' => 'Ist keine URL'
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
