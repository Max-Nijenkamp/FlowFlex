<?php

declare(strict_types=1);

use App\Actions\Core\CompleteSetupAction;
use App\Contracts\Core\BillingServiceInterface;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create(['setup_completed_at' => null]);
    $this->setCompany($this->company);
    app(BillingServiceInterface::class)->seedFreeCoreModules($this->company->id);
    Role::create(['name' => 'owner', 'guard_name' => 'web']);
    Role::create(['name' => 'employee', 'guard_name' => 'web']);
});

it('redirects an owner with incomplete setup to the wizard', function () {
    $owner = User::factory()->forCompany($this->company)->create();
    $owner->assignRole('owner');

    $this->actingAs($owner, 'web')->get('/app')->assertRedirect('/app/setup-wizard');
});

it('does not redirect non-owners', function () {
    $employee = User::factory()->forCompany($this->company)->create();
    $employee->assignRole('employee');

    $this->actingAs($employee, 'web')->get('/app')->assertSuccessful();
});

it('does not redirect once setup is complete', function () {
    $owner = User::factory()->forCompany($this->company)->create();
    $owner->assignRole('owner');

    $this->actingAs($owner, 'web');
    CompleteSetupAction::run();

    $this->get('/app')->assertSuccessful();
});

it('renders the wizard for an owner', function () {
    $owner = User::factory()->forCompany($this->company)->create();
    $owner->assignRole('owner');

    $this->actingAs($owner, 'web')->get('/app/setup-wizard')->assertSuccessful();
});

it('hides the wizard from non-owners (canAccess)', function () {
    $employee = User::factory()->forCompany($this->company)->create();
    $employee->assignRole('employee');

    $this->actingAs($employee, 'web')->get('/app/setup-wizard')->assertForbidden();
});
