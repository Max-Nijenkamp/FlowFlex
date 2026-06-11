<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Contracts\CRM\ContactServiceInterface;
use App\Models\CRM\Contact;
use Illuminate\Support\Facades\Auth;

class ContactService implements ContactServiceInterface
{
    public function findOrCreateByEmail(string $email, array $attributes = []): Contact
    {
        return Contact::query()->firstOrCreate(
            ['email' => $email],
            [
                'first_name' => $attributes['first_name'] ?? 'Unknown',
                'last_name' => $attributes['last_name'] ?? '',
                'source' => $attributes['source'] ?? 'manual',
                'owner_id' => $attributes['owner_id'] ?? Auth::guard('web')->id(),
                ...$attributes,
            ],
        );
    }

    public function moveLifecycleStage(string $contactId, string $stage): Contact
    {
        $contact = Contact::query()->findOrFail($contactId);
        $contact->update(['lifecycle_stage' => $stage]);

        return $contact->refresh();
    }
}
