<?php

declare(strict_types=1);

namespace App\Data\Projects;

use Spatie\LaravelData\Data;

class CreateTaskData extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $created_by,
        public readonly ?string $project_id = null,
        public readonly ?string $description = null,
        public readonly ?string $assignee_id = null,
        public readonly string $priority = 'medium',
        public readonly string $status = 'todo',
        public readonly ?string $due_date = null,
        public readonly ?string $start_date = null,
        public readonly ?float $estimate_hours = null,
        public readonly ?int $story_points = null,
        public readonly ?array $labels = null,
        public readonly ?string $parent_id = null,
    ) {}
}
