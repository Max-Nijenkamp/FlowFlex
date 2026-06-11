<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Exceptions\CRM\SelfReferralException;
use App\Models\CRM\Contact;
use App\Models\CRM\Referral;
use App\Models\CRM\ReferralProgram;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReferralService
{
    /** Generate-or-return the referrer's code for a program. */
    public function codeFor(string $contactId, string $programId): string
    {
        $existing = Referral::query()
            ->where('program_id', $programId)
            ->where('referrer_contact_id', $contactId)
            ->whereNull('referee_contact_id')
            ->whereNull('converted_at')
            ->where('referee_email', '')
            ->first();

        if ($existing !== null) {
            return $existing->referral_code;
        }

        $referral = Referral::create([
            'program_id' => $programId,
            'referrer_contact_id' => $contactId,
            'referral_code' => strtoupper(Str::random(8)),
            'referee_email' => '', // placeholder row holding the code
        ]);

        return $referral->referral_code;
    }

    /** Registers a referee against a code — fraud checks + program window. */
    public function register(string $code, string $refereeEmail): Referral
    {
        $codeRow = Referral::query()->where('referral_code', $code)->firstOrFail();
        $program = ReferralProgram::query()->findOrFail($codeRow->program_id);

        if (! $program->is_active
            || ($program->starts_at !== null && $program->starts_at->isFuture())
            || ($program->ends_at !== null && $program->ends_at->isPast())) {
            throw ValidationException::withMessages(['program' => 'Referral program is not active.']);
        }

        $referrer = Contact::query()->findOrFail($codeRow->referrer_contact_id);

        // Self-referral: email match or existing contact = referrer.
        if (strcasecmp($referrer->email, $refereeEmail) === 0) {
            throw new SelfReferralException;
        }

        $duplicate = Referral::query()
            ->where('program_id', $program->id)
            ->where('referee_email', $refereeEmail)
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages(['referee' => 'This person was already referred.']);
        }

        return Referral::create([
            'program_id' => $program->id,
            'referrer_contact_id' => $referrer->id,
            'referral_code' => $code.'-'.strtoupper(Str::random(4)),
            'referee_email' => $refereeEmail,
        ])->refresh();
    }

    /** Conversion: qualifies + notifies fulfilment owner (v1 manual fulfilment). */
    public function qualify(string $referralId): Referral
    {
        $referral = Referral::query()->findOrFail($referralId);
        $referral->update(['status' => 'qualified', 'converted_at' => now()]);

        return $referral->refresh();
    }

    public function markRewarded(string $referralId): Referral
    {
        $referral = Referral::query()->findOrFail($referralId);
        $referral->update(['status' => 'rewarded', 'rewarded_at' => now()]);

        return $referral->refresh();
    }

    /** Qualified + rewarded only *(assumed)*. */
    public function leaderboard(string $programId): Collection
    {
        return Referral::query()
            ->where('program_id', $programId)
            ->whereIn('status', ['qualified', 'rewarded'])
            ->selectRaw('referrer_contact_id, COUNT(*) as referral_count')
            ->groupBy('referrer_contact_id')
            ->orderByDesc('referral_count')
            ->get();
    }
}
