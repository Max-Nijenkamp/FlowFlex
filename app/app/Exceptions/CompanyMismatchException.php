<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class CompanyMismatchException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'Attempted to create a tenant record for a different company than the current context.'
        );
    }
}
