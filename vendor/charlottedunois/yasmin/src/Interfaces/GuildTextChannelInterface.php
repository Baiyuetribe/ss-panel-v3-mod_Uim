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
 * Something all guild text channels implement.
 */
interface GuildTextChannelInterface extends GuildChannelInterface, TextChannelInterface {
    /**
     * Deletes multiple messages at once. Resolves with $this.
     * @param \CharlotteDunois\Collect\Collection|array|int  $messages
     * @param string                                         $reason
     * @param bool                                           $filterOldMessages
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function bulkDelete($messages, string $reason = '', bool $filterOldMessages = false);
    
    /**
     * Creates an invite. Resolves with an instance of Invite.
     *
     * Options are as following (all are optional).
     *
     * ```
     * array(
     *    'maxAge' => int,
     *    'maxUses' => int, (0 = unlimited)
     *    'temporary' => bool,
     *    'unique' => bool
     * )
     * ```
     *
     * @param array $options
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function createInvite(array $options = array());
    
    /**
     * Fetches all invites of this channel. Resolves with a Collection of Invite instances, mapped by their code.
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Invite
     */
    function fetchInvites();
    
    /**
     * Sets the slowmode in seconds for this channel.
     * @param int     $slowmode
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     */
    function setSlowmode(int $slowmode, string $reason = '');
    
    /**
     * Sets the topic of the channel. Resolves with $this.
     * @param string  $topic
     * @param string  $reason
     * @return \React\Promise\ExtendedPromiseInterface
     * @throws \InvalidArgumentException
     */
    function setTopic(string $topic, string $reason = '');
}
