<?php

declare(strict_types=1);

namespace App\Events\Projects;

use App\Models\Company;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectMilestone;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectMilestoneReached
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Company $company,
        public readonly ProjectMilestone $milestone,
        public readonly Project $project,
    ) {}
}
