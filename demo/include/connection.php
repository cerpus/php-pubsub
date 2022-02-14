<?php

use Cerpus\PubSub\Connection\ConnectionFactory;

$connectionFactory = new ConnectionFactory(
    $_ENV['RABBITMQ_HOST'],
    (int) $_ENV['RABBITMQ_PORT'],
    $_ENV['RABBITMQ_USERNAME'],
    $_ENV['RABBITMQ_PASSWORD'],
);

return $connectionFactory->connect();
