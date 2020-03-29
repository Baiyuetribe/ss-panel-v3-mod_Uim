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
 * Represents a Group DM channel.
 *
 * @property string|null  $applicationID  The application which created the group DM channel.
 * @property string|null  $icon           The icon of the Group DM channel.
 */
class GroupDMChannel extends DMChannel implements \CharlotteDunois\Yasmin\Interfaces\GroupDMChannelInterface {
    /**
     * The application which created the group DM channel.
     * @var string|null
     */
    protected $applicationID;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, array $channel) {
        parent::__construct($client, $channel);
        
        $this->applicationID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($channel['application_id'] ?? null), 'string');
        $this->icon = $channel['icon'] ?? null;
    }
    
    /**
     * Adds the given user to the Group DM channel using the given access token. Resolves with $this.
     * @param string|\CharlotteDunois\Yasmin\Models\User  $user         The User instance, or the user ID.
     * @param string                                      $accessToken  The OAuth 2.0 access token for the user.
     * @param string                                      $nick         The nickname of the user being added.
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function addRecipient($user, string $accessToken, string $nick = '') {
        if($user instanceof \CharlotteDunois\Yasmin\Models\User) {
            $user = $user->id;
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($user, $accessToken, $nick) {
            $this->client->apimanager()->endpoints->channel->groupDMAddRecipient($this->id, $user, $accessToken, $nick)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
    }
    
    /**
     * Returns the group DM's icon URL, or null.
     * @param int|null  $size    One of 128, 256, 512, 1024 or 2048.
     * @param string    $format  One of png, jpg or webp.
     * @return string|null
     */
    function getIconURL(?int $size = null, string $format = 'png') {
        if($this->icon !== null) {
            return \CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['url'].\CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(\CharlotteDunois\Yasmin\HTTP\APIEndpoints::CDN['channelicons'], $this->id, $this->icon, $format).(!empty($size) ? '?size='.$size : '');
        }
        
        return null;
    }
    
    /**
     * Removes the given user from the Group DM channel. Resolves with $this.
     * @param string|\CharlotteDunois\Yasmin\Models\User  $user  The User instance, or the user ID.
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function removeRecipient($user) {
        if($user instanceof \CharlotteDunois\Yasmin\Models\User) {
            $user = $user->id;
        }
        
        return (new \React\Promise\Promise(function (callable $resolve, callable $reject) use ($user) {
            $this->client->apimanager()->endpoints->channel->groupDMRemoveRecipient($this->id, $user)->done(function () use ($resolve) {
                $resolve($this);
            }, $reject);
        }));
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
     * @return void
     * @internal
     */
    function _patch(array $channel) {
        $this->applicationID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($channel['application_id'] ?? $this->applicationID ?? null), 'string');
        $this->icon = $channel['icon'] ?? null;
        
        $this->ownerID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($channel['owner_id'] ?? $this->ownerID ?? null), 'string');
        $this->lastMessageID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($channel['last_message_id'] ?? $this->lastMessageID ?? null), 'string');
        
        if(isset($channel['recipients'])) {
            $this->recipients->clear();
            
            foreach($channel['recipients'] as $rec) {
                $user = $this->client->users->patch($rec);
                if($user) {
                    $this->recipients->set($user->id, $user);
                }
            }
        }
    }
}
