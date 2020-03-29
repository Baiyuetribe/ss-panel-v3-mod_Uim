# Yasmin [![Build Status](https://scrutinizer-ci.com/g/CharlotteDunois/Yasmin/badges/build.png?b=master)](https://scrutinizer-ci.com/g/CharlotteDunois/Yasmin/build-status/master)

Yasmin is a Discord API library for PHP. Yasmin connects to the Gateway and interacts with the REST API.

This library is **only** for PHP 7.1 (and later) and use in CLI. Only bot accounts are supported by Yasmin.

# Before you start
Before you start using this Library, you **need** to know how PHP works, you need to know the language and you need to know how Event Loops and Promises work. This is a fundamental requirement before you start. Without this knowledge, you will only suffer.

See https://github.com/elazar/asynchronous-php for resources.

# Getting Started
Getting started with Yasmin is pretty straight forward. All you need to do is to use [composer](https://packagist.org/packages/charlottedunois/yasmin) to install Yasmin and its dependencies. After that, you can include composer's autoloader into your file and start interacting with Discord and Yasmin!

```
composer require charlottedunois/yasmin
```

<br>

It is important to listen to `error` events. If you don't attach an `error` listener, the event emitter will throw an exception.

Make sure you also have a rejection handler for all promises, as unhandled promise rejections get swallowed and you will never know what happened to them.

**Important Information**: All properties on class instances, which are implemented using a magic method (which means pretty much all properties), are **throwing** if the property doesn't exist.

There is a WIP Gitbook with a few protips in it, feel free to read it: https://charlottedunois.gitbooks.io/yasmin-guide/content/

# Example
This is a fairly trivial example of using Yasmin. You should put all your listener code into try-catch blocks and handle exceptions accordingly.

```php
// Include composer autoloader

$loop = \React\EventLoop\Factory::create();
$client = new \CharlotteDunois\Yasmin\Client(array(), $loop);

$client->on('error', function ($error) {
    echo $error.PHP_EOL;
});

$client->on('ready', function () use ($client) {
    echo 'Logged in as '.$client->user->tag.' created on '.$client->user->createdAt->format('d.m.Y H:i:s').PHP_EOL;
});

$client->on('message', function ($message) {
    echo 'Received Message from '.$message->author->tag.' in '.($message->channel instanceof \CharlotteDunois\Yasmin\Interfaces\DMChannelInterface ? 'DM' : 'channel #'.$message->channel->name ).' with '.$message->attachments->count().' attachment(s) and '.\count($message->embeds).' embed(s)'.PHP_EOL;
});

$client->login('YOUR_TOKEN')->done();
$loop->run();
```

# Voice Support
There is no voice support.

# Documentation
https://yasmin.neko.run

# Windows and SSL
Unfortunately PHP on Windows does not have access to the Windows Certificate Store. This is an issue because TLS gets used and as such certificate verification gets applied (turning this off is **not** an option).

You will notice this issue by your script exiting immediately after one loop turn without any errors. Unfortunately there is for some reason no error or exception.

As such users of this library need to download a [Certificate Authority extract](https://curl.haxx.se/docs/caextract.html) from the cURL website.<br>
The path to the caextract must be set in the [`php.ini`](https://secure.php.net/manual/en/openssl.configuration.php) for `openssl.cafile`.
