<?php

declare(strict_types=1);

namespace Cerpus\PubSub\Tests;

use Cerpus\PubSub\Connection\ConnectionInterface;
use Cerpus\PubSub\PubSub;
use Closure;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PubSubTest extends TestCase
{
    private ConnectionInterface|MockObject $connection;

    private PubSub $pubSub;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(ConnectionInterface::class);
        $this->pubSub = new PubSub($this->connection);
    }

    public function testListen(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('listen');

        $this->pubSub->listen();
    }

    public function testPublish(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('declareTopic')
            ->with('topic');

        $this->connection
            ->expects($this->once())
            ->method('publish')
            ->with('topic', 'data');

        $this->pubSub->publish('topic', 'data');
    }

    public function testSubscribe(): void
    {
        $handler = fn() => 'cool';

        $this->connection
            ->expects($this->once())
            ->method('declareTopic')
            ->with('topic');

        $this->connection
            ->expects($this->once())
            ->method('subscribe')
            ->with(
                'subscriber name',
                'topic',
                $this->callback(fn(Closure $handler) => $handler() === 'cool'),
            );

        $this->pubSub->subscribe('subscriber name', 'topic', $handler);
    }

    public function testClose(): void
    {
        $this->connection
            ->expects($this->once())
            ->method('close');

        $this->pubSub->close();
    }
}
