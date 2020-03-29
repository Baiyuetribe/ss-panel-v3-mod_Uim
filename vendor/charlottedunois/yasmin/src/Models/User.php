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
 * Represents an user on Discord.
 *
 * @property string                                               $id                 The user ID.
 * @property string                                               $username           The username.
 * @property string                                               $discriminator      The discriminator of this user.
 * @property bool                                                 $bot                Is the user a bot? Or are you a bot?
 * @property string|null                                          $avatar             The hash of the user's avatar, or null.
 * @property string                                               $email              An email address or maybe nothing at all. More likely to be nothing at all.
 * @property bool|null                                            $mfaEnabled         Whether the user has two factor enabled on their account, or null if no information provided.
 * @property bool|null                                            $verified           Whether the email on this account has been verified, or null if no information provided.
 * @property bool                                                 $webhook            Determines wether the user is a webhook or not.
 * @property int                                                  $createdTimestamp   The timestamp of when this user was created.
 *
 * @property \DateTime                                            $createdAt          An DateTime instance of the createdTimestamp.
 * @property string                                               $tag                Username#Discriminator.
 */
class User extends ClientBase {
    /**
     * The user ID.
     * @var string
     */
    protected $id;
    
    /**
     * The username.
     * @var string
     */
    protected $username;
    
    /**
     * The discriminator of this user.
     * @var string
     */
    protected $discriminator;
    
    /**
     * Is the user a bot? Or are you a bot?
     * @var bool
     */
    protected $bot;
    
    /**
     * The hash of the user's avatar, or null.
     * @var string|null
     */
    protected $avatar;
    
    /**
     * An email address or maybe nothing at all. More likely to be nothing at all.
     * @var string
     */
    protected $email;
    
    /**
     * Whether the user has two factor enabled on their account, or null if no information provided.
     * @var bool|null
     */
    protected $mfaEnabled;
    
    /**
     * Whether the email on this account has been verified, or null if no information provided.
     * @var bool|null
     */
    protected $verified;
    
    /**
     * Determines wether the user is a webhook or not.
     * @var bool
     */
    protected $webhook;
    
    /**
     * The timestamp of when this user was created.
     * @var int
     */
    protected $createdTimestamp;
    
