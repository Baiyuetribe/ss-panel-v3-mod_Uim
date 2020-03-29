<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\HTTP;

/**
 * Handles the API endpoints.
 * @internal
 */
class APIEndpoints {
    /**
     * CDN constants.
     * @var array
     * @internal
     */
    const CDN = array(
        'url' => 'https://cdn.discordapp.com/',
        'emojis' => 'emojis/%s.%s',
        'icons' => 'icons/%s/%s.%s',
        'splashes' => 'splashes/%s/%s.%s',
        'defaultavatars' => 'embed/avatars/%s.%s',
        'avatars' => 'avatars/%s/%s.%s',
        'appicons' => 'app-icons/%s/%s.png',
        'appassets' => 'app-assets/%s/%s.png',
        'channelicons' => 'channel-icons/%s/%s.png',
        'guildbanners' => 'banners/%s/%s.%s'
    );
    
    /**
     * HTTP constants.
     * @var array
     * @internal
     */
    const HTTP = array(
        'url' => 'https://discordapp.com/api/',
        'version' => 7,
        'invite' => 'https://discord.gg/'
    );
    
    /**
     * Endpoints General.
     * @var array
     * @internal
     */
    const ENDPOINTS = array(
        'currentOAuthApplication' => 'oauth2/applications/@me'
    );
    
    /**
     * The API manager.
     * @var \CharlotteDunois\Yasmin\HTTP\APIManager
     */
    protected $api;
    
    /**
     * The channel endpoints.
     * @var \CharlotteDunois\Yasmin\HTTP\Endpoints\Channel
     */
    public $channel;
    
    /**
     * The emoji endpoints.
     * @var \CharlotteDunois\Yasmin\HTTP\Endpoints\Emoji
     */
    public $emoji;
    
    /**
     * The guild endpoints.
     * @var \CharlotteDunois\Yasmin\HTTP\Endpoints\Guild
     */
    public $guild;
    
    /**
     * The invite endpoints.
     * @var \CharlotteDunois\Yasmin\HTTP\Endpoints\Invite
     */
    public $invite;
    
    /**
     * The user endpoints.
     * @var \CharlotteDunois\Yasmin\HTTP\Endpoints\User
     */
    public $user;
    
    /**
     * The voice endpoints.
     * @var \CharlotteDunois\Yasmin\HTTP\Endpoints\Voice
     */
    public $voice;
    
    /**
     * The webhook endpoints.
     * @var \CharlotteDunois\Yasmin\HTTP\Endpoints\Webhook
     */
    public $webhook;
    
    
    /**
     * DO NOT initialize this class yourself.
     * @param \CharlotteDunois\Yasmin\HTTP\APIManager $api
     */
    function __construct(\CharlotteDunois\Yasmin\HTTP\APIManager $api) {
        $this->api = $api;
        
        $this->channel = new \CharlotteDunois\Yasmin\HTTP\Endpoints\Channel($api);
        $this->emoji = new \CharlotteDunois\Yasmin\HTTP\Endpoints\Emoji($api);
        $this->guild = new \CharlotteDunois\Yasmin\HTTP\Endpoints\Guild($api);
        $this->invite = new \CharlotteDunois\Yasmin\HTTP\Endpoints\Invite($api);
        $this->user = new \CharlotteDunois\Yasmin\HTTP\Endpoints\User($api);
        $this->voice = new \CharlotteDunois\Yasmin\HTTP\Endpoints\Voice($api);
        $this->webhook = new \CharlotteDunois\Yasmin\HTTP\Endpoints\Webhook($api);
    }
    
    /**
     * Gets the current OAuth application.
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function getCurrentApplication() {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::ENDPOINTS['currentOAuthApplication'];
        return $this->api->makeRequest('GET', $url, array());
    }
    
    /**
     * Formats Endpoints strings.
     * @param string  $endpoint
     * @param string  ...$args
     * @return string
     */
    static function format(string $endpoint, ...$args) {
        return \sprintf($endpoint, ...$args);
    }
}
