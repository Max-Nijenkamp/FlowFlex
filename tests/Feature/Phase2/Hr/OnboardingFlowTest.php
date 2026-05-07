<?php

use App\Enums\Hr\OnboardingFlowStatus;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\OnboardingFlow;
use App\Models\Hr\OnboardingTemplate;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.onboarding-flows.view',
        'hr.onboarding-flows.create',
        'hr.onboarding-flows.edit',
        'hr.onboarding-flows.delete',
    ]);

    $this->employee = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Bob',
        'last_name'  => 'Builder',
        'email'      => 'bob@test.com',
        'start_date' => '2024-01-01',
    ]);

    $this->template = OnboardingTemplate::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Standard',
        'is_active'  => true,
    ]);

    $this->flow = OnboardingFlow::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'employee_id' => $this->employee->id,
        'template_id' => $this->template->id,
        'status'      => OnboardingFlowStatus::NotStarted->value,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list onboarding flows', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/onboarding-flows')
        ->assertOk();
});

it('unauthenticated request redirects from onboarding flows list', function () {
    $this->get('/hr/onboarding-flows')->assertRedirect();
});

it('tenant without permission gets 403 on onboarding flows list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/onboarding-flows')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create an onboarding flow record', function () {
    $employee2 = Employee::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'New',
        'last_name'  => 'Hire',
        'email'      => 'newhire@test.com',
        'start_date' => '2024-03-01',
    ]);

    $flow = OnboardingFlow::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'employee_id' => $employee2->id,
        'template_id' => $this->template->id,
        'status'      => OnboardingFlowStatus::NotStarted->value,
    ]);

    expect($flow->exists)->toBeTrue();
    expect($flow->status)->toBe(OnboardingFlowStatus::NotStarted);
});

// ---------- Status transitions ----------

it('can transition flow status to in_progress', function () {
    $this->flow->update([
        'status'     => OnboardingFlowStatus::InProgress->value,
        'started_at' => now(),
    ]);

    expect($this->flow->fresh()->status)->toBe(OnboardingFlowStatus::InProgress);
});

it('can transition flow status to completed', function () {
    $this->flow->update([
        'status'       => OnboardingFlowStatus::Completed->value,
        'completed_at' => now(),
    ]);

    expect($this->flow->fresh()->status)->toBe(OnboardingFlowStatus::Completed);
});

// ---------- Delete ----------

it('can soft-delete an onboarding flow', function () {
    $this->flow->delete();

    expect($this->flow->trashed())->toBeTrue();
    expect(OnboardingFlow::withTrashed()->withoutGlobalScopes()->find($this->flow->id))->not->toBeNull();
});

it('soft-deleted flows do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->flow->delete();

    expect(OnboardingFlow::all()->pluck('id'))->not->toContain($this->flow->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see flows from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.onboarding-flows.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(OnboardingFlow::all()->pluck('id'))->not->toContain($this->flow->id);
});

// ---------- Progress ----------

it('progressPercentage returns 0 when no tasks', function () {
    expect($this->flow->progressPercentage())->toBe(0);
});

it('employee relationship loads correctly', function () {
    $this->flow->load('employee');

    expect($this->flow->employee->id)->toBe($this->employee->id);
});
