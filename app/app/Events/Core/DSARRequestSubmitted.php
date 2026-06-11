<?php

declare(strict_types=1);

namespace App\Events\Core;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Events\Dispatchable;

/** Cross-domain event — payload per architecture/event-bus. */
class DSARRequestSubmitted
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $dsar_request_id,
        public readonly string $request_type,
        public readonly string $subject_email,
        public readonly CarbonImmutable $due_at,
    ) {}
}
