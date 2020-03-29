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
 * Channel Storage to store channels, which utilizes Collection.
 */
class ChannelStorage extends Storage implements \CharlotteDunois\Yasmin\Interfaces\ChannelStorageInterface {
    /**
     * Channel Types.
     * @var array
     * @source
     */
    const CHANNEL_TYPES = array(
        0 => 'text',
        1 => 'dm',
        2 => 'voice',
        3 => 'group',
        4 => 'category',
        5 => 'news',
        6 => 'store',
        
        'text' => 0,
        'dm' => 1,
        'voice' => 2,
        'group' => 3,
        'category' => 4,
        'news' => 5,
        'store' => 6
    );
    
    /**
     * Resolves given data to a channel.
     * @param \CharlotteDunois\Yasmin\Interfaces\ChannelInterface|string|int  $channel  string/int = channel ID
     * @return \CharlotteDunois\Yasmin\Interfaces\ChannelInterface
     * @throws \InvalidArgumentException
     */
    function resolve($channel) {
        if($channel instanceof \CharlotteDunois\Yasmin\Interfaces\ChannelInterface) {
            return $channel;
        }
        
        if(\is_int($channel)) {
            $channel = (string) $channel;
        }
        
        if(\is_string($channel) && parent::has($channel)) {
            return parent::get($channel);
        }
        
        throw new \InvalidArgumentException('Unable to resolve unknown channel');
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
     * @return \CharlotteDunois\Yasmin\Interfaces\ChannelInterface|null
     */
    function get($key) {
        return parent::get($key);
    }
    
    /**
     * {@inheritdoc}
     * @param string                                               $key
     * @param \CharlotteDunois\Yasmin\Interfaces\ChannelInterface  $value
     * @return $this
     */
    function set($key, $value) {
        parent::set($key, $value);
        if($this !== $this->client->channels) {
            $this->client->channels->set($key, $value);
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
        if($this !== $this->client->channels) {
            $this->client->channels->delete($key);
        }
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * @return $this
     */
    function clear() {
        if($this !== $this->client->channels) {
            foreach($this->data as $key => $val) {
                $this->client->channels->delete($key);
                unset($val);
            }
        }
        
        parent::clear();
        return $this;
    }
    
    /**
     * Factory to create (or retrieve existing) channels.
     * @param array                                      $data
     * @param \CharlotteDunois\Yasmin\Models\Guild|null  $guilds
     * @return \CharlotteDunois\Yasmin\Interfaces\ChannelInterface
     * @throws \CharlotteDunois\Yasmin\DiscordException
     * @internal
     */
    function factory(array $data, ?\CharlotteDunois\Yasmin\Models\Guild $guild = null) {
        if($guild === null) {
            $guild = (!empty($data['guild_id']) ? $this->client->guilds->get($data['guild_id']) : null);
        }
        
        if(parent::has($data['id'])) {
            $channel = parent::get($data['id']);
            $channel->_patch($data);
            return $channel;
        }
        
        switch($data['type']) {
            default:
                throw new \CharlotteDunois\Yasmin\DiscordException('Unknown channel type');
            break;
            case 0:
                if($guild === null) {
                    throw new \CharlotteDunois\Yasmin\DiscordException('Unknown guild for guild channel');
                }
                
                $channel = new \CharlotteDunois\Yasmin\Models\TextChannel($this->client, $guild, $data);
            break;
            case 1:
                $channel = new \CharlotteDunois\Yasmin\Models\DMChannel($this->client, $data);
            break;
            case 2:
                if($guild === null) {
                    throw new \CharlotteDunois\Yasmin\DiscordException('Unknown guild for guild channel');
                }
                
                $channel = new \CharlotteDunois\Yasmin\Models\VoiceChannel($this->client, $guild, $data);
            break;
            case 3:
                $channel = new \CharlotteDunois\Yasmin\Models\GroupDMChannel($this->client, $data);
            break;
            case 4:
                if($guild === null) {
                    throw new \CharlotteDunois\Yasmin\DiscordException('Unknown guild for guild channel');
                }
                
                $channel = new \CharlotteDunois\Yasmin\Models\CategoryChannel($this->client, $guild, $data);
            break;
            case 6:
                if($guild === null) {
                    throw new \CharlotteDunois\Yasmin\DiscordException('Unknown guild for guild channel');
                }
                
                $channel = new \CharlotteDunois\Yasmin\Models\GuildStoreChannel($this->client, $guild, $data);
            break;
        }
        
        $this->set($channel->id, $channel);
        
        if($guild) {
            $guild->channels->set($channel->id, $channel);
        }
        
        return $channel;
    }
    
    /**
     * Get the type for the channel.
     * @param \CharlotteDunois\Yasmin\Interfaces\ChannelInterface  $channel
     * @return int
     */
    static function getTypeForChannel(\CharlotteDunois\Yasmin\Interfaces\ChannelInterface $channel) {
        if($channel instanceof \CharlotteDunois\Yasmin\Interfaces\GroupDMChannelInterface) {
            return self::CHANNEL_TYPES['group'];
        } elseif($channel instanceof \CharlotteDunois\Yasmin\Interfaces\DMChannelInterface) {
            return self::CHANNEL_TYPES['dm'];
        } elseif($channel instanceof \CharlotteDunois\Yasmin\Interfaces\GuildVoiceChannelInterface) {
            return self::CHANNEL_TYPES['voice'];
        } elseif($channel instanceof \CharlotteDunois\Yasmin\Interfaces\CategoryChannelInterface) {
            return self::CHANNEL_TYPES['category'];
        } elseif($channel instanceof \CharlotteDunois\Yasmin\Interfaces\GuildNewsChannelInterface) {
            return self::CHANNEL_TYPES['news'];
        } elseif($channel instanceof \CharlotteDunois\Yasmin\Interfaces\GuildStoreChannelInterface) {
            return self::CHANNEL_TYPES['store'];
        }
        
        return self::CHANNEL_TYPES['text'];
    }
}
