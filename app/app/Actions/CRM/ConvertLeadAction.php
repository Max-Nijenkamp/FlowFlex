<?php

declare(strict_types=1);

namespace App\Actions\CRM;

use App\Models\CRM\Contact;
use App\Models\CRM\Deal;
use App\Models\CRM\Lead;
use App\Models\CRM\Pipeline;
use App\Models\CRM\PipelineStage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Turn a qualified lead into a pipeline deal. Creates a matching contact when
 * one isn't supplied, drops the deal into the default pipeline's first stage,
 * and stamps the lead converted (idempotent — a converted lead won't convert
 * again).
 */
class ConvertLeadAction
{
    use AsAction;

    public function handle(Lead $lead, ?string $contactId = null): Deal
    {
        if ($lead->isConverted()) {
            throw ValidationException::withMessages(['lead' => 'This lead is already converted.']);
        }

        $pipeline = Pipeline::query()->orderByDesc('is_default')->orderBy('order')->first();

        if (! $pipeline instanceof Pipeline) {
            throw ValidationException::withMessages(['pipeline' => 'No pipeline exists yet — create one first.']);
        }

        $stage = PipelineStage::query()
            ->where('pipeline_id', $pipeline->id)
            ->orderBy('order')
            ->first();

        if (! $stage instanceof PipelineStage) {
            throw ValidationException::withMessages(['pipeline' => 'The pipeline has no stages yet.']);
        }

        return DB::transaction(function () use ($lead, $contactId, $stage): Deal {
            $contactId ??= $this->resolveContact($lead)?->id;

            $deal = Deal::create([
                'name' => $lead->company_name ?: $lead->name,
                'contact_id' => $contactId,
                'owner_id' => $lead->owner_id ?? Auth::guard('web')->id(),
                'stage_id' => $stage->id,
                'value_cents' => $lead->estimated_value_cents,
                'currency' => 'EUR',
                'probability' => $stage->probability_default,
                'status' => 'open',
                'stage_entered_at' => now(),
            ]);

            $lead->forceFill([
                'status' => 'converted',
                'converted_deal_id' => $deal->id,
                'converted_at' => now(),
            ])->save();

            return $deal;
        });
    }

    private function resolveContact(Lead $lead): ?Contact
    {
        if ($lead->email === null) {
            return null;
        }

        $existing = Contact::query()->where('email', $lead->email)->first();

        if ($existing instanceof Contact) {
            return $existing;
        }

        [$first, $last] = array_pad(explode(' ', trim($lead->name), 2), 2, '');

        return Contact::create([
            'first_name' => $first ?: $lead->name,
            'last_name' => $last,
            'email' => $lead->email,
        ]);
    }
}
