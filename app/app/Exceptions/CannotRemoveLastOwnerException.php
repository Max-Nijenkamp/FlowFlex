<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class CannotRemoveLastOwnerException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The last owner of a company cannot be demoted or removed.');
    }
}
