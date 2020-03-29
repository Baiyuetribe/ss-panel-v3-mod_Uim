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
 * Something all text channels implement.
 *
 * @method \CharlotteDunois\Yasmin\Interfaces\MessageStorageInterface  getMessages()       Gets the storage with all cached messages.
 * @method string                                                      getLastMessageID()  Gets the ID of the last sent message in this channel.
 */
interface TextChannelInterface extends ChannelInterface {
    /**
     * Collects messages during a specific duration (and max. amount). Resolves with a Collection of Message instances, mapped by their IDs.
     *
     * Options are as following (all are optional):
     *
     * ```
     * array(
     *   'max' => int, (max. messages to collect)
     *   'time' => int, (duration, in seconds, default 30)
     *   'errors' => array, (optional, which failed "conditions" (max not reached in time ("time")) lead to a rejected promise, defaults to [])
     * )
     * ```
     *
     * @param callable  $filter
     * @param array     $options
     * @return \React\Promise\ExtendedPromiseInterface  This promise is cancellable.
     * @throws \RangeException          The exception the promise gets rejected with, if waiting times out.
     * @throws \OutOfBoundsException    The exception the promise gets rejected with, if the promise gets cancelled.
     * @see \CharlotteDunois\Yasmin\Models\Message
     * @see \CharlotteDunois\Yasmin\Utils\Collector
     */
    function collectMessages(callable $filter, array $options = array());
    
    /**
     * Fetches a specific message using the ID. Resolves with an instance of Message.
     * @param string  $id
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Message
     */
    function fetchMessage(string $id);
    
    /**
     * Fetches messages of this channel. Resolves with a Collection of Message instances, mapped by their ID.
     *
     * Options are as following:
     *
     * ```
     * array(
     *   'after' => string, (message ID)
     *   'around' => string, (message ID)
     *   'before' => string, (message ID)
     *   'limit' => int, (1-100, defaults to 50)
     * )
     * ```
     *
     * @param array  $options
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Message
     */
    function fetchMessages(array $options = array());
    
    /**
     * Sends a message to a channel. Resolves with an instance of Message, or a Collection of Message instances, mapped by their ID.
     *
     * Options are as following (all are optional):
     *
     * ```
     * array(
     *    'embed' => array|\CharlotteDunois\Yasmin\Models\MessageEmbed, (an (embed) array/object or an instance of MessageEmbed)
     *    'files' => array, (an array of `[ 'name' => string, 'data' => string || 'path' => string ]` or just plain file contents, file paths or URLs)
     *    'nonce' => string, (a snowflake used for optimistic sending)
     *    'disableEveryone' => bool, (whether @everyone and @here should be replaced with plaintext, defaults to client option disableEveryone)
     *    'tts' => bool,
     *    'split' => bool|array, (*)
     * )
     *
     *   * array(
     *   *   'before' => string, (The string to insert before the split)
     *   *   'after' => string, (The string to insert after the split)
     *   *   'char' => string, (The string to split on)
     *   *   'maxLength' => int, (The max. length of each message)
     *   * )
     * ```
     *
     * @param string  $content
     * @param array   $options
     * @return \React\Promise\ExtendedPromiseInterface
     * @see \CharlotteDunois\Yasmin\Models\Message
     */
    function send(string $content, array $options = array());
    
    /**
     * Starts sending the typing indicator in this channel. Counts up a triggered typing counter.
     * @return void
     */
    function startTyping();
    
    /**
     * Stops sending the typing indicator in this channel. Counts down a triggered typing counter.
     * @param bool  $force
     * @return void
     */
    function stopTyping(bool $force = false);
    
    /**
     * Returns the amount of user typing in this channel.
     * @return int
     */
    function typingCount();
    
    /**
     * Determines whether the given user is typing in this channel or not.
     * @param \CharlotteDunois\Yasmin\Models\User  $user
     * @return bool
     */
    function isTyping(\CharlotteDunois\Yasmin\Models\User $user);
    
    /**
     * @param array  $message
     * @return \CharlotteDunois\Yasmin\Models\Message
     * @internal
     */
    function _createMessage(array $message);
    
    /**
     * @param \CharlotteDunois\Yasmin\Models\User  $user
     * @param int|null                             $timestamp
     * @return bool
     * @internal
     */
    function _updateTyping(\CharlotteDunois\Yasmin\Models\User $user, ?int $timestamp = null);
}
