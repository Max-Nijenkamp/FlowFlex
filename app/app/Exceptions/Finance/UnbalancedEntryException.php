<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use RuntimeException;

class UnbalancedEntryException extends RuntimeException
{
    public static function make(): self
    {
        return new self('Journal entry is unbalanced — total debits must equal total credits.');
    }
}
