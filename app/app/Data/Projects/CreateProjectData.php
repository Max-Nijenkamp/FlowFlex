<?php

declare(strict_types=1);

namespace App\Data\Projects;

use Spatie\LaravelData\Data;

class CreateProjectData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $owner_id,
        public readonly ?string $description = null,
        public readonly ?string $start_date = null,
        public readonly ?string $due_date = null,
        public readonly ?float $budget = null,
        public readonly ?string $color = null,
        public readonly string $priority = 'medium',
        public readonly string $status = 'planning',
    ) {}
}
