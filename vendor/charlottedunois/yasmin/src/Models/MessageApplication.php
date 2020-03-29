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
 * Represents a message application.
 *
 * @property string       $id           The ID of the application.
 * @property string       $name         The name of the application.
 * @property string|null  $icon         The hash of the application icon, or null.
 * @property string|null  $coverImage   The hash of the application cover image, or null.
 * @property string       $description  The description of the application.
 */
class MessageApplication extends ClientBase {
    /**
     * The ID of the application.
     * @var string
     */
    protected $id;
    
    /**
     * The name of the application.
     * @var string
     */
    protected $name;
    
    /**
     * The hash of the application icon.
     * @var string|null
     */
    protected $icon;
    
    /**
     * The hash of the application cover image.
     * @var string|null
     */
    protected $coverImage;
    
    /**
     * The description of the application.
     * @var string
     */
    protected $description;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, array $application) {
        parent::__construct($client);
        
        $this->id = (string) $application['id'];
        $this->name = (string) $application['name'];
        $this->icon = $application['icon'] ?? null;
        $this->coverImage = $application['cover_image'] ?? null;
        $this->description = (string) $application['description'];
        
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
        
        return parent::__get($name);
    }
    
    /**
     * Returns the URL of the cover image.
     * @param int|null  $size  Any powers of 2 (16-2048).
     * @return string|null
     */
    function getCoverImageURL(?int $size = null) {
        if($size & ($size - 1)) {
            throw new \InvalidArgumentException('Invalid size "'.$size.'", expected any powers of 2');
        }
        
        if($this->coverImage !== null) {
            return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['appicons'], $this->id, $this->coverImage).(!empty($size) ? '?size='.$size : '');
        }
        
        return null;
    }
}
