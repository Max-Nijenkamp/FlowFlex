<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class CannotDeleteBuiltInRoleException extends RuntimeException
{
    public static function make(string $role): self
    {
        return new self("The built-in role [{$role}] cannot be deleted.");
    }
}
