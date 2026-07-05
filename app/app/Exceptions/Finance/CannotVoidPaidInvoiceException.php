<?php

declare(strict_types=1);

namespace App\Exceptions\Finance;

use RuntimeException;

class CannotVoidPaidInvoiceException extends RuntimeException
{
    public static function make(): self
    {
        return new self('A paid invoice cannot be voided — issue a credit note instead.');
    }
}
