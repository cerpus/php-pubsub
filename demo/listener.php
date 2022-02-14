<?php

use Cerpus\PubSub\PubSub;

if (php_sapi_name() !== 'cli') {
    die('must be run from the command line');
}

function log_message(string $message, mixed ...$args) {
    $message = sprintf($message, ...$args);
    fwrite(STDOUT, $message);
    file_put_contents('mq.log', $message, flags: FILE_APPEND);
}

require __DIR__ . '/../vendor/autoload.php';

$connection = require __DIR__ . '/include/connection.php';

$pubSub = new PubSub($connection);
$pubSub->subscribe('add_topic', 'add_topic', function (string $topic) use ($pubSub) {
    log_message("Adding subscriber for topic %s\n", $topic);

    $pubSub->subscribe($topic . '_handler', $topic, function (string $data) use ($topic) {
        log_message("topic: %s\ndata: %s\n", $topic, $data);
    });
});
$pubSub->subscribe('close', 'close', function () use ($pubSub) {
    log_message("Stopping the listener\n");
    $pubSub->close();
});

echo "Listening...\n";
$pubSub->listen();

echo "No longer listening\n";
$pubSub->close();

