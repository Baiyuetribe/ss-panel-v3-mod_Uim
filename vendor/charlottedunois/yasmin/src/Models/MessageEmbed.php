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
 * Represents a received embed from a message. This class can also be used to make a Rich Embed.
 *
 * @property string            $type               The embed type.
 * @property string|null       $title              The title, or null.
 * @property array|null        $author             The author in the format `[ 'name' => string, 'icon' => string, 'url' => string ]`, or null.
 * @property string|null       $description        The description, or null.
 * @property string|null       $url                The URL, or null.
 * @property int|string|null   $timestamp          The timestamp, or the set timestamp (as ISO string), or null.
 * @property int|null          $color              The color, or null.
 * @property array|null        $footer             The footer in the format `[ 'name' => string, 'icon' => string ]`, or null.
 * @property array|null        $image              The image in the format `[ 'url' => string, 'height' => int, 'width' => int ]`, or null.
 * @property array|null        $thumbnail          The thumbnail in the format `[ 'url' => string, 'height' => int, 'width' => int ]`, or null.
 * @property array|null        $video              The video in the format `[ 'url' => string, 'height' => int, 'width' => int ]`, or null.
 * @property array|null        $provider           The provider in the format `[ 'name' => string, 'url' => string ]`, or null.
 * @property array[]           $fields             An array of embed fields in the format `[ 'name' > string, 'value' => string, 'inline' => bool ]`.
 *
 * @property \DateTime|null    $datetime           The DateTime instance of timestamp, or null.
 */
class MessageEmbed extends Base {
    /**
     * The embed type.
     * @var string
     */
    protected $type = 'rich';
    
    /**
     * The title, or null.
     * @var string|null
     */
    protected $title;
    
    /**
     * The author, or null.
     * @var string|null
     */
    protected $author;
    
    /**
     * The description, or null.
     * @var string|null
     */
    protected $description;
    
    /**
     * The URL, or null.
     * @var string|null
     */
    protected $url;
    
    /**
     * The timestamp, or the set timestamp (as ISO string), or null.
     * @var int|string|null
     */
    protected $timestamp;
    
    /**
     * The color, or null.
     * @var int|null
     */
    protected $color;
    
    /**
     * The footer, or null.
     * @var array|null
     */
    protected $footer;
    
    /**
     * The image, or null.
     * @var array|null
     */
    protected $image;
    
    /**
     * The thumbnail, or null.
     * @var array|null
     */
    protected $thumbnail;
    
    /**
     * The video, or null.
     * @var array|null
     */
    protected $video;
    
    /**
     * The provider, or null.
     * @var array|null
     */
    protected $provider;
    
    /**
     * An array of embed fields.
     * @var array
     */
    protected $fields = array();
    
    /**
     * Constructs a new instance.
     * @param array  $embed
     * @throws \Throwable
     */
    function __construct(array $embed = array()) {
        if(!empty($embed)) {
            $this->type = $embed['type'] ?? 'rich';
            
            if(!empty($embed['title'])) {
                $this->setTitle($embed['title']);
            }
            
            if(!empty($embed['author'])) {
                $this->setAuthor(
                    ((string) ($embed['author']['name'] ?? '')),
                    ((string) ($embed['author']['icon_url'] ?? '')),
                    ((string) ($embed['author']['url'] ?? ''))
                );
            }
            
            if(!empty($embed['description'])) {
                $this->setDescription($embed['description']);
            }
            
            $this->url = $embed['url'] ?? null;
            $this->timestamp = (!empty($embed['timestamp']) ? (new \DateTime($embed['timestamp']))->getTimestamp() : null);
            $this->color = $embed['color'] ?? null;
            
            if(!empty($embed['footer'])) {
                $this->setFooter(
                    ((string) ($embed['footer']['text'] ?? '')),
                    ((string) ($embed['footer']['icon_url'] ?? ''))
                );
            }
            
            if(!empty($embed['image'])) {
                $this->image = array(
                    'url' => ((string) $embed['image']['url']),
                    'height' => ((int) $embed['image']['height']),
                    'width' => ((int) $embed['image']['width'])
                );
            }
            
            if(!empty($embed['thumbnail'])) {
                $this->thumbnail = array(
                    'url' => ((string) $embed['thumbnail']['url']),
                    'height' => ((int) $embed['thumbnail']['height']),
                    'width' => ((int) $embed['thumbnail']['width'])
                );
            }
            
            if(!empty($embed['video'])) {
                $this->video = array(
                    'url' => ((string) $embed['video']['url']),
                    'height' => ((int) $embed['video']['height']),
                    'width' => ((int) $embed['video']['width'])
                );
            }
            
            if(!empty($embed['provider'])) {
                $this->provider = array(
                    'name' => ((string) $embed['provider']['name']),
                    'url' => ((string) $embed['provider']['url'])
                );
            }
            
            foreach(($embed['fields'] ?? array()) as $field) {
                $this->addField(
                    ((string) ($field['name'] ?? '')),
                    ((string) ($field['value'] ?? '')),
                    ((bool) ($field['inline'] ?? false))
                );
            }
        }
    }
    
