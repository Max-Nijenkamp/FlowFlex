<?php

declare(strict_types=1);

use App\Actions\Core\MarkAllReadAction;
use App\Contracts\Core\BillingServiceInterface;
use App\Data\Core\ActivateModuleData;
use App\Models\Company;
use App\Models\Core\NotificationPreference;
use App\Models\User;
use App\Notifications\Core\ModuleActivatedNotification;
use App\Services\Core\NotificationPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
});

it('defaults to both channels without a stored preference', function () {
    $channels = app(NotificationPreferenceService::class)
        ->channelsFor($this->user, ModuleActivatedNotification::class);

    expect($channels)->toBe(['database', 'mail']);
});

it('suppresses mail when email preference is off, keeps in-app', function () {
    NotificationPreference::factory()->create([
        'company_id' => $this->company->id,
        'user_id' => $this->user->id,
        'notification_type' => ModuleActivatedNotification::class,
        'email_enabled' => false,
    ]);

    $channels = app(NotificationPreferenceService::class)
        ->channelsFor($this->user, ModuleActivatedNotification::class);

    expect($channels)->toBe(['database']);
});

it('suppresses mail for undeliverable addresses', function () {
    $this->user->update(['email_deliverable' => false]);

    $channels = app(NotificationPreferenceService::class)
        ->channelsFor($this->user, ModuleActivatedNotification::class);

    expect($channels)->toBe(['database']);
});

it('queues notifications on the notifications queue', function () {
    Notification::fake();

    $this->user->notify(new ModuleActivatedNotification('core.settings'));

    Notification::assertSentTo(
        $this->user,
        ModuleActivatedNotification::class,
        fn (ModuleActivatedNotification $n) => $n->queue === 'notifications',
    );
});

it('notifies owners when a module is activated (event listener)', function () {
    config()->set('flowflex.modules', [
        'hr.payroll' => ['name' => 'Payroll', 'per_user_monthly_price_cents' => 150],
    ]);
    Role::create(['name' => 'owner', 'guard_name' => 'web']);
    $this->user->assignRole('owner');
    $this->actingAs($this->user, 'web');

    app(BillingServiceInterface::class)->activateModule(new ActivateModuleData('hr.payroll'));

    // Sync queue in tests: listener ran inline and stored a database notification.
    expect($this->user->notifications()->count())->toBe(1)
        ->and($this->user->notifications()->first()->data['title'])->toContain('Payroll');
});

it('marks all notifications read', function () {
    $this->user->notify(new ModuleActivatedNotification('core.settings'));
    $this->user->notify(new ModuleActivatedNotification('core.audit'));

    expect($this->user->unreadNotifications()->count())->toBe(2);

    MarkAllReadAction::run($this->user);

    expect($this->user->fresh()->unreadNotifications()->count())->toBe(0);
});
