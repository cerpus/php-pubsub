<?php

declare(strict_types=1);

namespace Cerpus\PubSub\Exception;

use Exception;

class DuplicateSubscriptionException extends Exception implements ExceptionInterface
{
    public static function create(string $topic): self
    {
        return new self("Duplicate subscriber '$topic' was provided");
    }
}
