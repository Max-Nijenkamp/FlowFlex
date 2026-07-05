<?php

declare(strict_types=1);

namespace App\Data\Crm;

use Spatie\LaravelData\Data;

class LogActivityData extends Data
{
    public function __construct(
        public string $type,
        public string $subject,
        public ?string $description = null,
        public ?string $contactId = null,
        public ?string $dealId = null,
        public ?string $accountId = null,
        public ?string $activityDate = null,
        public ?int $durationMinutes = null,
        public ?string $dueAt = null,
        public ?string $ownerId = null,
    ) {}
}
