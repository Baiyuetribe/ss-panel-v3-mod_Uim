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
 * Represents a presence.
 *
 * @property \CharlotteDunois\Yasmin\Models\Activity|null       $activity        The current activity the user is doing, or null.
 * @property \CharlotteDunois\Yasmin\Models\Activity[]          $activities      All activities the user is doing.
 * @property string                                             $status          What do you expect this to be?
 * @property \CharlotteDunois\Yasmin\Models\ClientStatus|null   $clientStatus    The client's status on desktop/mobile/web, or null.
 * @property string                                             $userID          The user ID this presence belongs to.
 *
 * @property \CharlotteDunois\Yasmin\Models\User|null           $user            The user this presence belongs to.
 */
class Presence extends ClientBase {
    /**
     * The user ID this presence belongs to.
     * @var string
     */
    protected $userID;
    
    /**
     * The current activity the user is doing, or null.
     * @var \CharlotteDunois\Yasmin\Models\Activity
     */
    protected $activity;
    
    /**
     * What do you expect this to be?
     * @var string
     */
    protected $status;

    /**
     * The client's status for desktop/mobile/web or null.
     * @var \CharlotteDunois\Yasmin\Models\ClientStatus|null
     */
    protected $clientStatus;
    
    /**
     * All activities the user is doing.
     * @var \CharlotteDunois\Yasmin\Models\Activity[]
     */
    protected $activities = array();

    /**
     * The manual creation of such an instance is discouraged. There may be an easy and safe way to create such an instance in the future.
     * @param \CharlotteDunois\Yasmin\Client  $client      The client this instance is for.
     * @param array                           $presence    An array containing user (as array, with an element id), activity (as array) and status.
     *
     * @throws \RuntimeException
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, array $presence) {
        parent::__construct($client);
        $this->userID = $this->client->users->patch($presence['user'])->id;
        
        $this->_patch($presence);
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
            case 'user':
                return $this->client->users->get($this->userID);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * @return mixed
     * @internal
     */
     function jsonSerialize() {
         return array(
             'status' => $this->status,
             'clientStatus' => $this->clientStatus,
             'game' => $this->activity
         );
     }
     
     /**
      * @return void
      * @internal
      */
     function _patch(array $presence) {
         $this->activity = (!empty($presence['game']) ? (new \CharlotteDunois\Yasmin\Models\Activity($this->client, $presence['game'])) : null);
         $this->status = $presence['status'];
         $this->clientStatus = (!empty($presence['client_status']) ? (new \CharlotteDunois\Yasmin\Models\ClientStatus($presence['client_status'])) : null);
         $this->activities = (!empty($presence['activities']) ? \array_map(function (array $activitiy) {
             return (new \CharlotteDunois\Yasmin\Models\Activity($this->client, $activitiy));
         }, $presence['activities']) : array());
     }
}
