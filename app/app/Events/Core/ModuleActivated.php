<?php

declare(strict_types=1);

namespace App\Events\Core;

use Illuminate\Foundation\Events\Dispatchable;

/** Cross-domain event — payload per architecture/event-bus. company_id always a scalar. */
class ModuleActivated
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $module_key,
        public readonly string $activated_by,
    ) {}
}
