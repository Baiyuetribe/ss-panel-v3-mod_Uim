<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Utils;

/**
 * Image Helper utilities.
 */
class ImageHelpers {
    /**
     * Returns the default extension for an image.
     * @param string  $image  The image hash.
     * @return string  Returns "gif" if the hash begins with "a_", otherwise "png".
     */
    static function getImageExtension(string $image): string {
        return (\strpos($image, 'a_') === 0 ? 'gif' : 'png');
    }
    
    /**
     * Returns whether the input number is a power of 2.
     * @param int|null  $size
     * @return bool
     */
    static function isPowerOfTwo(?int $size): bool {
        return ($size === null || !($size & ($size - 1)));
    }
}
