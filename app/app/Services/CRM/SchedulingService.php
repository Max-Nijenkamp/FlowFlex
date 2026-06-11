<?php

declare(strict_types=1);

namespace App\Services\CRM;

use App\Contracts\CRM\ContactServiceInterface;
use App\Exceptions\CRM\SlotTakenException;
use App\Models\CRM\Activity;
use App\Models\CRM\Availability;
use App\Models\CRM\Booking;
use App\Models\CRM\MeetingType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class SchedulingService
{
    public function __construct(private readonly ContactServiceInterface $contacts) {}

    /**
     * Free slots for a day: working hours − existing bookings − buffers.
     *
     * @return array<string> ISO start times
     */
    public function slots(string $meetingTypeSlug, CarbonImmutable $day): array
    {
        $type = MeetingType::query()->where('slug', $meetingTypeSlug)->firstOrFail();
        $repId = $this->candidateRep($type);
        $hours = $this->workingHoursFor($repId, $day);

        if ($hours === null) {
            return [];
        }

        $taken = Booking::query()
            ->where('assigned_rep_id', $repId)
            ->where('status', 'confirmed')
            ->whereDate('scheduled_at', $day->toDateString())
            ->pluck('scheduled_at')
            ->map(fn ($t) => CarbonImmutable::parse($t));

        $slots = [];
        $cursor = $day->setTimeFromTimeString($hours['start']);
        $dayEnd = $day->setTimeFromTimeString($hours['end']);
        $step = $type->duration_minutes + $type->buffer_minutes;

        while ($cursor->addMinutes($type->duration_minutes)->lte($dayEnd)) {
            $slotEnd = $cursor->addMinutes($type->duration_minutes);
            $conflict = $taken->contains(fn (CarbonImmutable $t) => $t->lt($slotEnd)
                && $t->addMinutes($type->duration_minutes + $type->buffer_minutes)->gt($cursor));

            if (! $conflict && $cursor->isFuture()) {
                $slots[] = $cursor->toIso8601String();
            }
            $cursor = $cursor->addMinutes($step);
        }

        return $slots;
    }

    /**
     * Books a slot: re-validated inside the transaction; find-or-creates the
     * contact; logs an activity. Round-robin = least bookings this week.
     */
    public function book(
        string $meetingTypeSlug,
        string $scheduledAt,
        string $email,
        string $firstName,
        string $lastName,
    ): Booking {
        return DB::transaction(function () use ($meetingTypeSlug, $scheduledAt, $email, $firstName, $lastName): Booking {
            $type = MeetingType::query()->where('slug', $meetingTypeSlug)->firstOrFail();
            $repId = $this->candidateRep($type);
            $at = CarbonImmutable::parse($scheduledAt);

            $clash = Booking::query()
                ->where('assigned_rep_id', $repId)
                ->where('status', 'confirmed')
                ->where('scheduled_at', $at)
                ->lockForUpdate()
                ->exists();

            if ($clash) {
                throw new SlotTakenException;
            }

            $contact = $this->contacts->findOrCreateByEmail($email, [
                'first_name' => $firstName, 'last_name' => $lastName, 'source' => 'booking',
            ]);

            $booking = Booking::create([
                'meeting_type_id' => $type->id,
                'contact_id' => $contact->id,
                'assigned_rep_id' => $repId,
                'scheduled_at' => $at,
            ])->refresh();

            Activity::create([
                'company_id' => $booking->company_id,
                'type' => 'meeting',
                'subject' => "Booked: {$type->name}",
                'contact_id' => $contact->id,
                'owner_id' => $repId,
                'due_at' => $at,
            ]);

            return $booking;
        });
    }

    public function cancel(string $bookingId): Booking
    {
        $booking = Booking::query()->findOrFail($bookingId);
        $booking->update(['status' => 'cancelled']);

        return $booking->refresh();
    }

    /** Round-robin pool: least confirmed bookings this week; owner type = owner. */
    private function candidateRep(MeetingType $type): string
    {
        if ($type->owner_id !== null) {
            return $type->owner_id;
        }

        $pool = collect($type->team_user_ids ?? []);

        return $pool
            ->sortBy(fn (string $userId) => Booking::query()
                ->where('assigned_rep_id', $userId)
                ->where('status', 'confirmed')
                ->where('scheduled_at', '>=', now()->startOfWeek())
                ->count())
            ->first() ?? throw new \RuntimeException('Meeting type has no owner or team pool.');
    }

    /** @return array{start: string, end: string}|null */
    private function workingHoursFor(string $userId, CarbonImmutable $day): ?array
    {
        $availability = Availability::query()->where('user_id', $userId)->first();

        // Default 09:00–17:00 weekdays when no availability row exists *(assumed)*.
        if ($availability === null) {
            return $day->isWeekend() ? null : ['start' => '09:00', 'end' => '17:00'];
        }

        return $availability->working_hours[strtolower($day->format('l'))] ?? null;
    }
}
