# Validator [![Build Status](https://scrutinizer-ci.com/g/CharlotteDunois/Validator/badges/build.png?b=master)](https://scrutinizer-ci.com/g/CharlotteDunois/Validator/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/CharlotteDunois/Validator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/CharlotteDunois/Validator/?branch=master)

This is a PHP validator for stuff.

# Usage
Include composer's autoloader and initialize an instance.

```php
<?php
// include autoloader

//This one will not fail
$nofail = CharlotteDunois\Validation\Validator::make(
    array(
        'username' => 'CharlotteDunois',
        'email' => 'noreply@github.com'
    ),
    array(
        'username' => 'string|required|min:5|max:75',
        'email' => 'email'
    )
);

var_dump($nofail->passes());

//This one will fail due to invalid email
$fail = CharlotteDunois\Validation\Validator::make(
    array(
        'username' => 'CharlotteDuois',
        'email' => 'noreply@githubcom'
    ),
    array(
        'username' => 'string|required|min:5|max:75',
        'email' => 'email'
    )
);

var_dump($fail->passes(), $fail->errors());
```

# Documentation
https://charlottedunois.github.io/Validator/
