<?php

declare(strict_types=1);

namespace App\Data\Projects;

use Spatie\LaravelData\Data;

class LogTimeData extends Data
{
    public function __construct(
        public readonly string $user_id,
        public readonly string $date,
        public readonly float $hours,
        public readonly ?string $task_id = null,
        public readonly ?string $project_id = null,
        public readonly ?string $description = null,
        public readonly bool $is_billable = false,
        public readonly ?float $billing_rate = null,
    ) {}
}
