<?php

declare(strict_types=1);

namespace App\Exceptions\Core;

use RuntimeException;

class CannotDeactivateCoreModuleException extends RuntimeException
{
    public function __construct(string $moduleKey)
    {
        parent::__construct("Module [{$moduleKey}] is a free core module and cannot be deactivated.");
    }
}
