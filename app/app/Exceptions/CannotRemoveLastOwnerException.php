<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class CannotRemoveLastOwnerException extends RuntimeException
{
    public static function make(): self
    {
        return new self('A company must keep exactly one owner — transfer ownership instead of demoting the last one.');
    }
}
