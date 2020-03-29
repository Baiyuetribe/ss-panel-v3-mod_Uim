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
 * when the bot is ready.
 */

require_once(__DIR__.'/vendor/autoload.php');

$loop = \React\EventLoop\Factory::create();
$client = new \CharlotteDunois\Yasmin\Client(array(), $loop);

$client->once('ready', function () use ($client) {
    try {
        $channel = $client->channels->get('CHANNEL_ID');
        /*
          or (not recommended if the bot is in more than 1 guild):
            $channel = $client->channels->first(function ($channel) {
                return ($channel->name === 'general');
            });
        */
        
        // Making sure the channel exists
        if($channel) {
            // Send the message
            
            // We do not need another promise here, so
            // we call done, because we want to consume the promise
            $channel->send('Hello, I am a Discord Bot written in PHP using Yasmin.')
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
