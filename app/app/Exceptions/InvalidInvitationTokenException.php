<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class InvalidInvitationTokenException extends RuntimeException
{
    public static function make(): self
    {
        return new self('This invitation link is no longer valid — ask your workspace admin for a fresh one.');
    }
}
