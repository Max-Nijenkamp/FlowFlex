<?php

use App\Enums\Hr\PayElementType;
use App\Models\Company;
use App\Models\Hr\PayElement;
use App\Models\Hr\PayrollEntity;
use App\Models\Tenant;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'hr', 'hr');
    givePermissions($this->tenant, [
        'hr.pay-elements.view',
        'hr.pay-elements.create',
        'hr.pay-elements.edit',
        'hr.pay-elements.delete',
    ]);

    $this->entity = PayrollEntity::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Main Payroll',
        'legal_name' => 'Test Corp',
        'country_code' => 'NL',
        'is_default' => true,
    ]);

    $this->element = PayElement::withoutGlobalScopes()->create([
        'company_id'         => $this->company->id,
        'payroll_entity_id'  => $this->entity->id,
        'name'               => 'Basic Salary',
        'element_type'       => PayElementType::BasicSalary->value,
        'is_taxable'         => true,
        'is_pensionable'     => true,
        'is_active'          => true,
    ]);
});

// ---------- List ----------

it('authenticated tenant with permission can list pay elements', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/hr/pay-elements')
        ->assertOk();
});

it('unauthenticated request redirects from pay elements list', function () {
    $this->get('/hr/pay-elements')->assertRedirect();
});

it('tenant without permission gets 403 on pay elements list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/hr/pay-elements')
        ->assertForbidden();
});

// ---------- Create ----------

it('can create a pay element', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Hr\Resources\PayElementResource\Pages\CreatePayElement::class)
        ->fillForm([
            'payroll_entity_id' => $this->entity->id,
            'name'              => 'Overtime',
            'element_type'      => PayElementType::Overtime->value,
            'is_taxable'        => true,
            'is_pensionable'    => false,
            'is_active'         => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(PayElement::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Overtime')
        ->exists()
    )->toBeTrue();
});

// ---------- Edit ----------

it('can update a pay element', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('hr');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Hr\Resources\PayElementResource\Pages\EditPayElement::class,
            ['record' => $this->element->getRouteKey()]
        )
        ->fillForm(['name' => 'Base Salary'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->element->fresh()->name)->toBe('Base Salary');
});

// ---------- Delete ----------

it('can soft-delete a pay element', function () {
    $this->element->delete();

    expect($this->element->trashed())->toBeTrue();
    expect(PayElement::withTrashed()->withoutGlobalScopes()->find($this->element->id))->not->toBeNull();
});

it('soft-deleted pay elements do not appear in list', function () {
    $this->actingAs($this->tenant, 'tenant');
    $this->element->delete();

    expect(PayElement::all()->pluck('id'))->not->toContain($this->element->id);
});

// ---------- Company isolation ----------

it('tenant from another company cannot see pay elements from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'hr.pay-elements.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(PayElement::all()->pluck('id'))->not->toContain($this->element->id);
});

// ---------- Enums and casts ----------

it('element_type casts to PayElementType enum', function () {
    expect($this->element->element_type)->toBe(PayElementType::BasicSalary);
});

it('all PayElementType enum values are valid', function () {
    $types = PayElementType::cases();

    expect($types)->toHaveCount(6);
    expect(collect($types)->pluck('value')->all())->toContain('basic_salary');
    expect(collect($types)->pluck('value')->all())->toContain('deduction');
});
