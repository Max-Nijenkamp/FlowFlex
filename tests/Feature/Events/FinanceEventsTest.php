<?php

use App\Enums\Finance\ExpenseStatus;
use App\Enums\Finance\InvoiceStatus;
use App\Events\Finance\CreditNoteIssued;
use App\Events\Finance\ExpenseApproved;
use App\Events\Finance\ExpenseRejected;
use App\Events\Finance\ExpenseSubmitted;
use App\Events\Finance\InvoiceCreated;
use App\Events\Finance\InvoiceOverdue;
use App\Events\Finance\InvoicePaid;
use App\Events\Finance\InvoiceSent;
use App\Listeners\Finance\LogCreditNoteIssued;
use App\Listeners\Finance\LogInvoiceCreated;
use App\Listeners\Finance\LogInvoicePaid;
use App\Listeners\Finance\LogInvoiceSent;
use App\Listeners\Finance\NotifyExpenseApproved;
use App\Listeners\Finance\NotifyExpenseRejected;
use App\Listeners\Finance\NotifyExpenseSubmitted;
use App\Listeners\Finance\NotifyInvoiceOverdue;
use App\Models\Finance\CreditNote;
use App\Models\Finance\Expense;
use App\Models\Finance\Invoice;
use Illuminate\Support\Facades\Event;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);
});

// ---------- Invoice Events ----------

it('InvoiceCreated event has correct listeners registered', function () {
    Event::fake();

    $invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-EVT-001',
        'currency'   => 'EUR',
        'issue_date' => now(),
        'due_date'   => now()->addDays(30),
        'status'     => InvoiceStatus::Draft->value,
        'subtotal'   => '500.00',
        'tax_amount' => '105.00',
        'total'      => '605.00',
    ]);

    event(new InvoiceCreated($invoice));

    Event::assertDispatched(InvoiceCreated::class);
    Event::assertListening(InvoiceCreated::class, LogInvoiceCreated::class);
});

it('InvoiceSent event has correct listeners registered', function () {
    Event::fake();

    $invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-EVT-002',
        'currency'   => 'EUR',
        'issue_date' => now(),
        'due_date'   => now()->addDays(30),
        'status'     => InvoiceStatus::Sent->value,
        'subtotal'   => '500.00',
        'tax_amount' => '105.00',
        'total'      => '605.00',
    ]);

    event(new InvoiceSent($invoice));

    Event::assertDispatched(InvoiceSent::class);
    Event::assertListening(InvoiceSent::class, LogInvoiceSent::class);
});

it('InvoicePaid event has correct listeners registered', function () {
    Event::fake();

    $invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-EVT-003',
        'currency'   => 'EUR',
        'issue_date' => now(),
        'due_date'   => now()->addDays(30),
        'status'     => InvoiceStatus::Paid->value,
        'subtotal'   => '500.00',
        'tax_amount' => '105.00',
        'total'      => '605.00',
        'paid_amount'=> '605.00',
    ]);

    event(new InvoicePaid($invoice));

    Event::assertDispatched(InvoicePaid::class);
    Event::assertListening(InvoicePaid::class, LogInvoicePaid::class);
});

it('InvoiceOverdue event has correct listeners registered', function () {
    Event::fake();

    $invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-EVT-004',
        'currency'   => 'EUR',
        'issue_date' => now()->subDays(60),
        'due_date'   => now()->subDays(30),
        'status'     => InvoiceStatus::Overdue->value,
        'subtotal'   => '500.00',
        'tax_amount' => '105.00',
        'total'      => '605.00',
    ]);

    event(new InvoiceOverdue($invoice));

    Event::assertDispatched(InvoiceOverdue::class);
    Event::assertListening(InvoiceOverdue::class, NotifyInvoiceOverdue::class);
});

// ---------- Expense Events ----------

it('ExpenseSubmitted event has correct listeners registered', function () {
    Event::fake();

    $expense = Expense::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'tenant_id'    => $this->tenant->id,
        'description'  => 'Travel to client',
        'amount'       => '150.00',
        'currency'     => 'EUR',
        'expense_date' => now(),
        'status'       => ExpenseStatus::Pending->value,
    ]);

    event(new ExpenseSubmitted($expense));

    Event::assertDispatched(ExpenseSubmitted::class);
    Event::assertListening(ExpenseSubmitted::class, NotifyExpenseSubmitted::class);
});

it('ExpenseApproved event has correct listeners registered', function () {
    Event::fake();

    $expense = Expense::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'tenant_id'    => $this->tenant->id,
        'description'  => 'Hotel stay',
        'amount'       => '250.00',
        'currency'     => 'EUR',
        'expense_date' => now(),
        'status'       => ExpenseStatus::Approved->value,
    ]);

    event(new ExpenseApproved($expense));

    Event::assertDispatched(ExpenseApproved::class);
    Event::assertListening(ExpenseApproved::class, NotifyExpenseApproved::class);
});

it('ExpenseRejected event has correct listeners registered', function () {
    Event::fake();

    $expense = Expense::withoutGlobalScopes()->create([
        'company_id'   => $this->company->id,
        'tenant_id'    => $this->tenant->id,
        'description'  => 'Rejected item',
        'amount'       => '500.00',
        'currency'     => 'EUR',
        'expense_date' => now(),
        'status'       => ExpenseStatus::Rejected->value,
    ]);

    event(new ExpenseRejected($expense));

    Event::assertDispatched(ExpenseRejected::class);
    Event::assertListening(ExpenseRejected::class, NotifyExpenseRejected::class);
});

// ---------- CreditNote Events ----------

it('CreditNoteIssued event has correct listeners registered', function () {
    Event::fake();

    $invoice = Invoice::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'number'     => 'INV-EVT-CN-001',
        'currency'   => 'EUR',
        'issue_date' => now(),
        'due_date'   => now()->addDays(30),
        'status'     => InvoiceStatus::Paid->value,
        'subtotal'   => '500.00',
        'tax_amount' => '105.00',
        'total'      => '605.00',
    ]);

    $creditNote = CreditNote::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'invoice_id' => $invoice->id,
        'number'     => 'CN-001',
        'issued_at'  => now(),
        'amount'     => '605.00',
        'reason'     => 'Faulty goods',
    ]);

    event(new CreditNoteIssued($creditNote));

    Event::assertDispatched(CreditNoteIssued::class);
    Event::assertListening(CreditNoteIssued::class, LogCreditNoteIssued::class);
});

// ---------- Listener implements ShouldQueue ----------

it('all Finance listeners implement ShouldQueue', function () {
    $listeners = [
        LogInvoiceCreated::class,
        LogInvoiceSent::class,
        LogInvoicePaid::class,
        NotifyInvoiceOverdue::class,
        NotifyExpenseSubmitted::class,
        NotifyExpenseApproved::class,
        NotifyExpenseRejected::class,
        LogCreditNoteIssued::class,
    ];

    foreach ($listeners as $listener) {
        expect(is_a($listener, \Illuminate\Contracts\Queue\ShouldQueue::class, true))
            ->toBeTrue("$listener does not implement ShouldQueue");
    }
});
