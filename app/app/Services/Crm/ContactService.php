<?php

declare(strict_types=1);

namespace App\Services\Crm;

use App\Contracts\Crm\ContactServiceInterface;
use App\Data\Crm\CreateContactData;
use App\Models\Crm\Activity;
use App\Models\Crm\Contact;
use App\Models\Crm\ContactAccount;
use App\Models\Crm\Deal;
use App\Models\Crm\DealContact;
use App\Models\User;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Owns crm_contacts / crm_accounts / crm_contact_accounts writes
 * (crm.contacts). Other domains enter through findOrCreateByEmail —
 * idempotent by (company, email), the platform-wide dedup guard.
 */
class ContactService implements ContactServiceInterface
{
    public function create(CreateContactData $data): Contact
    {
        $this->assertEmailAvailable($data->email);

        return Contact::query()->create([
            'company_id' => app(CompanyContext::class)->current()->id,
            'first_name' => $data->firstName,
            'last_name' => $data->lastName,
            'email' => $data->email,
            'phone' => $data->phone,
            'job_title' => $data->jobTitle,
            'account_id' => $data->accountId,
            'lifecycle_stage' => $data->lifecycleStage,
            'source' => $data->source,
            'owner_id' => $data->ownerId ?? Auth::id(),
            'custom_fields' => $data->customFields,
        ]);
    }

    public function findOrCreateByEmail(string $email, array $attributes = []): Contact
    {
        $existing = Contact::query()->where('email', $email)->first();

        if ($existing instanceof Contact) {
            return $existing;
        }

        return Contact::query()->create(array_merge([
            'company_id' => app(CompanyContext::class)->current()->id,
            'first_name' => $attributes['first_name'] ?? str($email)->before('@')->toString(),
            'last_name' => $attributes['last_name'] ?? '',
            'email' => $email,
            'lifecycle_stage' => 'lead',
            'source' => $attributes['source'] ?? 'form',
            'owner_id' => $attributes['owner_id'] ?? Auth::id() ?? app(CompanyContext::class)->current()->users()->first()?->id,
        ], $attributes));
    }

    public function moveLifecycleStage(string $contactId, string $stage): Contact
    {
        if (! in_array($stage, Contact::LIFECYCLE_STAGES, true)) {
            throw new InvalidArgumentException("Unknown lifecycle stage [{$stage}].");
        }

        /** @var Contact $contact */
        $contact = Contact::query()->findOrFail($contactId);
        $contact->update(['lifecycle_stage' => $stage]);

        return $contact;
    }

    /**
     * Duplicate resolution: reassign activities, deals and account links,
     * soft-delete the merged record. Pessimistic — both rows locked so a
     * concurrent merge/edit cannot lose reassignments.
     */
    public function merge(string $keepId, string $mergeId): Contact
    {
        if ($keepId === $mergeId) {
            throw new InvalidArgumentException('Cannot merge a contact into itself.');
        }

        return DB::transaction(function () use ($keepId, $mergeId): Contact {
            /** @var Contact $keep */
            $keep = Contact::query()->whereKey($keepId)->lockForUpdate()->firstOrFail();
            /** @var Contact $merge */
            $merge = Contact::query()->whereKey($mergeId)->lockForUpdate()->firstOrFail();

            Activity::query()->where('contact_id', $merge->id)->update(['contact_id' => $keep->id]);
            Deal::query()->where('contact_id', $merge->id)->update(['contact_id' => $keep->id]);
            DealContact::query()->where('contact_id', $merge->id)->update(['contact_id' => $keep->id]);

            ContactAccount::query()
                ->where('contact_id', $merge->id)
                ->get()
                ->each(function (ContactAccount $link) use ($keep): void {
                    $exists = ContactAccount::query()
                        ->where('contact_id', $keep->id)
                        ->where('account_id', $link->account_id)
                        ->exists();

                    $exists ? $link->delete() : $link->update(['contact_id' => $keep->id]);
                });

            // Free the merged record's unique email slot BEFORE backfilling
            // it onto the kept record, or the unique index rejects the copy.
            $backfill = [];
            foreach (['email', 'phone', 'job_title', 'account_id'] as $field) {
                if ($keep->{$field} === null && $merge->{$field} !== null) {
                    $backfill[$field] = $merge->{$field};
                }
            }

            $merge->update(['email' => null]);
            $merge->delete();

            if ($backfill !== []) {
                $keep->update($backfill);
            }

            $causer = Auth::user();
            app(AuditLogger::class)->log(
                'crm.contact-merged',
                $keep,
                $causer instanceof User ? $causer : null,
                ['merged_id' => $merge->id, 'kept_id' => $keep->id],
            );

            return $keep;
        });
    }

    public function linkAccount(string $contactId, string $accountId, ?string $title = null, bool $isPrimary = false): void
    {
        if ($isPrimary) {
            ContactAccount::query()->where('contact_id', $contactId)->update(['is_primary' => false]);
        }

        ContactAccount::query()->updateOrCreate(
            ['contact_id' => $contactId, 'account_id' => $accountId],
            [
                'company_id' => app(CompanyContext::class)->current()->id,
                'title' => $title,
                'is_primary' => $isPrimary,
            ],
        );
    }

    private function assertEmailAvailable(?string $email): void
    {
        if ($email === null || $email === '') {
            return;
        }

        if (Contact::query()->where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'A contact with this email already exists.',
            ]);
        }
    }
}
