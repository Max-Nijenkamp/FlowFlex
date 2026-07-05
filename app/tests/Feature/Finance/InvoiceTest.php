<?php

declare(strict_types=1);

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Data\Finance\RecordPaymentData;
use App\Events\Crm\DealWon;
use App\Events\Finance\InvoicePaid;
use App\Exceptions\Finance\CannotVoidPaidInvoiceException;
use App\Listeners\Finance\CreateInvoiceStubListener;
use App\Mail\Finance\CustomerInvoiceMail;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Finance\Customer;
use App\Models\Finance\Invoice;
use App\Models\Finance\JournalEntry;
use App\Models\User;
use App\Services\Finance\LedgerService;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

function invoiceCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create(['currency' => 'EUR']));
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    LedgerService::ensureDefaultChartOfAccounts($company->id);

    $customer = Customer::factory()->create(['company_id' => $company->id]);

    return [$company, $owner, $customer];
}

test('totals compute per line with 21% tax and per-line rounding', function () {
    [, , $customer] = invoiceCompany();

    $invoice = app(InvoiceServiceInterface::class)->create(new CreateInvoiceData(
        customerId: $customer->id,
        lines: [
            ['description' => 'Consulting', 'quantity' => 2, 'unit_price_cents' => 7550], // 151.00 + 31.71
            ['description' => 'Licence', 'quantity' => 1, 'unit_price_cents' => 999],     // 9.99 + 2.10
        ],
    ));

    expect($invoice->subtotal_cents)->toBe(15100 + 999)
        ->and($invoice->tax_total_cents)->toBe(3171 + 210)
        ->and($invoice->total_cents)->toBe(16099 + 3381);
});

test('send assigns a sequential number, posts revenue, and mails the PDF', function () {
    Mail::fake();
    [, , $customer] = invoiceCompany();
    $service = app(InvoiceServiceInterface::class);

    $first = $service->create(new CreateInvoiceData(customerId: $customer->id, lines: [
        ['description' => 'A', 'quantity' => 1, 'unit_price_cents' => 10000],
    ]));
    $second = $service->create(new CreateInvoiceData(customerId: $customer->id, lines: [
        ['description' => 'B', 'quantity' => 1, 'unit_price_cents' => 5000],
    ]));

    $first = $service->send($first->id);
    $second = $service->send($second->id);

    $year = now()->format('Y');
    expect($first->invoice_number)->toBe("INV-{$year}-0001")
        ->and($second->invoice_number)->toBe("INV-{$year}-0002")
        ->and((string) $first->status)->toBe('sent');

    // Revenue recognised: AR debit equals the invoice total.
    $entry = JournalEntry::query()->where('source_type', 'invoice')->where('source_id', $first->id)->firstOrFail();
    expect((int) $entry->lines()->sum('debit_cents'))->toBe($first->total_cents);

    Mail::assertQueued(CustomerInvoiceMail::class, 2);
});

test('partial payment part-pays; completing fires InvoicePaid with contract payload and posts the journal', function () {
    Mail::fake();
    Event::fake([InvoicePaid::class]);
    [, , $customer] = invoiceCompany();
    $service = app(InvoiceServiceInterface::class);

    $invoice = $service->create(new CreateInvoiceData(customerId: $customer->id, lines: [
        ['description' => 'Retainer', 'quantity' => 1, 'unit_price_cents' => 10000],
    ]));
    $invoice = $service->send($invoice->id);

    $invoice = $service->recordPayment(new RecordPaymentData(invoiceId: $invoice->id, amountCents: 5000));
    expect((string) $invoice->status)->toBe('partially_paid');
    Event::assertNotDispatched(InvoicePaid::class);

    $invoice = $service->recordPayment(new RecordPaymentData(invoiceId: $invoice->id, amountCents: $invoice->openBalanceCents()));
    expect((string) $invoice->status)->toBe('paid');

    Event::assertDispatched(InvoicePaid::class, fn (InvoicePaid $event): bool => $event->invoice_id === $invoice->id
        && $event->total_cents === $invoice->total_cents
        && $event->currency === 'EUR');

    expect(JournalEntry::query()->where('source_type', 'invoice-payment')->count())->toBe(2);
});

test('overpayment is rejected', function () {
    Mail::fake();
    [, , $customer] = invoiceCompany();
    $service = app(InvoiceServiceInterface::class);

    $invoice = $service->create(new CreateInvoiceData(customerId: $customer->id, lines: [
        ['description' => 'Small', 'quantity' => 1, 'unit_price_cents' => 1000],
    ]));
    $invoice = $service->send($invoice->id);

    expect(fn () => $service->recordPayment(new RecordPaymentData(
        invoiceId: $invoice->id,
        amountCents: $invoice->total_cents + 1,
    )))->toThrow(ValidationException::class);
});

