<?php

declare(strict_types=1);

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Contracts\Finance\LedgerServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Data\Finance\RecordPaymentData;
use App\Events\Finance\InvoicePaid;
use App\Events\HR\PayrollRunApproved;
use App\Exceptions\Finance\ClosedPeriodException;
use App\Exceptions\Finance\UnbalancedEntryException;
use App\Models\Company;
use App\Models\Finance\Customer;
use App\Models\Finance\JournalEntry;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->actingAs(User::factory()->forCompany($this->company)->create(), 'web');
    $this->ledger = app(LedgerServiceInterface::class);
    $this->invoices = app(InvoiceServiceInterface::class);
});

it('rejects unbalanced journal entries', function () {
    $this->ledger->post('T-1', 'broken', now()->toDateString(), [
        ['account_code' => '1000', 'debit_cents' => 100],
        ['account_code' => '4000', 'credit_cents' => 99],
    ]);
})->throws(UnbalancedEntryException::class);

it('blocks postings into closed periods', function () {
    $this->ledger->closePeriod(now()->format('Y-m'));

    $this->ledger->post('T-2', 'late', now()->toDateString(), [
        ['account_code' => '1000', 'debit_cents' => 100],
        ['account_code' => '4000', 'credit_cents' => 100],
    ]);
})->throws(ClosedPeriodException::class);

it('reverses entries with a mirrored posting, original untouched', function () {
    $entry = $this->ledger->post('T-3', 'original', now()->toDateString(), [
        ['account_code' => '1000', 'debit_cents' => 500],
        ['account_code' => '4000', 'credit_cents' => 500],
    ]);

    $this->ledger->reverse($entry->id, 'mistake');

    expect(JournalEntry::count())->toBe(2)
        ->and($this->ledger->accountBalance('1000')->getMinorAmount()->toInt())->toBe(0)
        ->and($entry->fresh()->deleted_at)->toBeNull();
});

it('invoice lifecycle: create → send (number + AR posting) → pay (InvoicePaid + cash posting)', function () {
    Event::fake([InvoicePaid::class]);
    $customer = Customer::factory()->forCompany($this->company)->create(['payment_terms_days' => 14]);

    $invoice = $this->invoices->create(new CreateInvoiceData(
        customer_id: $customer->id,
        issue_date: '2026-06-11',
        lines: [
            ['description' => 'Consulting', 'quantity' => 10, 'unit_price_cents' => 12500],
            ['description' => 'Licence', 'quantity' => 1, 'unit_price_cents' => 50000],
        ],
    ));

    // 10 × €125 + €500 = €1,750; due = issue + 14d terms
    expect($invoice->total_cents)->toBe(175000)
        ->and($invoice->due_date->toDateString())->toBe('2026-06-25')
        ->and($invoice->invoice_number)->toBeNull();

    $invoice = $this->invoices->send($invoice->id);
    expect($invoice->invoice_number)->toBe('INV-2026-001')
        ->and((string) $invoice->status)->toBe('sent')
        ->and($this->ledger->accountBalance('1100')->getMinorAmount()->toInt())->toBe(175000); // AR

    // Partial payment
    $invoice = $this->invoices->recordPayment(new RecordPaymentData(
        invoice_id: $invoice->id, amount_cents: 75000, payment_date: '2026-06-15', payment_method: 'bank-transfer',
    ));
    expect((string) $invoice->status)->toBe('partially_paid');
    Event::assertNotDispatched(InvoicePaid::class);

    // Final payment
    $invoice = $this->invoices->recordPayment(new RecordPaymentData(
        invoice_id: $invoice->id, amount_cents: 100000, payment_date: '2026-06-20', payment_method: 'bank-transfer',
    ));
    expect((string) $invoice->status)->toBe('paid')
        ->and($this->ledger->accountBalance('1100')->getMinorAmount()->toInt())->toBe(0) // AR cleared
        ->and($this->ledger->accountBalance('1000')->getMinorAmount()->toInt())->toBe(175000); // cash

    Event::assertDispatched(InvoicePaid::class, fn ($e) => $e->amount_cents === 175000);
});

it('rejects overpayment', function () {
    $customer = Customer::factory()->forCompany($this->company)->create();
    $invoice = $this->invoices->create(new CreateInvoiceData(
        customer_id: $customer->id, issue_date: '2026-06-11',
        lines: [['description' => 'X', 'quantity' => 1, 'unit_price_cents' => 1000]],
    ));
    $this->invoices->send($invoice->id);

    $this->invoices->recordPayment(new RecordPaymentData(
        invoice_id: $invoice->id, amount_cents: 2000, payment_date: '2026-06-12', payment_method: 'cash',
    ));
})->throws(ValidationException::class);

it('payroll approval posts a balanced GL entry (cross-domain listener)', function () {
    event(new PayrollRunApproved(
        company_id: $this->company->id,
        payroll_run_id: '01TESTRUN',
        period_start: CarbonImmutable::parse('2026-06-01'),
        period_end: CarbonImmutable::parse('2026-06-30'),
        total_gross_cents: 600000,
        total_net_cents: 450000,
        currency: 'EUR',
    ));

    // Sync queue: listener posted gross 6000 = net 4500 + withholdings 1500.
    expect($this->ledger->accountBalance('6000')->getMinorAmount()->toInt())->toBe(600000)
        ->and($this->ledger->accountBalance('2100')->getMinorAmount()->toInt())->toBe(450000)
        ->and($this->ledger->accountBalance('2200')->getMinorAmount()->toInt())->toBe(150000);
});
