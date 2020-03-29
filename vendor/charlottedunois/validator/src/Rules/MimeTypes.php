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
 * Name: `mimetypes`
 *
 * This rule ensures a specific upload field is of specific mime type (comma separated). Valid options (examples): `image/*`, `*­/*`, `image/png`. Usage: `mimetypes:MIME_TYPE`
 */
class MimeTypes implements \CharlotteDunois\Validation\RuleInterface {
    /**
     * {@inheritdoc}
     * @return bool|string|array
     */
    function validate($value, $key, $fields, $options, $exists, \CharlotteDunois\Validation\Validator $validator) {
        $finfo = \finfo_open(\FILEINFO_MIME);
        
        if(isset($_FILES[$key])) {
            if(!\file_exists($_FILES[$key]['tmp_name'])) {
                return 'formvalidator_make_invalid_file';
            }
            
            $mime = \finfo_file($finfo, $_FILES[$key]['tmp_name']);
        } else {
            if(!$exists) {
                return false;
            }
            
            $mime = \finfo_buffer($finfo, $value);
        }
        
        \finfo_close($finfo);
        
        if(!$mime) {
            return 'formvalidator_make_invalid_file'; // @codeCoverageIgnore
        }
        
        $mime = \explode(';', $mime);
        $mime = \array_shift($mime);
        
        $val = \explode(',', $options);
        $result = \explode('/', $mime);
        
        foreach($val as $mimet) {
            $mimee = \explode('/', $mimet);
            if(\count($mimee) == 2 && \count($result) == 2) {
                if(($mimee[0] == "*" || $mimee[0] == $result[0]) && ($mimee[1] == "*" || $mimee[1] == $result[1])) {
                    return true;
                }
            }
        }
        
        return 'formvalidator_make_invalid_file';
    }
}
