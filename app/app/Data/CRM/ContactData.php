<?php

declare(strict_types=1);

namespace App\Data\CRM;

use App\Models\CRM\Contact;
use Spatie\LaravelData\Data;

class ContactData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $full_name,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $job_title,
        public readonly ?string $account_id,
        public readonly string $lifecycle_stage,
        public readonly ?string $source,
    ) {}

    public static function fromModel(Contact $contact): self
    {
        return new self(
            id: $contact->id,
            first_name: $contact->first_name,
            last_name: $contact->last_name,
            full_name: $contact->full_name,
            email: $contact->email,
            phone: $contact->phone,
            job_title: $contact->job_title,
            account_id: $contact->account_id,
            lifecycle_stage: $contact->lifecycle_stage,
            source: $contact->source,
        );
    }
}
