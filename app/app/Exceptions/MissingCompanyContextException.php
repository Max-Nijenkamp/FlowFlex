<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MissingCompanyContextException extends RuntimeException
{
    public function __construct(string $message = 'No company context set. SetCompanyContext / WithCompanyContext middleware must run first.')
    {
        parent::__construct($message);
    }
}
