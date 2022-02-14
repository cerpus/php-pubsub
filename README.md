# Cerpus\PubSub

Simple publish-subscribe for RabbitMQ and PHP.

## Requirements

* PHP 8.0 or 8.1
* The extensions required by
  [php-amqplib](https://packagist.org/packages/php-amqplib/php-amqplib)

## Installation

~~~sh
composer require cerpus/pubsub
~~~

## Usage

~~~php
use Cerpus\PubSub\Connection\ConnectionFactory;
use Cerpus\PubSub\PubSub;

$connectionFactory = new ConnectionFactory('localhost', 5672, 'guest', 'guest', '/');
$pubSub = new PubSub($connectionFactory->connect());

// publish your own messages
$pubSub->publish('some_other_topic', 'some data');

// listen for incoming messages
$pubSub->subscribe('subscriber_name', 'some_topic', function (string $data) {
    // do something with the data
    echo "$data\n";
});
$pubSub->listen();
~~~

## Future scope

* Support ext-amqp, queue-interop
* Handle more exceptions from underlying libraries
* Support configuring flags like auto-delete, etc.

## License

This package is released under the MIT license. See the `LICENSE` file for more
information.
