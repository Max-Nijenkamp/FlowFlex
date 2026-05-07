<?php

use App\Models\Finance\RecurringInvoice;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'finance', 'finance');
    givePermissions($this->tenant, [
        'finance.recurring-invoices.view',
        'finance.recurring-invoices.create',
        'finance.recurring-invoices.edit',
        'finance.recurring-invoices.delete',
    ]);

    $this->invoice = RecurringInvoice::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'frequency'   => 'monthly',
        'next_run_at' => '2026-06-01',
        'is_active'   => true,
    ]);
});

it('authenticated tenant with permission can list recurring invoices', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/finance/recurring-invoices')
        ->assertOk();
});

it('unauthenticated request redirects from recurring invoices list', function () {
    $this->get('/finance/recurring-invoices')->assertRedirect();
});

it('tenant without permission gets 403 on recurring invoices list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/finance/recurring-invoices')
        ->assertForbidden();
});

it('can create a recurring invoice via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Finance\Resources\RecurringInvoiceResource\Pages\CreateRecurringInvoice::class)
        ->fillForm([
            'frequency'   => 'quarterly',
            'next_run_at' => '2026-07-01',
            'is_active'   => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(RecurringInvoice::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('frequency', 'quarterly')
        ->exists()
    )->toBeTrue();
});

it('can update a recurring invoice via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Finance\Resources\RecurringInvoiceResource\Pages\EditRecurringInvoice::class,
            ['record' => $this->invoice->getRouteKey()]
        )
        ->fillForm(['frequency' => 'annually'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->invoice->fresh()->frequency)->toBe('annually');
});

it('recurring invoice is_active casts to boolean', function () {
    expect($this->invoice->is_active)->toBeTrue();
});

it('tenant from another company cannot see recurring invoices from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'finance.recurring-invoices.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(RecurringInvoice::all()->pluck('id'))->not->toContain($this->invoice->id);
});
