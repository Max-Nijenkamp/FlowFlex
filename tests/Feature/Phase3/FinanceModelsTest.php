<?php

use App\Enums\Finance\ExpenseStatus;
use App\Enums\Finance\InvoiceStatus;
use App\Models\Company;
use App\Models\Finance\CreditNote;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use App\Models\Finance\ExpenseReport;
use App\Models\Finance\Invoice;
use App\Models\Finance\InvoiceLine;
use App\Models\Finance\MileageRate;
use App\Models\Tenant;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);
});

// ---------- Invoice ----------

it('can create an invoice with ULID primary key', function () {
    $invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-001',
        'currency'   => 'EUR',
        'issue_date' => '2026-05-01',
        'due_date'   => '2026-05-31',
        'status'     => InvoiceStatus::Draft->value,
        'subtotal'   => '1000.00',
        'tax_amount' => '210.00',
        'total'      => '1210.00',
    ]);

    expect($invoice->exists)->toBeTrue();
    expect($invoice->id)->toBeString()->toHaveLength(26); // ULID
    expect($invoice->status)->toBe(InvoiceStatus::Draft);
    expect($invoice->company_id)->toBe($this->company->id);
});

it('invoice supports soft deletes', function () {
    $invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-002',
        'currency'   => 'EUR',
        'issue_date' => '2026-05-01',
        'due_date'   => '2026-05-31',
        'status'     => InvoiceStatus::Draft->value,
        'subtotal'   => '500.00',
        'tax_amount' => '0.00',
        'total'      => '500.00',
    ]);

    $invoice->delete();

    expect($invoice->trashed())->toBeTrue();
    expect(Invoice::withTrashed()->withoutGlobalScopes()->find($invoice->id))->not->toBeNull();
});

it('invoice has company scope applied', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);

    $invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-003',
        'currency'   => 'EUR',
        'issue_date' => '2026-05-01',
        'due_date'   => '2026-05-31',
        'status'     => InvoiceStatus::Draft->value,
        'subtotal'   => '100.00',
        'tax_amount' => '0.00',
        'total'      => '100.00',
    ]);

    $this->actingAs($otherTenant, 'tenant');

    expect(Invoice::all()->pluck('id'))->not->toContain($invoice->id);
});

// ---------- Expense ----------

it('can create an expense with correct enum cast', function () {
    $expense = Expense::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'tenant_id'    => $this->tenant->id,
        'description'  => 'Team lunch',
        'amount'       => '85.50',
        'currency'     => 'EUR',
        'expense_date' => '2026-05-01',
        'status'       => ExpenseStatus::Pending->value,
    ]);

    expect($expense->exists)->toBeTrue();
    expect($expense->status)->toBe(ExpenseStatus::Pending);
    expect($expense->id)->toHaveLength(26);
});

it('expense supports soft deletes', function () {
    $expense = Expense::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'tenant_id'    => $this->tenant->id,
        'description'  => 'Travel',
        'amount'       => '50.00',
        'currency'     => 'EUR',
        'expense_date' => '2026-05-01',
        'status'       => ExpenseStatus::Pending->value,
    ]);

    $expense->delete();
    expect($expense->trashed())->toBeTrue();
});

// ---------- ExpenseReport ----------

it('can create an expense report', function () {
    $report = ExpenseReport::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'tenant_id'  => $this->tenant->id,
        'title'      => 'May 2026 Expenses',
        'status'     => 'draft',
    ]);

    expect($report->exists)->toBeTrue();
    expect($report->id)->toHaveLength(26);
});

// ---------- ExpenseCategory ----------

it('can create an expense category', function () {
    $category = ExpenseCategory::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Travel',
        'is_active'  => true,
    ]);

    expect($category->exists)->toBeTrue();
    expect($category->is_active)->toBeTrue();
});

// ---------- CreditNote ----------

it('can create a credit note', function () {
    $creditNote = CreditNote::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'CN-001',
        'amount'     => '150.00',
        'issued_at'  => '2026-05-07',
    ]);

    expect($creditNote->exists)->toBeTrue();
    expect($creditNote->id)->toHaveLength(26);
});

it('credit note supports soft deletes', function () {
    $creditNote = CreditNote::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'CN-002',
        'amount'     => '75.00',
        'issued_at'  => '2026-05-07',
    ]);

    $creditNote->delete();
    expect($creditNote->trashed())->toBeTrue();
});

// ---------- MileageRate ----------

it('can create a mileage rate', function () {
    $rate = MileageRate::withoutGlobalScopes()->create([
        'company_id'     => $this->company->id,
        'name'           => 'Standard Rate',
        'rate_per_km'    => '0.2300',
        'currency'       => 'EUR',
        'effective_from' => '2026-01-01',
        'is_active'      => true,
    ]);

    expect($rate->exists)->toBeTrue();
    expect((float) $rate->rate_per_km)->toBe(0.23);
});

// ---------- Invoice Lines ----------

it('can create an invoice line', function () {
    $invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-LINE-001',
        'currency'   => 'EUR',
        'issue_date' => '2026-05-01',
        'due_date'   => '2026-05-31',
        'status'     => InvoiceStatus::Draft->value,
        'subtotal'   => '100.00',
        'tax_amount' => '0.00',
        'total'      => '100.00',
    ]);

    $line = InvoiceLine::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'invoice_id'  => $invoice->id,
        'description' => 'Consulting hours',
        'quantity'    => '10.00',
        'unit_price'  => '10.00',
        'tax_rate'    => '0.00',
        'subtotal'    => '100.00',
    ]);

    expect($line->exists)->toBeTrue();
    expect((float) $line->quantity)->toBe(10.0);
});
