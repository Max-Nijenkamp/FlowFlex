<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Contracts\CRM\SequenceServiceInterface;
use App\Exceptions\CRM\AlreadyEnrolledException;
use App\Models\CRM\Activity;
use App\Models\CRM\Contact;
use App\Models\CRM\Sequence;
use App\Models\CRM\SequenceEnrolment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class SequenceService implements SequenceServiceInterface
{
    /** One ACTIVE enrolment per (sequence, contact); re-enrol after completion allowed. */
    public function enrol(string $sequenceId, string $contactId, ?string $dealId = null): SequenceEnrolment
    {
        return DB::transaction(function () use ($sequenceId, $contactId, $dealId): SequenceEnrolment {
            $sequence = Sequence::query()->with('steps')->findOrFail($sequenceId);

            $active = SequenceEnrolment::query()
                ->where('sequence_id', $sequenceId)
                ->where('contact_id', $contactId)
                ->where('status', 'active')
                ->lockForUpdate()
                ->exists();

            if ($active) {
                throw new AlreadyEnrolledException;
            }

            // A/B variants: random split per email step with variants config.
            $variantMap = [];
            foreach ($sequence->steps as $step) {
                if ($step->type === 'email' && isset($step->config['variants'])) {
                    $variantMap[$step->order] = array_rand($step->config['variants']);
                }
            }

            return SequenceEnrolment::create([
                'sequence_id' => $sequenceId,
                'contact_id' => $contactId,
                'deal_id' => $dealId,
                'next_step_at' => now(),
                'variant_map' => $variantMap === [] ? null : $variantMap,
                'enrolled_at' => now(),
            ])->refresh();
        });
    }

    /**
     * Scheduled advancement: executes every due enrolment one step; idempotent
     * inside the window because next_step_at moves forward atomically.
     *
     * @return array{advanced: int, completed: int, failed: int}
     */
    public function advanceDue(): array
    {
        $result = ['advanced' => 0, 'completed' => 0, 'failed' => 0];

        $due = SequenceEnrolment::query()
            ->where('status', 'active')
            ->where('next_step_at', '<=', now())
            ->with('sequence.steps')
            ->get();

        foreach ($due as $enrolment) {
            try {
                DB::transaction(function () use ($enrolment, &$result): void {
                    $steps = $enrolment->sequence->steps;
                    $step = $steps->firstWhere('order', $enrolment->current_step + 1);

                    if ($step === null) {
                        $enrolment->update(['status' => 'completed']);
                        $result['completed']++;

                        return;
                    }

                    $ownerId = $this->ownerFor($enrolment);

                    if (in_array($step->type, ['call', 'task'], true)) {
                        Activity::create([
                            'company_id' => $enrolment->company_id,
                            'type' => $step->type === 'call' ? 'call' : 'task',
                            'subject' => $step->config['text'] ?? 'Sequence step',
                            'contact_id' => $enrolment->contact_id,
                            'deal_id' => $enrolment->deal_id,
                            'owner_id' => $ownerId,
                            'due_at' => now(),
                        ]);
                    }
                    // email steps: queue via crm.email when connected — v1 logs activity
                    if ($step->type === 'email') {
                        Activity::create([
                            'company_id' => $enrolment->company_id,
                            'type' => 'email',
                            'subject' => 'Sequence email: '.($step->config['subject'] ?? 'step '.$step->order),
                            'contact_id' => $enrolment->contact_id,
                            'deal_id' => $enrolment->deal_id,
                            'owner_id' => $ownerId,
                            'due_at' => now(),
                        ]);
                    }

                    $next = $steps->firstWhere('order', $enrolment->current_step + 2);

                    $enrolment->update([
                        'current_step' => $step->order,
                        'next_step_at' => now()->addDays($next->wait_days ?? 0),
                        'status' => $next === null ? 'completed' : 'active',
                    ]);

                    if ($next === null) {
                        $result['completed']++;
                    } else {
                        $result['advanced']++;
                    }
                });
            } catch (Throwable $e) {
                report($e);
                $result['failed']++;
            }
        }

        return $result;
    }

    public function pause(string $enrolmentId): void
    {
        SequenceEnrolment::query()->whereKey($enrolmentId)->update(['status' => 'paused']);
    }

    public function resume(string $enrolmentId): void
    {
        SequenceEnrolment::query()->whereKey($enrolmentId)
            ->where('status', 'paused')
            ->update(['status' => 'active', 'next_step_at' => now()]);
    }

    public function unenrol(string $enrolmentId): void
    {
        SequenceEnrolment::query()->whereKey($enrolmentId)->update(['status' => 'unenrolled']);
    }

    /** Sequence owner → contact owner → any company user (scheduler has no auth). */
    private function ownerFor(SequenceEnrolment $enrolment): string
    {
        return $enrolment->sequence->owner_id
            ?? Contact::query()->whereKey($enrolment->contact_id)->value('owner_id')
            ?? User::query()->where('company_id', $enrolment->company_id)->value('id');
    }

    /** Prospect replied — stop every active enrolment for the contact. */
    public function pauseOnReply(string $contactId): void
    {
        SequenceEnrolment::query()
            ->where('contact_id', $contactId)
            ->where('status', 'active')
            ->update(['status' => 'paused']);
    }

    /** Enrols by trigger type — listeners call this; no matching sequence = no-op. */
    public function enrolByTrigger(string $triggerType, string $contactId, ?string $dealId = null): int
    {
        $enrolled = 0;

        $sequences = Sequence::query()
            ->where('trigger_type', $triggerType)
            ->where('is_active', true)
            ->get();

        foreach ($sequences as $sequence) {
            try {
                $this->enrol($sequence->id, $contactId, $dealId);
                $enrolled++;
            } catch (AlreadyEnrolledException) {
                // already running — skip
            }
        }

        return $enrolled;
    }
}
