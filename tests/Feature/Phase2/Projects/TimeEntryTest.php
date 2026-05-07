<?php

use App\Enums\Projects\TaskStatus;
use App\Enums\Projects\TaskPriority;
use App\Models\Company;
use App\Models\Projects\Task;
use App\Models\Projects\TimeEntry;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'projects', 'projects');
    givePermissions($this->tenant, [
        'projects.time.view',
        'projects.time.create',
        'projects.time.edit',
        'projects.time.delete',
    ]);

    $this->task = Task::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Test Task',
        'status'     => TaskStatus::Todo->value,
        'priority'   => TaskPriority::Medium->value,
    ]);

    $this->entry = TimeEntry::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'tenant_id'   => $this->tenant->id,
        'task_id'     => $this->task->id,
        'description' => 'Working on feature',
        'entry_date'  => '2024-08-01',
        'minutes'     => 90,
        'is_billable' => true,
        'is_approved' => false,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list time entries', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/projects/time-entries')
        ->assertOk();
});

it('unauthenticated request redirects from time entries list', function () {
    $this->get('/projects/time-entries')->assertRedirect();
});

it('tenant without permission gets 403 on time entries list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/projects/time-entries')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a time entry', function () {
    $entry = TimeEntry::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'tenant_id'   => $this->tenant->id,
        'task_id'     => $this->task->id,
        'entry_date'  => '2024-08-02',
        'minutes'     => 60,
        'is_billable' => false,
        'is_approved' => false,
    ]);

    expect($entry->exists)->toBeTrue();
    expect($entry->minutes)->toBe(60);
});

// ---------- Edit ----------

it('can update a time entry', function () {
    $this->entry->update(['minutes' => 120]);

    expect($this->entry->fresh()->minutes)->toBe(120);
});

// ---------- Delete ----------

it('can soft-delete a time entry', function () {
    $this->entry->delete();

    expect($this->entry->trashed())->toBeTrue();
    expect(TimeEntry::withTrashed()->withoutGlobalScopes()->find($this->entry->id))->not->toBeNull();
});

it('soft-deleted time entries do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->entry->delete();

    expect(TimeEntry::all()->pluck('id'))->not->toContain($this->entry->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see time entries from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'projects.time.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(TimeEntry::all()->pluck('id'))->not->toContain($this->entry->id);
});

// ---------- Helper methods ----------

it('hoursDecimal returns correct value', function () {
    expect($this->entry->hoursDecimal())->toBe(1.5);
});

it('is_billable casts to boolean', function () {
    expect($this->entry->is_billable)->toBeTrue();
    expect($this->entry->is_approved)->toBeFalse();
});
