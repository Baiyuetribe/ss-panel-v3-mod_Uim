<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Models;

/**
 * Represents an attachment (from a message).
 *
 * @property string     $id                 The attachment ID.
 * @property string     $filename           The filename.
 * @property int        $size               The filesize in bytes.
 * @property string     $url                The url to the file.
 * @property int|null   $height             The height (if image), or null.
 * @property int|null   $width              The width (if image), or null.
 * @property int        $createdTimestamp   The timestamp of when this attachment was created.
 *
 * @property \DateTime  $createdAt          An DateTime instance of the createdTimestamp.
 */
class MessageAttachment extends Base {
    /**
     * The attachment ID
     * @var string
     */
    protected $id;
    
    /**
     * The filename.
     * @var string
     */
    protected $filename;
    
    /**
     * The filesize in bytes.
     * @var int
     */
    protected $size;
    
    /**
     * The url to the file.
     * @var string
     */
    protected $url;
    
    /**
     * The height (if image), or null.
     * @var int|null
     */
    protected $height;
    
    /**
     * The width (if image), or null.
     * @var int|null
     */
    protected $width;
    
    /**
     * The timestamp of when this attachment was created.
     * @var int
     */
    protected $createdTimestamp;
    
    /**
     * Used for sending attachments.
     * @var string
     * @internal
     */
    protected $attachment;
    
    /**
     * Constructs a new instance.
     * @param array  $attachment  This parameter is used internally and should not be used.
     */
    function __construct(array $attachment = array()) {
        if(!empty($attachment)) {
            $this->id = (string) $attachment['id'];
            $this->filename = (string) $attachment['filename'];
            $this->size = (int) $attachment['size'];
            $this->url = (string) $attachment['url'];
            $this->height = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($attachment['height'] ?? null), 'int');
            $this->width = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($attachment['width'] ?? null), 'int');
            
            $this->createdTimestamp = (int) \CharlotteDunois\Yasmin\Utils\Snowflake::deconstruct($this->id)->timestamp;
        }
    }
    
    /**
     * {@inheritdoc}
     * @return mixed
     * @throws \RuntimeException
     * @internal
     */
    function __get($name) {
        if(\property_exists($this, $name)) {
            return $this->$name;
        }
        
        switch($name) {
            case 'createdAt':
                return \CharlotteDunois\Yasmin\Utils\DataHelpers::makeDateTime($this->createdTimestamp);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Sets the attachment.
     * @param string  $attachment  An URL or the filepath, or the data.
     * @param string  $filename    The filename.
     * @return $this
     */
    function setAttachment(string $attachment, string $filename = '') {
        $this->attachment = $attachment;
        $this->filename = $filename;
        return $this;
    }
    
    /**
     * Returns a proper message files array.
     * @return array
     * @internal
     */
    function _getMessageFilesArray() {
        $props = array(
            'name' => $this->filename
        );
        
        $file = @\realpath($this->attachment);
        if($file) {
            $props['path'] = $file;
        } elseif(\filter_var($this->attachment, \FILTER_VALIDATE_URL)) {
            $props['path'] = $this->attachment;
        } else {
            $props['data'] = $this->attachment;
        }
        
        if(empty($props['name'])) {
            if(!empty($props['path'])) {
                $props['name'] = \basename($props['path']);
            } else {
                $props['name'] = 'file-'.\bin2hex(\random_bytes(3)).'.jpg';
            }
        }
        
        return $props;
    }
}
