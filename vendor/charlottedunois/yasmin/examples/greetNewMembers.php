<?php
/**
 * Yasmin
 * Copyright 2018 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

/*
 * This example will demonstrate how you can send a message to a specific channel,
 * whenever a new member joins a guild.
 */

require_once(__DIR__.'/vendor/autoload.php');

$loop = \React\EventLoop\Factory::create();
$client = new \CharlotteDunois\Yasmin\Client(array(), $loop);

$client->on('guildMemberAdd', function ($member) {
    try {
        // Find the first channel matching the name member-log in the guild
        $channel = $member->guild->channels->first(function ($channel) {
            return ($channel->name === 'member-log');
        });
        
        // Making sure the channel exists
        if($channel) {
            // Send the message, welcoming & mentioning the member
            
            // We do not need another promise here, so
            // we call done, because we want to consume the promise
            $channel->send('Welcome to the guild '.$member->guild->name.', '.$member.'!')
                    ->done(null, function ($error) {
                        // We will just echo any errors for this example
                        echo $error.PHP_EOL;
                    });
        }
    } catch(\Exception $error) {
        // Handle exception
    }
});

$client->login('YOUR_TOKEN');
$loop->run();
