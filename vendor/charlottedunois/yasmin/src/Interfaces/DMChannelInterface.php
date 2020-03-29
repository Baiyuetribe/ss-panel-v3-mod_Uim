<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin\Interfaces;

/**
 * Something all direct message channels implement.
 */
interface DMChannelInterface extends ChannelInterface, TextChannelInterface {
    /**
     * Determines whether a given user is a recipient of this channel.
     * @param \CharlotteDunois\Yasmin\Models\User|string  $user  The User instance or user ID.
     * @return bool
     * @throws \InvalidArgumentException
     */
    function isRecipient($user);
}
