<?php

declare(strict_types=1);

namespace App\Exceptions\Hr;

use RuntimeException;

class ManagerCycleException extends RuntimeException
{
    public static function make(): self
    {
        return new self('This manager assignment creates a reporting cycle.');
    }
}
