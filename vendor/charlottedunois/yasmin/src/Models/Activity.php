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
 * Something someone does.
 *
 * @property string                                                  $name           The name of the activity.
 * @property int                                                     $type           The activity type.
 * @property string|null                                             $url            The stream url, if streaming.
 *
 * @property string|null                                             $applicationID  The application ID associated with the activity, or null.
 * @property \CharlotteDunois\Yasmin\Models\RichPresenceAssets|null  $assets         Assets for rich presence, or null.
 * @property string|null                                             $details        Details about the activity, or null.
 * @property array|null                                              $party          Party of the activity, an array in the format `[ 'id' => string, 'size' => [ size (int), max (int|null) ]|null ]`, or null.
 * @property string|null                                             $state          State of the activity, or null.
 * @property array|null                                              $timestamps     Timestamps for the activity, an array in the format `[ 'start' => \DateTime|null, 'end' => \DateTime|null ]`, or null.
 * @property int|null                                                $flags          The activity flags (as bitfield), like if an activity is a spectate activity.
 * @property string|null                                             $sessionID      The ID that links to the activity session.
 * @property string|null                                             $syncID         The sync ID. For spotify, this is the spotify track ID.
 *
 * @property bool                                                    $streaming      Whether or not the activity is being streamed.
 */
class Activity extends ClientBase {
    /**
     * The Activity flags.
     * @var array
     * @source
     */
    const FLAGS = array(
        'INSTANCE' => 1,
        'JOIN' => 2,
        'SPECTATE' => 4,
        'JOIN_REQUEST' => 8,
        'SYNC' => 16,
        'PLAY' => 32
    );
    
    /**
     * The Activity types.
     * @var array
     * @source
     */
    const TYPES = array(
        0 => 'playing',
        1 => 'streaming',
        2 => 'listening',
        3 => 'watching'
    );
    
    /**
     * The name of the activity.
     * @var string
     */
    protected $name;
    
    /**
     * The activity type.
     * @var int
     */
    protected $type;
    
    /**
     * The stream url, if streaming.
     * @var string|null
     */
    protected $url;
    
    /**
     * The application ID associated with the activity, or null.
     * @var string|null
     */
    protected $applicationID;
    
    /**
     * Assets for rich presence, or null.
     * @var \CharlotteDunois\Yasmin\Models\RichPresenceAssets|null
     */
    protected $assets;
    
    /**
     * Details about the activity, or null.
     * @var string|null
     */
    protected $details;
    
    /**
     * Party of the activity, or null.
     * @var array|null
     */
    protected $party;
    
    /**
     * State of the activity, or null.
     * @var string|null
     */
    protected $state;
    
    /**
     * Timestamps for the activity, or null.
     * @var array|null
     */
    protected $timestamps;
    
    /**
     * The activity flags (as bitfield), like if an activity is a spectate activity.
     * @var int|null
     */
    protected $flags;
    
    /**
     * The ID that links to the activity session.
     * @var string|null
     */
    protected $sessionID;
    
    /**
     * The sync ID. For spotify, this is the spotify track ID.
     * @var string|null
     */
    protected $syncID;
    
    /**
     * The manual creation of such a class is discouraged. There may be an easy and safe way to create such a class in the future.
     * @param \CharlotteDunois\Yasmin\Client  $client      The client this instance is for.
     * @param array                           $activity    An array containing name, type (as int) and url (nullable).
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, array $activity) {
        parent::__construct($client);
        
        $this->name = (string) $activity['name'];
        $this->type = (int) $activity['type'];
        $this->url = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($activity['url'] ?? null), 'string');
        
        $this->applicationID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($activity['application_id'] ?? null), 'string');
        $this->details = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($activity['details'] ?? null), 'string');
        $this->party = (!empty($activity['party']) ?
            array(
                ((string) ($activity['party']['id'] ?? '')),
                (!empty($activity['party']['size']) ?
                    array(
                        ((int) $activity['party']['size'][0]),
                        (isset($activity['party']['size'][1]) ? ((int) $activity['party']['size'][1]) : null)
                    ) : null)
            ) : null);
        $this->state = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($activity['state'] ?? null), 'string');
        
        $this->assets = (!empty($activity['assets']) ? (new \CharlotteDunois\Yasmin\Models\RichPresenceAssets($this->client, $this, $activity['assets'])) : null);
        $this->timestamps = (!empty($activity['timestamps']) ? array(
            'start' => (!empty($activity['timestamps']['start']) ? \CharlotteDunois\Yasmin\Utils\DataHelpers::makeDateTime(((int) (((int) $activity['timestamps']['start']) / 1000))) : null),
            'end' => (!empty($activity['timestamps']['end']) ? \CharlotteDunois\Yasmin\Utils\DataHelpers::makeDateTime(((int) (((int) $activity['timestamps']['end']) / 1000))) : null)
        ) : null);
        
        $this->flags = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($activity['flags'] ?? null), 'int');
        $this->sessionID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($activity['session_id'] ?? null), 'string');
        $this->syncID = \CharlotteDunois\Yasmin\Utils\DataHelpers::typecastVariable(($activity['sync_id'] ?? null), 'string');
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
            case 'streaming':
                return ($this->type === 1);
            break;
        }
        
        return parent::__get($name);
    }
    
    /**
     * Whether this activity is a rich presence.
     * @return bool
     */
    function isRichPresence() {
        return ($this->applicationID !== null || $this->party !== null || $this->sessionID !== null || $this->syncID !== null);
    }
    
    /**
     * @return mixed
     * @internal
     */
    function jsonSerialize() {
        return array(
            'name' => $this->name,
            'type' => $this->type,
            'url' => $this->url
        );
    }
}
