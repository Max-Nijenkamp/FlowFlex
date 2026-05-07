<?php

use App\Enums\Finance\ExpenseStatus;
use App\Enums\Finance\InvoiceStatus;
use App\Models\Finance\Expense;
use App\Models\Finance\Invoice;
use App\Models\Tenant;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);
    ['key' => $this->plainKey] = makeApiKey($this->company);

    // Create test invoices for this company
    $this->invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-API-001',
        'currency'   => 'EUR',
        'issue_date' => '2026-05-01',
        'due_date'   => '2026-05-31',
        'status'     => InvoiceStatus::Sent->value,
        'subtotal'   => '1000.00',
        'tax_amount' => '210.00',
        'total'      => '1210.00',
    ]);

    // Create test expense for this company
    $this->expense = Expense::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'tenant_id'    => $this->tenant->id,
        'description'  => 'Travel expense',
        'amount'       => '75.00',
        'currency'     => 'EUR',
        'expense_date' => '2026-05-01',
        'status'       => ExpenseStatus::Pending->value,
    ]);
});

// ---------- Invoices ----------

it('GET /api/v1/finance/invoices returns 401 without API key', function () {
    $this->getJson('/api/v1/finance/invoices')
        ->assertUnauthorized();
});

it('GET /api/v1/finance/invoices returns 200 with valid API key', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/finance/invoices')
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'meta' => ['total', 'per_page', 'current_page', 'last_page'],
        ]);
});

it('finance invoices API is scoped to the authenticated company', function () {
    $otherCompany = makeCompany();
    ['key' => $otherKey] = makeApiKey($otherCompany);

    $otherInvoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $otherCompany->id,
        'number'     => 'INV-OTHER-001',
        'currency'   => 'EUR',
        'issue_date' => '2026-05-01',
        'due_date'   => '2026-05-31',
        'status'     => InvoiceStatus::Draft->value,
        'subtotal'   => '500.00',
        'tax_amount' => '0.00',
        'total'      => '500.00',
    ]);

    $response = $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/finance/invoices')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($this->invoice->id)
                ->not->toContain($otherInvoice->id);
});

it('GET /api/v1/finance/invoices/{id} returns single invoice', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson("/api/v1/finance/invoices/{$this->invoice->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $this->invoice->id)
        ->assertJsonPath('data.number', 'INV-API-001');
});

// ---------- Expenses ----------

it('GET /api/v1/finance/expenses returns 401 without API key', function () {
    $this->getJson('/api/v1/finance/expenses')
        ->assertUnauthorized();
});

it('GET /api/v1/finance/expenses returns 200 with valid API key', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/finance/expenses')
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'meta' => ['total', 'per_page', 'current_page', 'last_page'],
        ]);
});

it('GET /api/v1/finance/expenses/{id} returns single expense', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson("/api/v1/finance/expenses/{$this->expense->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $this->expense->id)
        ->assertJsonPath('data.description', 'Travel expense');
});
