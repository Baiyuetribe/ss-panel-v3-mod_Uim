<?php
/**
 * Validator
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Validator/blob/master/LICENSE
**/

namespace CharlotteDunois\Validation;

final class CallbackRuleTest extends \PHPUnit\Framework\TestCase {
    function testPrototype() {
        $prototype = \CharlotteDunois\Validation\Rules\Callback::prototype(function (?string $a = null): ?string {});
        $this->assertSame('?string?=?string', $prototype);
    }
    
    function testPrototypeNoSignature() {
        $this->expectException(\InvalidArgumentException::class);
        \CharlotteDunois\Validation\Rules\Callback::prototype(function () {});
    }
    
    function testPrototypeArray() {
        $prototype = \CharlotteDunois\Validation\Rules\Callback::prototype(array($this, 'prototyping'));
        $this->assertSame('?string?', $prototype);
    }
    
    function prototyping(?string $a = null) {
        
    }
}
