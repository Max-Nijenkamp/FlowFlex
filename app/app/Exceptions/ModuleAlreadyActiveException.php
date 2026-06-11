<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class ModuleAlreadyActiveException extends RuntimeException
{
    public function __construct(string $moduleKey)
    {
        parent::__construct("Module [{$moduleKey}] is already active for this company.");
    }
}
