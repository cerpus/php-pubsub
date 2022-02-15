<?php

declare(strict_types=1);

namespace Cerpus\PubSub;

use Cerpus\PubSub\Connection\ConnectionInterface;
use Closure;

class PubSub
{
    public function __construct(private ConnectionInterface $connection)
    {
    }

    public function publish(string $topic, string $data): void
    {
        $this->connection->declareTopic($topic);
        $this->connection->publish($topic, $data);
    }

    /**
     * @param Closure(string):void $handler
     */
    public function subscribe(
        string $name,
        string $topic,
        Closure $handler,
    ): void {
        $this->connection->declareTopic($topic);
        $this->connection->subscribe($name, $topic, $handler);
    }

    public function listen(): void
    {
        $this->connection->listen();
    }

    public function close(): void
    {
        $this->connection->close();
    }
}
