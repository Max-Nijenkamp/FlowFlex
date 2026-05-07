<?php

use App\Enums\Finance\InvoiceStatus;
use App\Models\Finance\Invoice;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'finance', 'finance');
    givePermissions($this->tenant, [
        'finance.invoices.view',
        'finance.invoices.create',
        'finance.invoices.edit',
        'finance.invoices.delete',
    ]);

    $this->invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-001',
        'currency'   => 'EUR',
        'issue_date' => now(),
        'due_date'   => now()->addDays(30),
        'status'     => InvoiceStatus::Draft->value,
        'subtotal'   => '1000.00',
        'tax_amount' => '210.00',
        'total'      => '1210.00',
    ]);
});

it('authenticated tenant with permission can list invoices', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/finance/invoices')
        ->assertOk();
});

it('unauthenticated request redirects from invoices list', function () {
    $this->get('/finance/invoices')->assertRedirect();
});

it('tenant without permission gets 403 on invoices list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/finance/invoices')
        ->assertForbidden();
});

it('can create an invoice via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Finance\Resources\InvoiceResource\Pages\CreateInvoice::class)
        ->fillForm([
            'number'     => 'INV-002',
            'currency'   => 'EUR',
            'issue_date' => now()->toDateString(),
            'due_date'   => now()->addDays(30)->toDateString(),
            'status'     => InvoiceStatus::Draft->value,
            'subtotal'   => '500.00',
            'tax_amount' => '0.00',
            'total'      => '500.00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Invoice::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('number', 'INV-002')
        ->exists()
    )->toBeTrue();
});

it('can update an invoice via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Finance\Resources\InvoiceResource\Pages\EditInvoice::class,
            ['record' => $this->invoice->getRouteKey()]
        )
        ->fillForm(['number' => 'INV-001-UPD'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->invoice->fresh()->number)->toBe('INV-001-UPD');
});

it('can soft-delete an invoice', function () {
    $this->invoice->delete();

    expect($this->invoice->trashed())->toBeTrue();
    expect(Invoice::withTrashed()->withoutGlobalScopes()->find($this->invoice->id))->not->toBeNull();
});

it('tenant from another company cannot see invoices from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'finance.invoices.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(Invoice::all()->pluck('id'))->not->toContain($this->invoice->id);
});
