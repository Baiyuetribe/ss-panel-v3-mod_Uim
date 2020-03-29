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
 * Represents an user's client status.
 *
 * @property string|null  $desktop  The status of the user on the desktop client.
 * @property string|null  $mobile   The status of the user on the mobile client.
 * @property string|null  $web      The status of the user on the web client.
 */
class ClientStatus extends Base {
    /**
     * Client status: online.
     * @var string
     * @source
     */
    const STATUS_ONLINE = 'online';
    
    /**
     * Client status: do not disturb.
     * @var string
     * @source
     */
    const STATUS_DND = 'dnd';
    
    /**
     * Client status: idle.
     * @var string
     * @source
     */
    const STATUS_IDLE = 'idle';
    
    /**
     * Client status: offline.
     * @var string
     * @source
     */
    CONST STATUS_OFFLINE = 'offline';
    
    /**
     * The status of the user on the desktop client.
     * @var string|null
     */
    protected $desktop;
    
    /**
     * The status of the user on the mobile client.
     * @var string|null
     */
    protected $mobile;
    
    /**
     * The status of the user on the web client.
     * @var string|null
     */
    protected $web;

    /**
     * Constructs a new instance.
     * @param array  $clientStatus  An array containing the client status data.
     * @internal
     */
    function __construct(array $clientStatus) {
        $this->desktop = $clientStatus['desktop'] ?? null;
        $this->mobile = $clientStatus['mobile'] ?? null;
        $this->web = $clientStatus['web'] ?? null;
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
     * @return mixed
     * @internal
     */
    function jsonSerialize() {
        return array(
            'desktop' => $this->desktop,
            'mobile' => $this->mobile,
            'web' => $this->web,
        );
    }
}
