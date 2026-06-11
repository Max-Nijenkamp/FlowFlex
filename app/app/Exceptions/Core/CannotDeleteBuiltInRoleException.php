<?php

declare(strict_types=1);

namespace App\Exceptions\Core;

use RuntimeException;

class CannotDeleteBuiltInRoleException extends RuntimeException
{
    public function __construct(string $role)
    {
        parent::__construct("Built-in role [{$role}] cannot be deleted.");
    }
}
