<?php

use App\Models\Company;
use App\Models\Hr\PayrollEntity;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.payroll.view',
        'hr.payroll.create',
        'hr.payroll.edit',
        'hr.payroll.delete',
    ]);

    $this->entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'    => $this->company->id,
        'name'          => 'Main Payroll',
        'legal_name'    => 'ACME Ltd',
        'country_code'  => 'NL',
        'is_default'    => true,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list payroll entities', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/payroll-entities')
        ->assertOk();
});

it('unauthenticated request redirects from payroll entities list', function () {
    $this->get('/hr/payroll-entities')->assertRedirect();
});

it('tenant without permission gets 403 on payroll entities list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/payroll-entities')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a payroll entity', function () {
    $entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'name'         => 'Secondary Payroll',
        'legal_name'   => 'ACME BV',
        'country_code' => 'BE',
        'is_default'   => false,
    ]);

    expect($entity->exists)->toBeTrue();
    expect($entity->name)->toBe('Secondary Payroll');
});

// ---------- Edit ----------

it('can update a payroll entity', function () {
    $this->entity->update(['name' => 'Primary Payroll Updated']);

    expect($this->entity->fresh()->name)->toBe('Primary Payroll Updated');
});

// ---------- Delete ----------

it('can soft-delete a payroll entity', function () {
    $this->entity->delete();

    expect($this->entity->trashed())->toBeTrue();
    expect(PayrollEntity::withTrashed()->withoutGlobalScopes()->find($this->entity->id))->not->toBeNull();
});

it('soft-deleted payroll entities do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->entity->delete();

    expect(PayrollEntity::all()->pluck('id'))->not->toContain($this->entity->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see payroll entities from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.payroll.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(PayrollEntity::all()->pluck('id'))->not->toContain($this->entity->id);
});

it('is_default casts to boolean', function () {
    expect($this->entity->is_default)->toBeTrue();
});
