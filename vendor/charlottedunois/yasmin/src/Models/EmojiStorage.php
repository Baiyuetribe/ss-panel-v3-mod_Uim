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
 * Emoji Storage to store emojis, utilizes Collection.
 */
class EmojiStorage extends Storage implements \CharlotteDunois\Yasmin\Interfaces\EmojiStorageInterface {
    /**
     * The guild this storage belongs to.
     * @var \CharlotteDunois\Yasmin\Models\Guild
     */
    protected $guild;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, ?\CharlotteDunois\Yasmin\Models\Guild $guild = null, ?array $data = null) {
        parent::__construct($client, $data);
        $this->guild = $guild;
        
        $this->baseStorageArgs[] = $this->guild;
    }
    
    /**
     * Resolves given data to an emoji.
     * @param \CharlotteDunois\Yasmin\Models\Emoji|\CharlotteDunois\Yasmin\Models\MessageReaction|string|int  $emoji  string/int = emoji ID
     * @return \CharlotteDunois\Yasmin\Models\Emoji
     * @throws \InvalidArgumentException
     */
    function resolve($emoji) {
        if($emoji instanceof \CharlotteDunois\Yasmin\Models\Emoji) {
            return $emoji;
        }
        
        if($emoji instanceof \CharlotteDunois\Yasmin\Models\MessageReaction) {
            return $emoji->emoji;
        }
        
        if(\is_int($emoji)) {
            $emoji = (string) $emoji;
        }
        
        if(\is_string($emoji) && parent::has($emoji)) {
            return parent::get($emoji);
        }
        
        throw new \InvalidArgumentException('Unable to resolve unknown emoji');
    }
    
    /**
     * {@inheritdoc}
     * @param string  $key
     * @return bool
     */
    function has($key) {
        return parent::has($key);
    }
    
    /**
     * {@inheritdoc}
     * @param string  $key
     * @return \CharlotteDunois\Yasmin\Models\Emoji|null
     */
    function get($key) {
        return parent::get($key);
    }
    
    /**
     * {@inheritdoc}
     * @param string                                $key
     * @param \CharlotteDunois\Yasmin\Models\Emoji  $value
     * @return $this
     */
    function set($key, $value) {
        parent::set($key, $value);
        if($this !== $this->client->emojis) {
            $this->client->emojis->set($key, $value);
        }
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * @param string  $key
     * @return $this
     */
    function delete($key) {
        parent::delete($key);
        if($this !== $this->client->emojis) {
            $this->client->emojis->delete($key);
        }
        
        return $this;
    }
    
    /**
     * Factory to create (or retrieve existing) emojis.
     * @param array  $data
     * @return \CharlotteDunois\Yasmin\Models\Emoji
     * @internal
     */
    function factory(array $data) {
        if(parent::has($data['id'])) {
            $emoji = parent::get($data['id']);
            $emoji->_patch($data);
            return $emoji;
        }
        
        $emoji = new \CharlotteDunois\Yasmin\Models\Emoji($this->client, $this->guild, $data);
        
        if($emoji->id !== null) {
            $this->set($emoji->id, $emoji);
            $this->client->emojis->set($emoji->id, $emoji);
        }
        
        return $emoji;
    }
}
