<?php

declare(strict_types=1);

namespace App\Data\Crm;

use Spatie\LaravelData\Data;

class CreateDealData extends Data
{
    public function __construct(
        public string $name,
        public string $stageId,
        public int $valueCents = 0,
        public string $currency = 'EUR',
        public ?string $accountId = null,
        public ?string $contactId = null,
        public ?string $ownerId = null,
        public ?string $expectedCloseDate = null,
    ) {}
}
