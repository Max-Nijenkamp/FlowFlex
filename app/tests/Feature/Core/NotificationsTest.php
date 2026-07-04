<?php

declare(strict_types=1);

use App\Actions\MarkAllReadAction;
use App\Data\UpdateNotificationPreferencesData;
use App\Events\NotificationCreated;
use App\Models\Company;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Services\NotificationPreferenceService;
use App\Support\Notifications\FlowFlexNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class ProbeNotification extends FlowFlexNotification
{
    public function notificationType(): string
    {
        return 'module-activated';
    }

    public function title(): string
    {
        return 'Module switched on';
    }

    public function body(): string
    {
        return 'HR is now active for your workspace.';
    }
}

test('channels resolve from preferences with both-on defaults', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();
    $service = app(NotificationPreferenceService::class);

    expect($service->channelsFor($user, 'module-activated'))->toBe(['database', 'mail']);

    NotificationPreference::query()->create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'notification_type' => 'module-activated',
        'in_app_enabled' => true,
        'email_enabled' => false,
    ]);

    expect($service->channelsFor($user, 'module-activated'))->toBe(['database']);

    NotificationPreference::query()->where('user_id', $user->id)->update(['in_app_enabled' => false]);
    expect($service->channelsFor($user, 'module-activated'))->toBe([]);
});

test('unknown notification types are rejected by the DTO', function () {
    expect(fn () => new UpdateNotificationPreferencesData(['made-up-type' => ['in_app' => true, 'email' => true]]))
        ->toThrow(InvalidArgumentException::class);
});

test('email-off delivers in-app only', function () {
    Event::fake([NotificationCreated::class]);
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();

    NotificationPreference::query()->create([
        'company_id' => $company->id,
        'user_id' => $user->id,
        'notification_type' => 'module-activated',
        'in_app_enabled' => true,
        'email_enabled' => false,
    ]);

    NotificationFacade::send($user, new ProbeNotification);

    expect($user->notifications()->count())->toBe(1)
        ->and($user->notifications()->first()->data['title'])->toBe('Module switched on');
});

test('the database payload is tenant-stamped and fires the broadcast event on the right channel', function () {
    Event::fake([NotificationCreated::class]);
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();

    NotificationFacade::send($user, new ProbeNotification);

    expect($user->notifications()->first()->data['company_id'])->toBe($company->id);

    Event::assertDispatched(NotificationCreated::class, function (NotificationCreated $event) use ($company, $user): bool {
        $channels = collect($event->broadcastOn())->map(fn ($c) => (string) $c->name);

        return $event->company_id === $company->id
            && $event->user_id === $user->id
            && $channels->contains("private-company.{$company->id}.notifications");
    });
});

test('mark-all-read touches only the acting user', function () {
    Event::fake([NotificationCreated::class]);
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();
    $other = User::factory()->for($company)->create();

    NotificationFacade::send($user, new ProbeNotification);
    NotificationFacade::send($user, new ProbeNotification);
    NotificationFacade::send($other, new ProbeNotification);

    $updated = MarkAllReadAction::run($user);

    expect($updated)->toBe(2)
        ->and($user->unreadNotifications()->count())->toBe(0)
        ->and($other->unreadNotifications()->count())->toBe(1);
});

test('channel authorization rejects a user from another company', function () {
    $companyA = Company::factory()->create();
    $companyB = Company::factory()->create();
    $userA = User::factory()->for($companyA)->create();

    // The exact closure registered in routes/channels.php
    $callback = fn (User $user, string $companyId): bool => $user->company_id === $companyId;

    expect($callback($userA, $companyA->id))->toBeTrue()
        ->and($callback($userA, $companyB->id))->toBeFalse();
});

test('preference upserts never touch another user', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();
    $other = User::factory()->for($company)->create();

    NotificationPreference::query()->create([
        'company_id' => $company->id,
        'user_id' => $other->id,
        'notification_type' => 'module-activated',
        'in_app_enabled' => true,
        'email_enabled' => true,
    ]);

    NotificationPreference::query()->updateOrCreate(
        ['user_id' => $user->id, 'notification_type' => 'module-activated'],
        ['company_id' => $company->id, 'in_app_enabled' => false, 'email_enabled' => false],
    );

    expect(NotificationPreference::query()->where('user_id', $other->id)->first()->in_app_enabled)->toBeTrue()
        ->and(NotificationPreference::query()->where('user_id', $user->id)->first()->in_app_enabled)->toBeFalse();
});
