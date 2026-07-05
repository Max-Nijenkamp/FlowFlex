<?php

declare(strict_types=1);

namespace App\Exceptions\Hr;

use RuntimeException;

class LeaveOverlapException extends RuntimeException
{
    public static function make(): self
    {
        return new self('This request overlaps an existing approved or pending leave.');
    }
}
