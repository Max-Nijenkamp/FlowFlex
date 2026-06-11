<?php

declare(strict_types=1);

use App\Actions\HR\UpdateOwnProfileAction;
use App\Data\HR\UpdateOwnProfileData;
use App\Models\Company;
use App\Models\HR\EmergencyContact;
use App\Models\HR\Employee;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');
});

it('updates only own phone, personal email and emergency contacts', function () {
    $employee = Employee::factory()->forCompany($this->company)->create([
        'user_id' => $this->user->id,
        'job_title' => 'Original Title',
    ]);

    $result = UpdateOwnProfileAction::run(UpdateOwnProfileData::from([
        'phone' => '+31612345678',
        'personal_email' => 'personal@home.test',
        'emergency_contacts' => [
            ['name' => 'Partner', 'relationship' => 'spouse', 'phone' => '+31687654321'],
        ],
    ]));

    expect($result->phone)->toBe('+31612345678')
        ->and($result->personal_email)->toBe('personal@home.test')
        ->and($result->job_title)->toBe('Original Title') // HR-only field untouched
        ->and(EmergencyContact::query()->where('employee_id', $employee->id)->count())->toBe(1);
});

it('replaces emergency contacts on each save and caps at 3', function () {
    Employee::factory()->forCompany($this->company)->create(['user_id' => $this->user->id]);

    UpdateOwnProfileAction::run(UpdateOwnProfileData::from([
        'emergency_contacts' => [
            ['name' => 'A', 'relationship' => 'friend', 'phone' => '+31600000001'],
            ['name' => 'B', 'relationship' => 'friend', 'phone' => '+31600000002'],
        ],
    ]));
    UpdateOwnProfileAction::run(UpdateOwnProfileData::from([
        'emergency_contacts' => [
            ['name' => 'C', 'relationship' => 'parent', 'phone' => '+31600000003'],
        ],
    ]));

    expect(EmergencyContact::count())->toBe(1)
        ->and(EmergencyContact::query()->first()->name)->toBe('C');

    UpdateOwnProfileData::validateAndCreate([
        'emergency_contacts' => [
            ['name' => '1', 'relationship' => 'x', 'phone' => '1'],
            ['name' => '2', 'relationship' => 'x', 'phone' => '2'],
            ['name' => '3', 'relationship' => 'x', 'phone' => '3'],
            ['name' => '4', 'relationship' => 'x', 'phone' => '4'],
        ],
    ]);
})->throws(ValidationException::class);

it('refuses when no employee record is linked', function () {
    UpdateOwnProfileAction::run(UpdateOwnProfileData::from(['phone' => '+31612345678']));
})->throws(AuthorizationException::class);

it('never touches another employee even with the same data shape', function () {
    $own = Employee::factory()->forCompany($this->company)->create(['user_id' => $this->user->id]);
    $other = Employee::factory()->forCompany($this->company)->create(['phone' => '+31699999999']);

    UpdateOwnProfileAction::run(UpdateOwnProfileData::from(['phone' => '+31611111111']));

    expect($own->fresh()->phone)->toBe('+31611111111')
        ->and($other->fresh()->phone)->toBe('+31699999999');
});
