<?php

declare(strict_types=1);

namespace App\Contracts\Crm;

use App\Data\Crm\CreateContactData;
use App\Models\Crm\Contact;

interface ContactServiceInterface
{
    public function create(CreateContactData $data): Contact;

    /** @param  array<string, mixed>  $attributes */
    public function findOrCreateByEmail(string $email, array $attributes = []): Contact;

    public function moveLifecycleStage(string $contactId, string $stage): Contact;

    public function merge(string $keepId, string $mergeId): Contact;

    public function linkAccount(string $contactId, string $accountId, ?string $title = null, bool $isPrimary = false): void;
}
