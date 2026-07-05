<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\Crm\Activity;
use App\Models\Crm\Contact;
use App\Models\User;
use App\Notifications\ActivityReminderNotification;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Notification;

function activityCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    return [$company, $owner];
}

test('the reminder command notifies owners of due tasks exactly once', function () {
    Notification::fake();
    [$company, $owner] = activityCompany();
    $contact = Contact::factory()->create(['company_id' => $company->id, 'owner_id' => $owner->id]);

    $task = Activity::factory()->task()->create([
        'company_id' => $company->id, 'owner_id' => $owner->id,
        'contact_id' => $contact->id, 'due_at' => now()->addHours(2),
    ]);

    $this->artisan('crm:send-activity-reminders')->assertSuccessful();
    $this->artisan('crm:send-activity-reminders')->assertSuccessful(); // second run: reminded_at guard

    Notification::assertSentToTimes($owner, ActivityReminderNotification::class, 1);
    expect($task->fresh()->reminded_at)->not->toBeNull();
});

test('tasks due far in the future are not reminded; completed tasks are skipped', function () {
    Notification::fake();
    [$company, $owner] = activityCompany();
    $contact = Contact::factory()->create(['company_id' => $company->id, 'owner_id' => $owner->id]);

    Activity::factory()->task()->create([
        'company_id' => $company->id, 'owner_id' => $owner->id,
        'contact_id' => $contact->id, 'due_at' => now()->addDays(9),
    ]);
    Activity::factory()->task()->create([
        'company_id' => $company->id, 'owner_id' => $owner->id,
        'contact_id' => $contact->id, 'due_at' => now()->subHour(), 'is_complete' => true,
    ]);

    $this->artisan('crm:send-activity-reminders')->assertSuccessful();

    Notification::assertNothingSent();
});

test('overdue detection flags incomplete past-due tasks only', function () {
    [$company, $owner] = activityCompany();
    $contact = Contact::factory()->create(['company_id' => $company->id, 'owner_id' => $owner->id]);

    $overdue = Activity::factory()->task()->create([
        'company_id' => $company->id, 'owner_id' => $owner->id,
        'contact_id' => $contact->id, 'due_at' => now()->subDay(),
    ]);
    $open = Activity::factory()->task()->create([
        'company_id' => $company->id, 'owner_id' => $owner->id,
        'contact_id' => $contact->id, 'due_at' => now()->addDay(),
    ]);

    expect($overdue->isOverdue())->toBeTrue()
        ->and($open->isOverdue())->toBeFalse();
});

test('tenant isolation: reminders for company A never reach company B owners', function () {
    Notification::fake();
    [$companyA, $ownerA] = activityCompany();
    $contactA = Contact::factory()->create(['company_id' => $companyA->id, 'owner_id' => $ownerA->id]);
    Activity::factory()->task()->create([
        'company_id' => $companyA->id, 'owner_id' => $ownerA->id,
        'contact_id' => $contactA->id, 'due_at' => now()->addHour(),
    ]);

    [, $ownerB] = activityCompany();

    $this->artisan('crm:send-activity-reminders')->assertSuccessful();

    Notification::assertSentToTimes($ownerA, ActivityReminderNotification::class, 1);
    Notification::assertNotSentTo($ownerB, ActivityReminderNotification::class);
});
