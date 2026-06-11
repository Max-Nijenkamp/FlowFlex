<?php

declare(strict_types=1);

namespace App\Contracts\CRM;

use App\Models\CRM\Contact;

interface ContactServiceInterface
{
    /** Idempotent by email — the listener entry point. @param array<string, mixed> $attributes */
    public function findOrCreateByEmail(string $email, array $attributes = []): Contact;

    public function moveLifecycleStage(string $contactId, string $stage): Contact;
}
