<?php

declare(strict_types=1);

namespace App\Events\Projects;

use App\Models\Company;
use App\Models\Projects\TimeEntry;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimeEntryApproved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Company $company,
        public readonly TimeEntry $timeEntry,
    ) {}
}