    /**
     * Whether the user fetched this user.
     * @var bool
     */
    protected $userFetched = false;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, array $user, bool $isWebhook = false, bool $userFetched = false) {
        parent::__construct($client);
        
        $this->id = (string) $user['id'];
        $this->webhook = $isWebhook;
        $this->userFetched = $userFetched;
        
        $this->createdTimestamp = (int) \CharlotteDunois\Yasmin\Utils\Snowflake::deconstruct($this->id)->timestamp;
        $this->_patch($user);
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
            case 'tag':
                return $this->username.'#'.$this->discriminator;
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * @return mixed
     * @internal
     */
    function __debugInfo() {
        $vars = parent::__debugInfo();
        unset($vars['userFetched']);
        return $vars;
    }
    
    /**
     * Opens a DM channel to this user. Resolves with an instance of DMChannel.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\DMChannel
     */
    function createDM() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            $channel = $this->client->channels->first(function ($channel) {
                return (
                    $channel instanceof \CharlotteDunois\Yasmin\Interfaces\DMChannelInterface &&
                    !($channel instanceof \CharlotteDunois\Yasmin\Interfaces\GroupDMChannelInterface) &&
                    $channel->isRecipient($this)
                );
            });
            
            if($channel) {
                return $resolve($channel);
            }
            
            $this->client->apimanager()->endpoints->user->createUserDM($this->id)->done(function ($data) use ($resolve) {
                $channel = $this->client->channels->factory($data);
                $resolve($channel);
            }, $reject);
        }));
    }
    
    /**
     * Deletes an existing DM channel to this user. Resolves with $this.
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function deleteDM() {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) {
            $channel = $this->client->channels->first(function ($channel) {
                return ($channel instanceof \CharlotteDunois\Yasmin\Interfaces\DMChannelInterface && $channel->isRecipient($this));
            });
            
            if(!$channel) {
                return $resolve($this);
            }
            
            $this->client->apimanager()->endpoints->channel->deleteChannel($channel->id)->done(function () use ($channel, $resolve) {
                $this->client->channels->delete($channel->id);
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Get the default avatar URL.
     * @param int|null  $size    Any powers of 2 (16-2048).
     * @return string
     * @throws \InvalidArgumentException Thrown if $size is not a power of 2
     */
    function getDefaultAvatarURL(?int $size = 1024) {
        if(!\CharlotteDunois\Yasmin\Utils\ImageHelpers::isPowerOfTwo($size)) {
            throw new \InvalidArgumentException('Invalid size "'.$size.'", expected any powers of 2');
        }
        
        return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['defaultavatars'], ($this->discriminator % 5), 'png').(!empty($size) ? '?size='.$size : '');
    }
    
    /**
     * Get the avatar URL.
     * @param int|null  $size    Any powers of 2 (16-2048).
     * @param string    $format  One of png, webp, jpg or gif (empty = default format).
     * @return string|null
     * @throws \InvalidArgumentException Thrown if $size is not a power of 2
     */
    function getAvatarURL(?int $size = 1024, string $format = '') {
        if(!\CharlotteDunois\Yasmin\Utils\ImageHelpers::isPowerOfTwo($size)) {
            throw new \InvalidArgumentException('Invalid size "'.$size.'", expected any powers of 2');
        }
        
        if($this->avatar === null) {
            return null;
        }
        
        if(empty($format)) {
            $format = \CharlotteDunois\Yasmin\Utils\ImageHelpers::getImageExtension($this->avatar);
        }
        
        return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['avatars'], $this->id, $this->avatar, $format).(!empty($size) ? '?size='.$size : '');
    }
    
    /**
     * Get the URL of the displayed avatar.
     * @param int|null  $size    Any powers of 2 (16-2048).
     * @param string    $format  One of png, webp, jpg or gif (empty = default format).
     * @return string
     * @throws \InvalidArgumentException Thrown if $size is not a power of 2
     */
    function getDisplayAvatarURL(?int $size = 1024, string $format = '') {
        return ($this->avatar ? $this->getAvatarURL($size, $format) : $this->getDefaultAvatarURL($size));
    }
    
    /**
     * Gets the presence for this user, or null.
     * @return \CharlotteDunois\Yasmin\Models\Presence|null
     */
    function getPresence() {
        if($this->client->presences->has($this->id)) {
            return $this->client->presences->get($this->id);
        }
        
        foreach($this->client->guilds as $guild) {
            if($guild->presences->has($this->id)) {
                $presence = $guild->presences->get($this->id);
                $this->client->presences->set($this->id, $presence);
                
                return $presence;
            }
        }
        
        return null;
    }
    
    /**
     * Fetches the User's connections. Requires connections scope. Resolves with a Collection of UserConnection instances, mapped by their ID.
     * @param string  $accessToken
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\UserConnection
     */
    function fetchUserConnections(string $accessToken) {
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($accessToken) {
            $this->client->apimanager()->endpoints->user->getUserConnections($accessToken)->done(function ($data) use ($resolve) {
                $collect = new \CharlotteDunois\Collect\Collection();
                foreach($data as $conn) {
                    $connection = new \CharlotteDunois\Yasmin\Models\UserConnection($this->client, $this, $conn);
                    $collect->set($connection->id, $connection);
                }
                
                $resolve($collect);
            }, $reject);
        }));
    }
    
    /**
     * Automatically converts the User instance to a mention.
     * @return string
     */
    function __toString() {
        return '<@'.$this->id.'>';
    }
    
    /**
     * @return void
     * @internal
     */
    function _patch(array $user) {
        $this->username = (string) $user['username'];
        $this->discriminator = (string) ($user['discriminator'] ?? '0000');
        $this->bot = (!empty($user['bot']));
        $this->avatar = $user['avatar'] ?? null;
        $this->email = (string) ($user['email'] ?? '');
        $this->mfaEnabled = (isset($user['mfa_enabled']) ? !empty($user['mfa_enabled']) : null);
        $this->verified = (isset($user['verified']) ? !empty($user['verified']) : null);
    }
}
