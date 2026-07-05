<?php

declare(strict_types=1);

namespace App\Data\Crm;

use Spatie\LaravelData\Data;

class CreateContactData extends Data
{
    /** @param  array<string, mixed>  $customFields */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $jobTitle = null,
        public ?string $accountId = null,
        public string $lifecycleStage = 'lead',
        public ?string $source = null,
        public ?string $ownerId = null,
        public array $customFields = [],
    ) {}
}
