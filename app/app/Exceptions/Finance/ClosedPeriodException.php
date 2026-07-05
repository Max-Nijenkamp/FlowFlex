<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use RuntimeException;

class ClosedPeriodException extends RuntimeException
{
    public static function make(): self
    {
        return new self('This fiscal period is closed — reopen it before posting.');
    }
}
