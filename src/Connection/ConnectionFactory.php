<?php

declare(strict_types=1);

namespace Cerpus\PubSub\Connection;

use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConnectionFactory
{
    public function __construct(
        private string $host,
        private int $port,
        private string $username,
        private string $password,
        private string $vhost = '/',
        private bool $secure = false,
        private array $sslOptions = [],
    ) {
    }

    public function connect(): ConnectionInterface
    {
        if ($this->secure) {
            $connection = new AMQPSSLConnection(
                $this->host,
                $this->port,
                $this->username,
                $this->password,
                $this->vhost,
                $this->sslOptions,
            );
        } else {
            $connection = new AMQPStreamConnection(
                $this->host,
                $this->port,
                $this->username,
                $this->password,
                $this->vhost,
            );
        }

        return new PhpAmqpLibConnection($connection);
    }
}
