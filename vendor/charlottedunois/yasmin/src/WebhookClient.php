<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

namespace CharlotteDunois\Yasmin;

/**
 * The webhook client.
 *
 * @property string                                    $id         The webhook ID.
 * @property string|null                               $name       The webhook default name, or null.
 * @property string|null                               $avatar     The webhook default avatar, or null.
 * @property string|null                               $channelID  The channel the webhook belongs to.
 * @property string|null                               $guildID    The guild the webhook belongs to, or null.
 * @property \CharlotteDunois\Yasmin\Models\User|null  $owner      The owner of the webhook, or null.
 * @property string                                    $token      The webhook token.
 */
class WebhookClient extends \CharlotteDunois\Yasmin\Models\Webhook {
    /**
     * Constructor.
     * @param string                               $id       The webhook ID.
     * @param string                               $token    The webhook token.
     * @param array                                $options  Any Client Options.
     * @param \React\EventLoop\LoopInterface|null  $loop     The ReactPHP Event Loop.
     */
    function __construct(string $id, string $token, array $options = array(), ?\React\EventLoop\LoopInterface $loop = null) {
        $options['internal.ws.disable'] = true;
        
        $client = new \CharlotteDunois\Yasmin\Client($options, $loop);
        parent::__construct($client, array(
            'id' => $id,
            'token' => $token
        ));
    }
}
