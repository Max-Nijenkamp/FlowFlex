<?php

declare(strict_types=1);

namespace App\Data\CRM;

use App\Models\CRM\Deal;
use Spatie\LaravelData\Data;

class DealData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $account_id,
        public readonly ?string $contact_id,
        public readonly string $owner_id,
        public readonly string $stage_id,
        public readonly int $value_cents,
        public readonly string $currency,
        public readonly float $probability,
        public readonly string $status,
        public readonly ?string $expected_close_date,
    ) {}

    public static function fromModel(Deal $deal): self
    {
        return new self(
            id: $deal->id,
            name: $deal->name,
            account_id: $deal->account_id,
            contact_id: $deal->contact_id,
            owner_id: $deal->owner_id,
            stage_id: $deal->stage_id,
            value_cents: $deal->value_cents,
            currency: $deal->currency,
            probability: $deal->probability,
            status: (string) $deal->status,
            expected_close_date: $deal->expected_close_date?->toDateString(),
        );
    }
}
