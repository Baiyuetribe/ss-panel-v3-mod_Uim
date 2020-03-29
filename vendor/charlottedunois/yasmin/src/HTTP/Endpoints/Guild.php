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
 * Handles the API endpoints "Guild".
 * @internal
 */
class Guild {
    /**
     * Endpoints Guilds.
     * @var array
     */
    const ENDPOINTS = array(
        'get' => 'guilds/%s',
        'create' => 'guilds',
        'modify' => 'guilds/%s',
        'delete' => 'guilds/%s',
        'channels' => array(
            'list' => 'guilds/%s/channels',
            'create' => 'guilds/%s/channels',
            'modifyPositions' => 'guilds/%s/channels'
        ),
        'members' => array(
            'get' => 'guilds/%s/members/%s',
            'list' => 'guilds/%s/members',
            'add' => 'guilds/%s/members/%s',
            'modify' => 'guilds/%s/members/%s',
            'modifyCurrentNick' => 'guilds/%s/members/@me/nick',
            'addRole' => 'guilds/%s/members/%s/roles/%s',
            'removeRole' => 'guilds/%s/members/%s/roles/%s',
            'remove' => 'guilds/%s/members/%s'
        ),
        'bans' => array(
            'get' => 'guilds/%s/bans/%s',
            'list' => 'guilds/%s/bans',
            'create' => 'guilds/%s/bans/%s',
            'remove' => 'guilds/%s/bans/%s'
        ),
        'roles' => array(
            'list' => 'guilds/%s/roles',
            'create' => 'guilds/%s/roles',
            'modifyPositions' => 'guilds/%s/roles',
            'modify' => 'guilds/%s/roles/%s',
            'delete' => 'guilds/%s/roles/%s'
        ),
        'prune' => array(
            'count' => 'guilds/%s/prune',
            'begin' => 'guilds/%s/prune'
        ),
        'voice' => array(
            'regions' => 'guilds/%s/regions'
        ),
        'invites' => array(
            'list' => 'guilds/%s/invites'
        ),
        'integrations' => array(
            'list' => 'guilds/%s/integrations',
            'create' => 'guilds/%s/integrations',
            'modify' => 'guilds/%s/integrations/%s',
            'delete' => 'guilds/%s/integrations/%s',
            'sync' => 'guilds/%s/integrations/%s'
        ),
        'embed' => array(
            'get' => 'guilds/%s/embed',
            'modify' => 'guilds/%s/embed'
        ),
        'audit-logs' => 'guilds/%s/audit-logs',
        'vanity-url' => 'guilds/%s/vanity-url'
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
    
    function getGuild(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['get'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function createGuild(array $options) {
        $url = self::ENDPOINTS['create'];
        return $this->api->makeRequest('POST', $url, array('data' => $options));
    }
    
    function modifyGuild(string $guildid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['modify'], $guildid);
        return $this->api->makeRequest('PATCH', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function deleteGuild(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['delete'], $guildid);
        return $this->api->makeRequest('DELETE', $url, array());
    }
    
    function getGuildChannels(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['channels']['list'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function createGuildChannel(string $guildid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['channels']['create'], $guildid);
        return $this->api->makeRequest('POST', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function modifyGuildChannelPositions(string $guildid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['channels']['modifyPositions'], $guildid);
        return $this->api->makeRequest('PATCH', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function getGuildMember(string $guildid, string $userid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['members']['get'], $guildid, $userid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function listGuildMembers(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['members']['list'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function addGuildMember(string $guildid, string $userid, array $options) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['members']['add'], $guildid, $userid);
        return $this->api->makeRequest('PUT', $url, array('data' => $options));
    }
    
    function modifyGuildMember(string $guildid, string $userid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['members']['modify'], $guildid, $userid);
        return $this->api->makeRequest('PATCH', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function removeGuildMember(string $guildid, string $userid, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['members']['remove'], $guildid, $userid);
        return $this->api->makeRequest('DELETE', $url, array('auditLogReason' => $reason));
    }
    
    function modifyCurrentNick(string $guildid, string $userid, string $nick) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['members']['modifyCurrentNick'], $guildid, $userid);
        return $this->api->makeRequest('PATCH', $url, array('data' => array('nick' => $nick)));
    }
    
    function addGuildMemberRole(string $guildid, string $userid, string $roleid, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['members']['addRole'], $guildid, $userid, $roleid);
        return $this->api->makeRequest('PUT', $url, array('auditLogReason' => $reason));
    }
    
    function removeGuildMemberRole(string $guildid, string $userid, string $roleid, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['members']['removeRole'], $guildid, $userid, $roleid);
        return $this->api->makeRequest('DELETE', $url, array('auditLogReason' => $reason));
    }
    
    function getGuildBan(string $guildid, string $userid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['bans']['get'], $guildid, $userid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function getGuildBans(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['bans']['list'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function createGuildBan(string $guildid, string $userid, int $daysDeleteMessages = 0, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['bans']['create'], $guildid, $userid);
        
        $qs = array('delete-message-days' => $daysDeleteMessages);
        if(!empty($reason)) {
            $qs['reason'] = $reason;
        }
        
        return $this->api->makeRequest('PUT', $url, array('auditLogReason' => $reason, 'querystring' => $qs));
    }
    
    function removeGuildBan(string $guildid, string $userid, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['bans']['remove'], $guildid, $userid);
        return $this->api->makeRequest('DELETE', $url, array('auditLogReason' => $reason));
    }
    
    function getGuildRoles(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['roles']['list'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function createGuildRole(string $guildid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['roles']['create'], $guildid);
        return $this->api->makeRequest('POST', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function modifyGuildRolePositions(string $guildid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['roles']['modifyPositions'], $guildid);
        return $this->api->makeRequest('PATCH', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function modifyGuildRole(string $guildid, string $roleid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['roles']['modify'], $guildid, $roleid);
        return $this->api->makeRequest('PATCH', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function deleteGuildRole(string $guildid, string $roleid, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['roles']['delete'], $guildid, $roleid);
        return $this->api->makeRequest('DELETE', $url, array('auditLogReason' => $reason));
    }
    
    function getGuildPruneCount(string $guildid, int $days) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['prune']['count'], $guildid);
        return $this->api->makeRequest('GET', $url, array('querystring' => array('days' => $days)));
    }
    
    function beginGuildPrune(string $guildid, int $days, bool $withCount, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['prune']['begin'], $guildid);
        return $this->api->makeRequest('POST', $url, array('auditLogReason' => $reason, 'querystring' => array('days' => $days, 'compute_prune_count' => $withCount)));
    }
    
    function getGuildVoiceRegions(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['voice']['regions'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function getGuildInvites(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['invites']['list'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function getGuildIntegrations(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['integrations']['list'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function createGuildIntegration(string $guildid, array $options) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['integrations']['create'], $guildid);
        return $this->api->makeRequest('POST', $url, array('data' => $options));
    }
    
    function modifyGuildIntegration(string $guildid, string $integrationid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['integrations']['modify'], $guildid, $integrationid);
        return $this->api->makeRequest('PATCH', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function deleteGuildIntegration(string $guildid, string $integrationid, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['integrations']['delete'], $guildid, $integrationid);
        return $this->api->makeRequest('DELETE', $url, array('auditLogReason' => $reason));
    }
    
    function syncGuildIntegration(string $guildid, string $integrationid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['integrations']['sync'], $guildid, $integrationid);
        return $this->api->makeRequest('POST', $url, array());
    }
    
    function getGuildEmbed(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['embed']['get'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
    
    function modifyGuildEmbed(string $guildid, array $options, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['embed']['modify'], $guildid);
        return $this->api->makeRequest('PATCH', $url, array('auditLogReason' => $reason, 'data' => $options));
    }
    
    function getGuildAuditLog(string $guildid, array $query) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['audit-logs'], $guildid);
        return $this->api->makeRequest('GET', $url, array('querystring' => $query));
    }
    
    function getGuildVanityURL(string $guildid) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['vanity-url'], $guildid);
        return $this->api->makeRequest('GET', $url, array());
    }
}
