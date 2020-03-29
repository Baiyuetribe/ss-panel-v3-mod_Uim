<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\HTTP\Endpoints;

/**
 * Handles the API endpoints "Channel".
 * @internal
 */
class Channel {
    /**
     * Endpoints Channels.
     * @var array
     */
    const ENDPOINTS = array(
        'get' => 'channels/%s',
        'modify' => 'channels/%s',
        'delete' => 'channels/%s',
        'messages' => array(
            'list' => 'channels/%s/messages',
            'get' => 'channels/%s/messages/%s',
            'create' => 'channels/%s/messages',
            'reactions' => array(
                'create' => 'channels/%s/messages/%s/reactions/%s/@me',
                'delete' => 'channels/%s/messages/%s/reactions/%s/%s',
                'get' => 'channels/%s/messages/%s/reactions/%s',
                'deleteAll' => 'channels/%s/messages/%s/reactions',
            ),
            'edit' => 'channels/%s/messages/%s',
            'delete' => 'channels/%s/messages/%s',
            'bulkDelete' => 'channels/%s/messages/bulk-delete'
        ),
        'permissions' => array(
            'edit' => 'channels/%s/permissions/%s',
            'delete' => 'channels/%s/permissions/%s'
        ),
        'invites' => array(
            'list' => 'channels/%s/invites',
            'create' => 'channels/%s/invites'
        ),
        'typing' => 'channels/%s/typing',
        'pins' => array(
            'list' => 'channels/%s/pins',
            'add' => 'channels/%s/pins/%s',
            'delete' => 'channels/%s/pins/%s'
        ),
        'groupDM' => array(
            'add' => 'channels/%s/recipients/%s',
            'remove' => 'channels/%s/recipients/%s'
        )
    );
    
    /**
     * @var \CharlotteDunois\Yasmin\HTTP\APIManager
     */
    protected $api;
    
    /**
     * Constructor.
     * @param \CharlotteDunois\Yasmin\HTTP\APIManager $api
     */
    function __construct(\CharlotteDunois\Yasmin\HTTP\APIManager $api) {
        $this->api = $api;
    }
    
    function getChannel(string $channelid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['get'], $channelid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function modifyChannel(string $channelid, array $data, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['modify'], $channelid);
        return $this->api->makeRequest('PATCH', $url, array('auditLogReason' => $reason, 'data' => $data));
    }
    
    function deleteChannel(string $channelid, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['delete'], $channelid);
        return $this->api->makeRequest('DELETE', $url, array('auditLogReason' => $reason));
    }
    
    function getChannelMessages(string $channelid, array $options = array()) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['list'], $channelid);
        return $this->api->makeRequest('GET', $url, array('querystring' => $options));
    }
    
    function getChannelMessage(string $channelid, string $messageid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['get'], $channelid, $messageid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function createMessage(string $channelid, array $options, array $files = array()) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['create'], $channelid);
        return $this->api->makeRequest('POST', $url, array('data' => $options, 'files' => $files));
    }
    
    function editMessage(string $channelid, string $messageid, array $options) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['edit'], $channelid, $messageid);
        return $this->api->makeRequest('PATCH', $url, array('data' => $options));
    }
    
    function deleteMessage(string $channelid, string $messageid, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['delete'], $channelid, $messageid);
        return $this->api->makeRequest('DELETE', $url, array('auditLogReason' => $reason));
    }
    
    function bulkDeleteMessages(string $channelid, array $snowflakes, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['bulkDelete'], $channelid);
        return $this->api->makeRequest('POST', $url, array('auditLogReason' => $reason, 'data' => array('messages' => $snowflakes)));
    }
    
    function createMessageReaction(string $channelid, string $messageid, string $emoji) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['reactions']['create'], $channelid, $messageid, $emoji);
        return $this->api->makeRequest('PUT', $url, array('reactionRatelimit' => true));
    }
    
    function deleteMessageUserReaction(string $channelid, string $messageid, string $emoji, string $userid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['reactions']['delete'], $channelid, $messageid, $emoji, $userid);
        return $this->api->makeRequest('DELETE', $url, array('reactionRatelimit' => true));
    }
    
    function getMessageReactions(string $channelid, string $messageid, string $emoji, array $querystring = array()) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['reactions']['get'], $channelid, $messageid, $emoji);
        return $this->api->makeRequest('GET', $url, array('querystring' => $querystring));
    }
    
    function deleteMessageReactions(string $channelid, string $messageid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['messages']['reactions']['deleteAll'], $channelid, $messageid);
        return $this->api->makeRequest('DELETE', $url, array());
    }
    
    function editChannelPermissions(string $channelid, string $overwriteid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['permissions']['edit'], $channelid, $overwriteid);
        return $this->api->makeRequest('PUT', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function deleteChannelPermission(string $channelid, string $overwriteid, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['permissions']['delete'], $channelid, $overwriteid);
        return $this->api->makeRequest('DELETE', $url, array('auditLogReason' => $reason));
    }
    
    function getChannelInvites(string $channelid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['invites']['list'], $channelid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function createChannelInvite(string $channelid, array $options = array()) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['invites']['create'], $channelid);
        return $this->api->makeRequest('POST', $url, array('data' => $options));
    }
    
    function triggerChannelTyping(string $channelid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['typing'], $channelid);
        return $this->api->makeRequest('POST', $url, array());
    }
    
    function getPinnedChannelMessages(string $channelid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['pins']['list'], $channelid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function pinChannelMessage(string $channelid, string $messageid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['pins']['add'], $channelid, $messageid);
        return $this->api->makeRequest('PUT', $url, array());
    }
    
    function unpinChannelMessage(string $channelid, string $messageid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['pins']['delete'], $channelid, $messageid);
        return $this->api->makeRequest('DELETE', $url, array());
    }
    
    function groupDMAddRecipient(string $channelid, string $userid, string $accessToken, string $nick) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['groupDM']['add'], $channelid, $userid);
        return $this->api->makeRequest('PUT', $url, array('data' => array('access_token' => $accessToken, 'nick' => $nick)));
    }
    
    function groupDMRemoveRecipient(string $channelid, string $userid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['groupDM']['remove'], $channelid, $userid);
        return $this->api->makeRequest('DELETE', $url, array());
    }
}
