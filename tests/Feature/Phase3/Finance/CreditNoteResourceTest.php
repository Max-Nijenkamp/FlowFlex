<?php

use App\Enums\Finance\InvoiceStatus;
use App\Models\Finance\CreditNote;
use App\Models\Finance\Invoice;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'finance', 'finance');
    givePermissions($this->tenant, [
        'finance.credit-notes.view',
        'finance.credit-notes.create',
        'finance.credit-notes.edit',
        'finance.credit-notes.delete',
    ]);

    $this->invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-CN-001',
        'currency'   => 'EUR',
        'issue_date' => now(),
        'due_date'   => now()->addDays(30),
        'status'     => InvoiceStatus::Paid->value,
        'subtotal'   => '1000.00',
        'tax_amount' => '210.00',
        'total'      => '1210.00',
        'paid_amount'=> '1210.00',
    ]);

    $this->creditNote = CreditNote::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'invoice_id' => $this->invoice->id,
        'number'     => 'CN-001',
        'issued_at'  => now(),
        'amount'     => '1210.00',
        'reason'     => 'Faulty goods',
    ]);
});

it('authenticated tenant with permission can list credit notes', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/finance/credit-notes')
        ->assertOk();
});

it('unauthenticated request redirects from credit notes list', function () {
    $this->get('/finance/credit-notes')->assertRedirect();
});

it('tenant without permission gets 403 on credit notes list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/finance/credit-notes')
        ->assertForbidden();
});

it('can create a credit note via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Finance\Resources\CreditNoteResource\Pages\CreateCreditNote::class)
        ->fillForm([
            'number'    => 'CN-002',
            'amount'    => '100.00',
            'issued_at' => now()->toDateString(),
            'reason'    => 'Partial refund',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(CreditNote::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('number', 'CN-002')
        ->exists()
    )->toBeTrue();
});

it('can update a credit note via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Finance\Resources\CreditNoteResource\Pages\EditCreditNote::class,
            ['record' => $this->creditNote->getRouteKey()]
        )
        ->fillForm(['reason' => 'Updated reason'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->creditNote->fresh()->reason)->toBe('Updated reason');
});

it('credit note eager-loads invoice to prevent N+1', function () {
    $query = CreditNote::withoutGlobalScopes()->with(['invoice']);
    $notes = $query->get();

    expect($notes->every(fn ($n) => $n->relationLoaded('invoice')))->toBeTrue();
});

it('tenant from another company cannot see credit notes from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'finance.credit-notes.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(CreditNote::all()->pluck('id'))->not->toContain($this->creditNote->id);
});
