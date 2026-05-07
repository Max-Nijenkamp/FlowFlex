<?php

use App\Models\Company;
use App\Models\Hr\LeaveType;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.leave-types.view',
        'hr.leave-types.create',
        'hr.leave-types.edit',
        'hr.leave-types.delete',
    ]);

    $this->leaveType = LeaveType::withoutGlobalScopes()->create([
        'company_id'        => $this->company->id,
        'name'              => 'Annual Leave',
        'code'              => 'AL',
        'is_paid'           => true,
        'requires_approval' => true,
        'is_active'         => true,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list leave types', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/leave-types')
        ->assertOk();
});

it('unauthenticated request redirects from leave types list', function () {
    $this->get('/hr/leave-types')->assertRedirect();
});

it('tenant without permission gets 403 on leave types list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/leave-types')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a leave type record', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Hr\Resources\LeaveTypeResource\Pages\CreateLeaveType::class)
        ->fillForm([
            'name'              => 'Sick Leave',
            'code'              => 'SL',
            'is_paid'           => true,
            'requires_approval' => false,
            'is_active'         => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(LeaveType::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('code', 'SL')
        ->exists()
    )->toBeTrue();
});

// ---------- Edit ----------

it('can update a leave type', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Hr\Resources\LeaveTypeResource\Pages\EditLeaveType::class,
            ['record' => $this->leaveType->getRouteKey()]
        )
        ->fillForm(['name' => 'Annual Holiday'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->leaveType->fresh()->name)->toBe('Annual Holiday');
});

// ---------- Delete ----------

it('can soft-delete a leave type', function () {
    $this->leaveType->delete();

    expect($this->leaveType->trashed())->toBeTrue();
    expect(LeaveType::withTrashed()->withoutGlobalScopes()->find($this->leaveType->id))->not->toBeNull();
});

it('soft-deleted leave types do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->leaveType->delete();

    expect(LeaveType::all()->pluck('id'))->not->toContain($this->leaveType->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see leave types from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.leave-types.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(LeaveType::all()->pluck('id'))->not->toContain($this->leaveType->id);
});

// ---------- Attributes ----------

it('leave type casts boolean fields correctly', function () {
    expect($this->leaveType->is_paid)->toBeTrue();
    expect($this->leaveType->requires_approval)->toBeTrue();
    expect($this->leaveType->is_active)->toBeTrue();
});
