<?php

declare(strict_types=1);

namespace Cerpus\PubSub\Connection;

use Closure;

interface ConnectionInterface
{
    public function publish(string $topic, string $data): void;

    public function subscribe(string $name, string $topic, Closure $handler): void;

    /**
     * Declare a topic. If the topic is already declared, nothing should happen.
     */
    public function declareTopic(string $topic): void;

    public function listen(): void;

    public function close(): void;
}