test('voiding a paid invoice is rejected; voiding a sent one posts a reversal', function () {
    Mail::fake();
    [, , $customer] = invoiceCompany();
    $service = app(InvoiceServiceInterface::class);

    $paid = $service->create(new CreateInvoiceData(customerId: $customer->id, lines: [
        ['description' => 'Paid', 'quantity' => 1, 'unit_price_cents' => 1000],
    ]));
    $paid = $service->send($paid->id);
    $paid = $service->recordPayment(new RecordPaymentData(invoiceId: $paid->id, amountCents: $paid->total_cents));

    expect(fn () => $service->void($paid->id, 'nope'))->toThrow(CannotVoidPaidInvoiceException::class);

    $sent = $service->create(new CreateInvoiceData(customerId: $customer->id, lines: [
        ['description' => 'Sent', 'quantity' => 1, 'unit_price_cents' => 2000],
    ]));
    $sent = $service->send($sent->id);
    $sent = $service->void($sent->id, 'Ordered by mistake');

    expect((string) $sent->status)->toBe('voided')
        ->and(JournalEntry::query()->where('source_type', 'reversal')->exists())->toBeTrue();
});

test('the recurring command is idempotent and advances the schedule', function () {
    Mail::fake();
    [, , $customer] = invoiceCompany();
    $service = app(InvoiceServiceInterface::class);

    $template = $service->create(new CreateInvoiceData(
        customerId: $customer->id,
        lines: [['description' => 'Hosting', 'quantity' => 1, 'unit_price_cents' => 2500]],
        recurringSchedule: 'monthly',
    ));
    // Due today
    $template->update(['next_recurring_at' => now()->toDateString()]);

    $this->artisan('finance:generate-recurring-invoices')->assertSuccessful();
    $this->artisan('finance:generate-recurring-invoices')->assertSuccessful();

    setPermissionsTeamId($template->company_id);
    $copies = Invoice::query()->where('id', '!=', $template->id)->count();

    expect($copies)->toBe(1)
        ->and($template->fresh()->next_recurring_at->isFuture())->toBeTrue();
});

test('the overdue command only touches sent invoices past due', function () {
    Mail::fake();
    [, , $customer] = invoiceCompany();
    $service = app(InvoiceServiceInterface::class);

    $pastDue = $service->create(new CreateInvoiceData(
        customerId: $customer->id,
        lines: [['description' => 'Old', 'quantity' => 1, 'unit_price_cents' => 1000]],
        dueDate: now()->subDays(3)->toDateString(),
    ));
    $pastDue = $service->send($pastDue->id);

    $draft = $service->create(new CreateInvoiceData(
        customerId: $customer->id,
        lines: [['description' => 'Draft', 'quantity' => 1, 'unit_price_cents' => 1000]],
        dueDate: now()->subDays(3)->toDateString(),
    ));

    $this->artisan('finance:mark-overdue-invoices')->assertSuccessful();

    expect((string) $pastDue->fresh()->status)->toBe('overdue')
        ->and((string) $draft->fresh()->status)->toBe('draft');
});

test('DealWon creates a draft invoice stub with copied lines, never sent', function () {
    Mail::fake();
    [$company] = invoiceCompany();

    // The listener no-ops unless finance.invoicing is active for the company.
    CompanyModuleSubscription::query()->firstOrCreate(
        ['company_id' => $company->id, 'module_key' => 'finance.invoicing', 'deactivated_at' => null],
        ['activated_at' => now()],
    );
    Cache::forget("company:{$company->id}:modules");

    $listener = new CreateInvoiceStubListener;
    $listener->handle(new DealWon(
        company_id: $company->id,
        deal_id: '01JZZZZZZZZZZZZZZZZZZZZZZZ',
        account_id: null,
        contact_id: null,
        value_cents: 250_000,
        currency: 'EUR',
        name: 'Big rollout',
    ));

    $stub = Invoice::query()->where('source_deal_id', '01JZZZZZZZZZZZZZZZZZZZZZZZ')->firstOrFail();

    expect((string) $stub->status)->toBe('draft')
        ->and($stub->invoice_number)->toBeNull()
        ->and($stub->lines()->count())->toBe(1)
        ->and($stub->total_cents)->toBeGreaterThan(0);
    Mail::assertNothingQueued();
});
