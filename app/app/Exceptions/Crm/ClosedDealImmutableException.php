<?php

declare(strict_types=1);

namespace App\Exceptions\Crm;

use RuntimeException;

class ClosedDealImmutableException extends RuntimeException
{
    public static function make(): self
    {
        return new self('This deal is closed — reopen is not supported; duplicate it to start a new cycle.');
    }
}