    /**
     * {@inheritdoc}
     * @return mixed
     * @throws \Exception
     * @throws \RuntimeException
     * @internal
     */
    function __get($name) {
        if(\property_exists($this, $name)) {
            return $this->$name;
        }
        
        switch($name) {
            case 'datetime':
                return (new \DateTime('@'.$this->timestamp));
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Set the author of this embed.
     * @param string  $name      Maximum length is 256 characters.
     * @param string  $iconurl   The URL to the icon.
     * @param string  $url       The URL to the author.
     * @return $this
     * @throws \InvalidArgumentException
     */
    function setAuthor($name, string $iconurl = '', string $url = '') {
        $name = (string) $name;
        
        if(\strlen($name) === 0) {
            $this->author = null;
            return $this;
        }
        
        if(\mb_strlen($name) > 256) {
            throw new \InvalidArgumentException('Author name can not be longer than 256 characters.');
        }
        
        if($this->exceedsOverallLimit(\mb_strlen($name))) {
            throw new \InvalidArgumentException('Embed text values collectively can not exceed than 6000 characters');
        }
        
        $this->author = array(
            'name' => $name,
            'icon_url' => $iconurl,
            'url' => $url
        );
        
        return $this;
    }
    
    /**
     * Set the color of this embed.
     * @param mixed  $color
     * @return $this
     * @throws \InvalidArgumentException
     * @see \CharlotteDunois\Yasmin\Utils\DataHelpers::resolveColor()
     */
    function setColor($color) {
        $this->color = \CharlotteDunois\Yasmin\Utils\DataHelpers::resolveColor($color);
        return $this;
    }
    
    /**
     * Set the description of this embed.
     * @param string  $description  Maximum length is 2048 characters.
     * @return $this
     * @throws \InvalidArgumentException
     */
    function setDescription($description) {
        $description = (string) $description;
        
        if(\strlen($description) === 0) {
            $this->description = null;
            return $this;
        }
        
        if(\mb_strlen($description) > 2048) {
            throw new \InvalidArgumentException('Embed description can not be longer than 2048 characters');
        }
        
        if($this->exceedsOverallLimit(\mb_strlen($description))) {
            throw new \InvalidArgumentException('Embed text values collectively can not exceed than 6000 characters');
        }
        
        $this->description = $description;
        return $this;
    }
    
    /**
     * Set the footer of this embed.
     * @param string  $text     Maximum length is 2048 characters.
     * @param string  $iconurl  The URL to the icon.
     * @return $this
     * @throws \InvalidArgumentException
     */
    function setFooter($text, string $iconurl = '') {
        $text = (string) $text;
        
        if(\strlen($text) === 0) {
            $this->footer = null;
            return $this;
        }
        
        if(\mb_strlen($text) > 2048) {
            throw new \InvalidArgumentException('Footer text can not be longer than 2048 characters.');
        }
        
        if($this->exceedsOverallLimit(\mb_strlen($text))) {
            throw new \InvalidArgumentException('Embed text values collectively can not exceed than 6000 characters');
        }
        
        $this->footer = array(
            'text' => $text,
            'icon_url' => $iconurl
        );
        
        return $this;
    }
    
    /**
     * Set the image of this embed.
     * @param string  $url
     * @return $this
     */
    function setImage($url) {
        $this->image = array('url' => (string) $url);
        return $this;
    }
    
    /**
     * Set the thumbnail of this embed.
     * @param string  $url
     * @return $this
     */
    function setThumbnail($url) {
        $this->thumbnail = array('url' => (string) $url);
        return $this;
    }
    
    /**
     * Set the timestamp of this embed.
     * @param int|null  $timestamp
     * @return $this
     * @throws \Exception
     */
    function setTimestamp(?int $timestamp = null) {
        $this->timestamp = (new \DateTime(($timestamp !== null ? '@'.$timestamp : 'now')))->format('c');
        return $this;
    }
    
    /**
     * Set the title of this embed.
     * @param string  $title    Maximum length is 256 characters.
     * @return $this
     * @throws \InvalidArgumentException
     */
    function setTitle(string $title) {
        if(\strlen($title) == 0) {
            $this->title = null;
            return $this;
        }
        
        if(\mb_strlen($title) > 256) {
            throw new \InvalidArgumentException('Embed title can not be longer than 256 characters');
        }
        
        if($this->exceedsOverallLimit(\mb_strlen($title))) {
            throw new \InvalidArgumentException('Embed text values collectively can not exceed than 6000 characters');
        }
        
        $this->title = $title;
        return $this;
    }
    
    /**
     * Set the URL of this embed.
     * @param string  $url
     * @return $this
     */
    function setURL(string $url) {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Adds a field to this embed.
     * @param string  $title    Maximum length is 256 characters.
     * @param string  $value    Maximum length is 1024 characters.
     * @param bool    $inline   Whether this field gets shown with other inline fields on one line.
     * @return $this
     * @throws \RangeException
     * @throws \InvalidArgumentException
     */
    function addField($title, $value, bool $inline = false) {
        if(\count($this->fields) >= 25) {
            throw new \RangeException('Embeds can not have more than 25 fields');
        }
        
        $title = (string) $title;
        $value = (string) $value;
        
        if(\strlen($title) === 0 || \strlen($value) === 0) {
            throw new \InvalidArgumentException('Both embed title and value must not be empty strings');
        }
        
        if(\mb_strlen($title) > 256) {
            throw new \InvalidArgumentException('Embed title can not be longer than 256 characters');
        }
        
        if(\mb_strlen($value) > 1024) {
            throw new \InvalidArgumentException('Embed value can not be longer than 1024 characters');
        }
        
        if($this->exceedsOverallLimit((\mb_strlen($title) + \mb_strlen($value)))) {
            throw new \InvalidArgumentException('Embed text values collectively can not exceed than 6000 characters');
        }
        
        $this->fields[] = array(
            'name' => $title,
            'value' => $value,
            'inline' => $inline
        );
        
        return $this;
    }
    
    /**
     * Checks to see if adding a property has put us over Discord's 6000-char overall limit.
     * @param int  $addition
     * @return bool
     */
    protected function exceedsOverallLimit(int $addition): bool {
        $total = (
            \mb_strlen(($this->title ?? "")) +
            \mb_strlen(($this->description ?? "")) +
            \mb_strlen(($this->footer['text'] ?? "")) +
            \mb_strlen(($this->author['name'] ?? "")) +
            $addition
        );
        
        foreach($this->fields as $field) {
            $total += \mb_strlen($field['name']);
            $total += \mb_strlen($field['value']);
        }
        
        return ($total > 6000);
    }
}
