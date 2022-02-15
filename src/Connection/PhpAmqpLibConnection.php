<?php

declare(strict_types=1);

namespace Cerpus\PubSub\Connection;

use Cerpus\PubSub\Exception\DuplicateSubscriptionException;
use Cerpus\PubSub\Exception\RuntimeException;
use Closure;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Connection\AbstractConnection;
use PhpAmqpLib\Exception\AMQPExceptionInterface;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

final class PhpAmqpLibConnection implements ConnectionInterface
{
    /** @var array<string> */
    private array $declaredTopics = [];

    /** @var array<string> */
    private array $declaredSubscriptions = [];

    private AbstractChannel $channel;

    public function __construct(
        private AbstractConnection $connection,
        private LoggerInterface|null $logger = null,
    ) {
        $this->channel = $this->connection->channel();
    }

    public function declareTopic(string $topic): void
    {
        if (isset($this->declaredTopics[$topic])) {
            return;
        }

        try {
            $this->channel->exchange_declare(
                $topic,
                AMQPExchangeType::FANOUT,
                false,
                true,
                false,
            );
        } catch (AMQPExceptionInterface $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $this->declaredTopics[$topic] = true;
    }

    public function subscribe(
        string $name,
        string $topic,
        Closure $handler
    ): void {
        if (isset($this->declaredSubscriptions[$name])) {
            throw new DuplicateSubscriptionException();
        }

        $callback = function (AMQPMessage $msg) use ($handler): void {
            $handler($msg->body);
            $msg->ack();
        };

        try {
            $this->channel->queue_declare($name, false, true, false, false);
            $this->channel->queue_bind($name, $topic);
            $this->channel->basic_consume(
                $name,
                '',
                false,
                false,
                false,
                false,
                $callback,
            );
        } catch (AMQPExceptionInterface $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function publish(string $topic, string $data): void
    {
        $this->declareTopic($topic);

        try {
            $this->channel->basic_publish(new AMQPMessage($data), $topic);
        } catch (AMQPExceptionInterface $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function listen(): void
    {
        try {
            while ($this->channel->is_open()) {
                $this->channel->wait();
            }
        } catch (AMQPExceptionInterface $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function close(): void
    {
        try {
            if ($this->channel->is_open()) {
                $this->channel->close();
            }

            if ($this->connection->isConnected()) {
                $this->connection->close();
            }
        } catch (AMQPExceptionInterface $e) {
            $this->logger?->warning('Failed to close connection', [
                'exception' => $e,
            ]);
        }
    }
}
