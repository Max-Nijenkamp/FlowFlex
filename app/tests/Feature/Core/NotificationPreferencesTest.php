<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Core\NotificationPreference;
use App\Models\Core\NotificationQuietHours;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('Notification Preferences', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
    });

    it('creates notification preference for user', function () {
        $pref = NotificationPreference::create([
            'user_id'       => $this->user->id,
            'company_id'    => $this->company->id,
            'event_type'    => 'hr.leave.approved',
            'channel'       => 'database',
            'enabled'       => true,
            'delivery_mode' => 'realtime',
        ]);

        expect($pref->enabled)->toBeTrue();
        expect($pref->event_type)->toBe('hr.leave.approved');
    });

    it('enforces unique preference per user/event/channel', function () {
        NotificationPreference::create([
            'user_id'    => $this->user->id,
            'company_id' => $this->company->id,
            'event_type' => 'hr.leave.approved',
            'channel'    => 'database',
            'enabled'    => true,
        ]);

        expect(fn () => NotificationPreference::create([
            'user_id'    => $this->user->id,
            'company_id' => $this->company->id,
            'event_type' => 'hr.leave.approved',
            'channel'    => 'database',
            'enabled'    => false,
        ]))->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
    });

    it('creates quiet hours for user', function () {
        $qh = NotificationQuietHours::create([
            'user_id'    => $this->user->id,
            'start_time' => '22:00:00',
            'end_time'   => '07:00:00',
            'timezone'   => 'Europe/Amsterdam',
        ]);

        expect($qh->user_id)->toBe($this->user->id);
    });

    it('enforces one quiet hours setting per user', function () {
        NotificationQuietHours::create([
            'user_id'    => $this->user->id,
            'start_time' => '22:00:00',
            'end_time'   => '07:00:00',
            'timezone'   => 'UTC',
        ]);

        expect(fn () => NotificationQuietHours::create([
            'user_id'    => $this->user->id,
            'start_time' => '23:00:00',
            'end_time'   => '08:00:00',
            'timezone'   => 'UTC',
        ]))->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
    });

    it('quiet hours are scoped to user not company', function () {
        $otherUser = User::factory()->create(['company_id' => $this->company->id]);

        NotificationQuietHours::create([
            'user_id'    => $this->user->id,
            'start_time' => '22:00:00',
            'end_time'   => '07:00:00',
            'timezone'   => 'UTC',
        ]);

        NotificationQuietHours::create([
            'user_id'    => $otherUser->id,
            'start_time' => '22:00:00',
            'end_time'   => '07:00:00',
            'timezone'   => 'UTC',
        ]);

        expect(NotificationQuietHours::count())->toBe(2);
    });

    it('allows null start_time and end_time in quiet hours', function () {
        $qh = NotificationQuietHours::create([
            'user_id'    => $this->user->id,
            'start_time' => null,
            'end_time'   => null,
            'timezone'   => 'UTC',
        ]);

        expect($qh->start_time)->toBeNull();
        expect($qh->end_time)->toBeNull();
    });

    it('quiet hours updateOrCreate does not crash when times are null', function () {
        expect(fn () => NotificationQuietHours::updateOrCreate(
            ['user_id' => $this->user->id],
            ['start_time' => null, 'end_time' => null, 'timezone' => 'UTC'],
        ))->not->toThrow(\Illuminate\Database\QueryException::class);
    });

    it('deleting quiet hours when both times are null removes the row', function () {
        NotificationQuietHours::create([
            'user_id'    => $this->user->id,
            'start_time' => '22:00:00',
            'end_time'   => '07:00:00',
            'timezone'   => 'UTC',
        ]);

        // Simulate saveQuietHours clearing: both null → delete
        NotificationQuietHours::where('user_id', $this->user->id)->delete();

        expect(NotificationQuietHours::where('user_id', $this->user->id)->count())->toBe(0);
    });
});
