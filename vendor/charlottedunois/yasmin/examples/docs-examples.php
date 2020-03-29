<?php
/**
 * Yasmin
 * Copyright 2018 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

/*
 * This file is merely for the purpose of filling the docs with examples.
 */

// ClientUser::setAvatar
// In/After ready event
$client->user->setAvatar(__DIR__.'/resources/avatar.png');
$client->user->setAvatar('https://my.resources.online/resources/avatar.png');
$client->user->setAvatar(file_get_contents(__DIR__.'/resources/avatar.png'));

// ClientUser::setGame
// In/After ready event
$client->user->setGame('Yasmin');

// ClientUser::setStatus
// In/After ready event
$client->user->setStatus('online');

// ClientUser::setPresence
// In/After ready event
$client->user->setPresence(
    array(
        'status' => 'idle',
        'game' => array(
            'name' => 'Yasmin',
            'type' => 0
        )
    )
);

// ClientUser::setUsername
// In/After ready event
$client->user->setUsername('My Super New Username');
