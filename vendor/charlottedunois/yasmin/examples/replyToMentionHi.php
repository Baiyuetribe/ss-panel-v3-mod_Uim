<?php
/**
 * Yasmin
 * Copyright 2018 Charlotte Dunois, All Rights Reserved
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
*/

/*
 * This example will demonstrate how to listen on the message event and reply to the message,
 * if the message mentions the bot and says hi.
 */

require_once(__DIR__.'/vendor/autoload.php');

$loop = \React\EventLoop\Factory::create();
$client = new \CharlotteDunois\Yasmin\Client(array(
    'ws.disabledEvents' => array(
        /* We disable the TYPING_START event to save CPU cycles, we don't need it here in this example. */
        'TYPING_START'
    )
), $loop);

// Precompute mention format
$mentions = array();

$client->once('ready', function () use ($client, &$mentions) {
    $format1 = '<@'.$client->user->id.'>';
    $format2 = '<@!'.$client->user->id.'>';
    
    $mentions = array(
        $format1,
        strlen($format1),
        $format2,
        strlen($format2)
    );
});

$client->on('message', function ($message) use ($client, &$mentions) {
    try {
        // Get the start of message content to compare with our mention formats
        // We use the longest mention format
        // We also trim it from any trailing whitespaces
        $start = \trim(\substr($message->content, $mentions[3]));
        
        // Now we compare it, we only want the bot to respond
        // when the mention is at the start of the content
        if($start === $mentions[0] || $start === $mentions[1]) {
            // We do not need another promise here, so
            // we call done, because we want to consume the promise
            $message->reply('Hi!')->done(null, function ($error) {
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
