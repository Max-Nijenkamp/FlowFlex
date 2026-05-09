<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class MissingCompanyContextException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Company context has not been set for this request.');
    }
}
