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
 * Rich Presence assets.
 *
 * @property \CharlotteDunois\Yasmin\Models\Activity  $activity    The activity which these assets belong to.
 * @property string|null                              $largeImage  The ID of the large image, or null.
 * @property string|null                              $largeText   The text of the large image, or null.
 * @property string|null                              $smallImage  The ID of the small image, or null.
 * @property string|null                              $smallText   The text of the small image, or null.
 */
class RichPresenceAssets extends ClientBase {
    /**
     * The activity which these assets belong to.
     * @var \CharlotteDunois\Yasmin\Models\Activity
     */
    protected $activity;
    
    /**
     * The ID of the large image, or null.
     * @var string|null
     */
    protected $largeImage;
    
    /**
     * The text of the large image, or null.
     * @var string|null
     */
    protected $largeText;
    
    /**
     * The ID of the small image, or null.
     * @var string|null
     */
    protected $smallImage;
    
    /**
     * The text of the small image, or null.
     * @var string|null
     */
    protected $smallText;
    
    /**
     * The manual creation of such an instance is discouraged. There may be an easy and safe way to create such an instance in the future.
     * @param \CharlotteDunois\Yasmin\Client           $client      The client this instance is for.
     * @param \CharlotteDunois\Yasmin\Models\Activity  $activity    The activity instance.
     * @param array                                    $assets      An array containing the presence data.
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\Models\Activity $activity, array $assets) {
        parent::__construct($client);
        $this->activity = $activity;
        
        $this->largeImage = $assets['large_image'] ?? null;
        $this->largeText = $assets['large_text'] ?? null;
        $this->smallImage = $assets['small_image'] ?? null;
        $this->smallText = $assets['small_text'] ?? null;
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
     * Returns the URL of the large image.
     * @param int|null  $size  Any powers of 2 (16-2048).
     * @return string|null
     */
    function getLargeImageURL(?int $size = null) {
        if($this->largeImage !== null) {
            if($size & ($size - 1)) {
                throw new \InvalidArgumentException('Invalid size "'.$size.'", expected any powers of 2');
            }
            
            if(\strpos($this->largeImage, 'spotify:') === 0) {
                return 'https://i.scdn.co/image/'.\substr($this->largeImage, 8);
            }
            
            return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['appassets'], $this->activity->applicationID, $this->largeImage).(!empty($size) ? '?size='.$size : '');
        }
        
        return null;
    }
    
    /**
     * Returns the URL of the small image.
     * @param int|null  $size  Any powers of 2 (16-2048).
     * @return string|null
     */
    function getSmallImageURL(?int $size = null) {
        if($this->smallImage !== null) {
            if($size & ($size - 1)) {
                throw new \InvalidArgumentException('Invalid size "'.$size.'", expected any powers of 2');
            }
            
            if(\strpos($this->smallImage, 'spotify:') === 0) {
                return 'https://i.scdn.co/image/'.\substr($this->smallImage, 8);
            }
            
            return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['appassets'], $this->activity->applicationID, $this->smallImage).(!empty($size) ? '?size='.$size : '');
        }
        
        return null;
    }
    
    /**
     * @return mixed
     * @internal
     */
    function jsonSerialize() {
        return array(
            'large_image' => $this->largeImage,
            'large_text' => $this->largeText,
            'small_image' => $this->smallImage,
            'small_text' => $this->smallText
        );
    }
}
