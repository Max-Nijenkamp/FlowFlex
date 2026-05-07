<?php

use App\Enums\Projects\TimesheetStatus;
use App\Models\Company;
use App\Models\Projects\Timesheet;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'projects', 'projects');
    givePermissions($this->tenant, [
        'projects.timesheets.view',
        'projects.timesheets.create',
        'projects.timesheets.edit',
        'projects.timesheets.delete',
    ]);

    $this->timesheet = Timesheet::withoutGlobalScopes()->create([
        'company_id'      => $this->company->id,
        'tenant_id'       => $this->tenant->id,
        'week_start_date' => '2024-08-05',
        'status'          => TimesheetStatus::Draft->value,
        'total_minutes'   => 0,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list timesheets', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/projects/timesheets')
        ->assertOk();
});

it('unauthenticated request redirects from timesheets list', function () {
    $this->get('/projects/timesheets')->assertRedirect();
});

it('tenant without permission gets 403 on timesheets list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/projects/timesheets')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a timesheet', function () {
    $sheet = Timesheet::withoutGlobalScopes()->create([
        'company_id'      => $this->company->id,
        'tenant_id'       => $this->tenant->id,
        'week_start_date' => '2024-08-12',
        'status'          => TimesheetStatus::Draft->value,
        'total_minutes'   => 0,
    ]);

    expect($sheet->exists)->toBeTrue();
    expect($sheet->status)->toBe(TimesheetStatus::Draft);
});

// ---------- Submit ----------

it('can submit a timesheet changing status to submitted', function () {
    $this->timesheet->update([
        'status'       => TimesheetStatus::Submitted->value,
        'submitted_at' => now(),
        'total_minutes'=> 2400,
    ]);

    expect($this->timesheet->fresh()->status)->toBe(TimesheetStatus::Submitted);
});

// ---------- Edit ----------

it('can update a timesheet', function () {
    $this->timesheet->update(['total_minutes' => 3600]);

    expect($this->timesheet->fresh()->total_minutes)->toBe(3600);
});

// ---------- Delete ----------

it('can soft-delete a timesheet', function () {
    $this->timesheet->delete();

    expect($this->timesheet->trashed())->toBeTrue();
    expect(Timesheet::withTrashed()->withoutGlobalScopes()->find($this->timesheet->id))->not->toBeNull();
});

it('soft-deleted timesheets do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->timesheet->delete();

    expect(Timesheet::all()->pluck('id'))->not->toContain($this->timesheet->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see timesheets from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'projects.timesheets.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(Timesheet::all()->pluck('id'))->not->toContain($this->timesheet->id);
});

// ---------- Enum casts ----------

it('status casts to TimesheetStatus enum', function () {
    expect($this->timesheet->status)->toBe(TimesheetStatus::Draft);
});
