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
 * Represents a guild ban.
 *
 * @property \CharlotteDunois\Yasmin\Models\Guild  $guild   The guild this ban is from.
 * @property \CharlotteDunois\Yasmin\Models\User   $user    The banned user.
 * @property string|null                           $reason  The ban reason, or null.
 */
class GuildBan extends ClientBase {
    /**
     * The guild this ban is from.
     * @var \CharlotteDunois\Yasmin\Models\Guild
     */
    protected $guild;
    
    /**
     * The banned user.
     * @var \CharlotteDunois\Yasmin\Models\User
     */
    protected $user;
    
    /**
     * The ban reason, or null.
     * @var string|null
     */
    protected $reason;
    
    /**
     * @internal
     */
    function __construct(\CharlotteDunois\Yasmin\Client $client, \CharlotteDunois\Yasmin\Models\Guild $guild, \CharlotteDunois\Yasmin\Models\User $user, ?string $reason) {
        parent::__construct($client);
        
        $this->guild = $guild;
        $this->user = $user;
        $this->reason = $reason;
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
     * Unbans the user.
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function unban(string $reason = '') {
        return $this->guild->unban($this->user, $reason);
    }
}
