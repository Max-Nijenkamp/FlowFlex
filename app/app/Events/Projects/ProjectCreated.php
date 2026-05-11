<?php

declare(strict_types=1);

namespace App\Events\Projects;

use App\Models\Company;
use App\Models\Projects\Project;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Company $company,
        public readonly Project $project,
    ) {}
}
