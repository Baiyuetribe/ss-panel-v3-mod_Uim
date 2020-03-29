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
 * This interface defines required methods and their arguments for managing route ratelimits using various systems.<br>
 * The ratelimit bucket queue is always managed in memory (as in belongs to that process), however the ratelimits are distributed to the used system.
 *
 * Included are two ratelimit bucket systems:<br>
 *  * In memory ratelimit bucket, using arrays - Class: `\CharlotteDunois\Yasmin\HTTP\RatelimitBucket` (default)<br>
 *  * Redis ratelimit bucket, using Athena to interface with Redis - Class: `\CharlotteDunois\Yasmin\HTTP\AthenaRatelimitBucket`
 *
 * To use a different one than the default, you have to pass the full qualified class name to the client constructor as client option `http.ratelimitbucket.name`.
 *
 * The Redis ratelimit bucket system uses Athena, an asynchronous redis cache for PHP. The package is called `charlottedunois/athena` (which is suggested on composer).<br>
 * To be able to use the Redis ratelimit bucket, you need to pass an instance of `AthenaCache` as client option `http.ratelimitbucket.athena` to the client.
 */
interface RatelimitBucketInterface {
    /**
     * Initializes the bucket.
     * @param \CharlotteDunois\Yasmin\HTTP\APIManager  $api
     * @param string                                   $endpoint
     * @throws \RuntimeException
     */
    function __construct(\CharlotteDunois\Yasmin\HTTP\APIManager $api, string $endpoint);
    
    /**
     * Destroys the bucket.
     */
    function __destruct();
    
    /**
     * Whether we are busy.
     * @return bool
     */
    function isBusy(): bool;
    
    /**
     * Sets the busy flag (marking as running).
     * @param bool  $busy
     * @return void
     */
    function setBusy(bool $busy): void;
    
    /**
     * Sets the ratelimits from the response.
     * @param int|null    $limit
     * @param int|null    $remaining
     * @param float|null  $resetTime  Reset time in seconds with milliseconds.
     * @return \React\Promise\ExtendedPromiseInterface|void
     */
    function handleRatelimit(?int $limit, ?int $remaining, ?float $resetTime);
    
    /**
     * Returns the endpoint this bucket is for.
     * @return string
     */
    function getEndpoint(): string;
    
    /**
     * Returns the size of the queue.
     * @return int
     */
    function size(): int;
    
    /**
     * Pushes a new request into the queue.
     * @param \CharlotteDunois\Yasmin\HTTP\APIRequest $request
     * @return $this
     */
    function push(\CharlotteDunois\Yasmin\HTTP\APIRequest $request);
    
    /**
     * Unshifts a new request into the queue. Modifies remaining ratelimit.
     * @param \CharlotteDunois\Yasmin\HTTP\APIRequest $request
     * @return $this
     */
    function unshift(\CharlotteDunois\Yasmin\HTTP\APIRequest $request);
    
    /**
     * Retrieves ratelimit meta data.
     *
     * The resolved value must be:
     * ```
     * array(
     *     'limited' => bool,
     *     'resetTime' => float|null
     * )
     * ```
     *
     * @return \React\Promise\ExtendedPromiseInterface|array
     */
    function getMeta();
    
    /**
     * Returns the first queue item or false. Modifies remaining ratelimit.
     * @return \CharlotteDunois\Yasmin\HTTP\APIRequest|false
     */
    function shift();
    
    /**
     * Unsets all queue items.
     * @return void
     */
    function clear(): void;
}
