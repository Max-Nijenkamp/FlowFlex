<?php

declare(strict_types=1);

use App\Contracts\Finance\BankServiceInterface;
use App\Contracts\Finance\InvoiceServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Exceptions\Finance\AmountMismatchException;
use App\Models\Company;
use App\Models\Finance\BankAccount;
use App\Models\Finance\BankTransaction;
use App\Models\Finance\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->actingAs(User::factory()->forCompany($this->company)->create(), 'web');
    $this->bank = app(BankServiceInterface::class);
    $this->account = BankAccount::factory()->forCompany($this->company)->create();
});

it('imports statements, dedupes on re-import, reports bad rows', function () {
    $csv = "2026-06-01,Client payment,175000\n2026-06-02,Office rent,-120000\nnot,a,row,extra\n";

    $first = $this->bank->import($this->account->id, $csv);
    expect($first['imported'])->toBe(2)
        ->and($first['errors'])->toHaveCount(1);

    $second = $this->bank->import($this->account->id, $csv);
    expect($second['imported'])->toBe(0)
        ->and($second['skipped'])->toBe(2)
        ->and(BankTransaction::count())->toBe(2)
        ->and($this->account->fresh()->current_balance_cents)->toBe(55000);
});

it('suggests and reconciles a matching invoice', function () {
    $customer = Customer::factory()->forCompany($this->company)->create();
    $invoices = app(InvoiceServiceInterface::class);
    $invoice = $invoices->create(new CreateInvoiceData(
        customer_id: $customer->id,
        issue_date: now()->toDateString(),
        lines: [['description' => 'Work', 'quantity' => 1, 'unit_price_cents' => 50000]],
        due_date: now()->addDays(2)->toDateString(),
    ));
    $invoices->send($invoice->id);

    $this->bank->import($this->account->id, now()->toDateString().",Incoming,50000\n");
    $transaction = BankTransaction::query()->firstOrFail();

    expect($this->bank->suggestMatches($transaction->id)->pluck('id'))->toContain($invoice->id);

    $reconciled = $this->bank->reconcile($transaction->id, $invoice->id);
    expect($reconciled->reconciled_at)->not->toBeNull();
});

it('rejects reconciliation on amount mismatch', function () {
    $customer = Customer::factory()->forCompany($this->company)->create();
    $invoices = app(InvoiceServiceInterface::class);
    $invoice = $invoices->create(new CreateInvoiceData(
        customer_id: $customer->id, issue_date: now()->toDateString(),
        lines: [['description' => 'Work', 'quantity' => 1, 'unit_price_cents' => 99999]],
    ));
    $invoices->send($invoice->id);

    $this->bank->import($this->account->id, now()->toDateString().",Wrong,12345\n");
    $transaction = BankTransaction::query()->firstOrFail();

    $this->bank->reconcile($transaction->id, $invoice->id);
})->throws(AmountMismatchException::class);
