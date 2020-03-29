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
 * Handles the API endpoints "Invite".
 * @internal
 */
class Invite {
    /**
     * Endpoints Invites.
     * @var array
     */
    const ENDPOINTS = array(
        'get' => 'invites/%s',
        'delete' => 'invites/%s',
        'accept' => 'invites/%s'
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
    
    function getInvite(string $code, bool $withCounts = false) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['get'], $code);
        
        $opts = array();
        if($withCounts) {
            $opts['querystring'] = array('with_counts' => 'true');
        }
        
        return $this->api->makeRequest('GET', $url, $opts);
    }
    
    function deleteInvite(string $code, string $reason = '') {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['delete'], $code);
        return $this->api->makeRequest('DELETE', $url, array('auditLogReason' => $reason));
    }
    
    function acceptInvite(string $code) {
        $url = \CharlotteDunois\Yasmin\HTTP\APIEndpoints::format(self::ENDPOINTS['accept'], $code);
        return $this->api->makeRequest('POST', $url, array());
    }
}
