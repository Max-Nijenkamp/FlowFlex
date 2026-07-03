<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MissingCompanyContextException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'No company context set. Tenant queries fail closed — set CompanyContext before touching tenant models.'
        );
    }
}
