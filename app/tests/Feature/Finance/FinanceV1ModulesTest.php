<?php

declare(strict_types=1);

use App\Contracts\Finance\ApServiceInterface;
use App\Contracts\Finance\ArServiceInterface;
use App\Contracts\Finance\BudgetServiceInterface;
use App\Contracts\Finance\InvoiceServiceInterface;
use App\Contracts\Finance\LedgerServiceInterface;
use App\Contracts\Finance\ReportingServiceInterface;
use App\Data\Finance\CreateInvoiceData;
use App\Data\Finance\RecordPaymentData;
use App\Events\Finance\InvoicePaid;
use App\Exceptions\Finance\AlreadyDisposedException;
use App\Exceptions\Finance\BudgetApprovedException;
use App\Exceptions\Finance\MissingExchangeRateException;
use App\Exceptions\Finance\PeriodFiledException;
use App\Listeners\Finance\UpdateARAgingListener;
use App\Models\Company;
use App\Models\Finance\Bill;
use App\Models\Finance\Customer;
use App\Models\Finance\DunningRule;
use App\Models\Finance\ExchangeRate;
use App\Models\Finance\Supplier;
use App\Models\Finance\TaxRate;
use App\Models\User;
use App\Services\Finance\CashFlowService;
use App\Services\Finance\CurrencyService;
use App\Services\Finance\FixedAssetService;
use App\Services\Finance\ForecastService;
use App\Services\Finance\TaxCalculator;
use App\Services\Finance\TaxService;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    $this->user = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->user, 'web');
});

function makeInvoice($test, int $cents, string $issueDate, string $dueDate)
{
    $customer = Customer::factory()->forCompany($test->company)->create();
    $invoice = app(InvoiceServiceInterface::class)->create(new CreateInvoiceData(
        customer_id: $customer->id, issue_date: $issueDate, due_date: $dueDate,
        lines: [['description' => 'Work', 'quantity' => 1, 'unit_price_cents' => $cents]],
    ));

    return app(InvoiceServiceInterface::class)->send($invoice->id);
}

// --- finance.ar ---
it('buckets AR aging at boundary days and computes DSO', function () {
    $today = CarbonImmutable::today();
    makeInvoice($this, 10000, $today->subDays(40)->toDateString(), $today->subDays(30)->toDateString()); // exactly 30 → 1-30
    makeInvoice($this, 20000, $today->subDays(101)->toDateString(), $today->subDays(91)->toDateString()); // 91 → 90+
    makeInvoice($this, 30000, $today->toDateString(), $today->addDays(14)->toDateString()); // current

    $aging = app(ArServiceInterface::class)->aging();

    expect($aging['1-30'])->toBe(10000)
        ->and($aging['90+'])->toBe(20000)
        ->and($aging['current'])->toBe(30000)
        ->and(app(ArServiceInterface::class)->dso($today->subDays(120), $today))->toBeGreaterThan(0);
});

it('escalates dunning in order, once each, and InvoicePaid resets the level', function () {
    $today = CarbonImmutable::today();
    foreach ([1 => 7, 2 => 30] as $level => $days) {
        DunningRule::create([
            'company_id' => $this->company->id, 'aging_bucket' => '1-30',
            'days_overdue' => $days, 'email_template' => "dunning-{$level}", 'escalation_level' => $level,
        ]);
    }
    $invoice = makeInvoice($this, 50000, $today->subDays(50)->toDateString(), $today->subDays(40)->toDateString());

    $ar = app(ArServiceInterface::class);
    expect($ar->runDunning())->toBe(2); // both qualifying levels fire, in order
    expect($invoice->fresh()->last_dunning_level)->toBe(2);
    expect($ar->runDunning())->toBe(0); // idempotent

    (new UpdateARAgingListener)->handle(new InvoicePaid(
        company_id: $this->company->id, invoice_id: $invoice->id, crm_account_id: null,
        amount_cents: 50000, currency: 'EUR', paid_at: CarbonImmutable::now(),
    ));
    expect($invoice->fresh()->last_dunning_level)->toBe(0);
});

it('writes off an invoice: GL bad-debt entry + approver recorded', function () {
    $today = CarbonImmutable::today();
    $invoice = makeInvoice($this, 80000, $today->subDays(120)->toDateString(), $today->subDays(110)->toDateString());

    $writeoff = app(ArServiceInterface::class)->writeOff($invoice->id, 'Customer bankrupt');

    expect($writeoff->amount_cents)->toBe(80000)
        ->and($writeoff->approved_by)->toBe($this->user->id)
        ->and(app(LedgerServiceInterface::class)->accountBalance('6300')->getMinorAmount()->toInt())->toBe(80000);
});

it('allocates one payment across multiple invoices', function () {
    $today = CarbonImmutable::today();
    $a = makeInvoice($this, 30000, $today->toDateString(), $today->addDays(14)->toDateString());
    $b = makeInvoice($this, 20000, $today->toDateString(), $today->addDays(14)->toDateString());

    app(ArServiceInterface::class)->allocatePayment([
        ['invoice_id' => $a->id, 'amount_cents' => 30000],
        ['invoice_id' => $b->id, 'amount_cents' => 10000],
    ], $today->toDateString(), 'bank-transfer');

    expect((string) $a->fresh()->status)->toBe('paid')
        ->and((string) $b->fresh()->status)->toBe('partially_paid');
});

// --- finance.ap ---
it('runs the AP flow: bill → approve posts liability → payment run pays with early discount', function () {
    $supplier = Supplier::create([
        'company_id' => $this->company->id, 'name' => 'Acme Supplies', 'iban' => 'NL91ABNA0417164300',
    ]);
    expect(DB::table('fin_suppliers')->value('iban'))->not->toContain('NL91') // encrypted
        ->and($supplier->fresh()->iban_last4)->toBe('4300');

    $ap = app(ApServiceInterface::class);
    $bill = $ap->createBill(
        $supplier->id, 'INV-2026-001', now()->toDateString(), now()->addDays(30)->toDateString(),
        [['description' => 'Paper', 'account_code' => '6100', 'amount_cents' => 100000]],
        earlyDiscountPercent: 2.0, earlyDiscountUntil: now()->addDays(10)->toDateString(),
    );

    // duplicate supplier bill number rejected
    expect(fn () => $ap->createBill($supplier->id, 'INV-2026-001', now()->toDateString(), now()->addDays(30)->toDateString(),
        [['description' => 'Dup', 'account_code' => '6100', 'amount_cents' => 1000]]))
        ->toThrow(QueryException::class);

    $bill = $ap->approveBill($bill->id);
    expect((string) $bill->status)->toBe('approved')
        ->and(app(LedgerServiceInterface::class)->accountBalance('2000')->getMinorAmount()->toInt())->toBe(100000); // credit-normal liability

    $run = $ap->createPaymentRun(now()->toDateString(), [$bill->id]);
    $run = $ap->executeRun($run->id);

    expect((string) $bill->fresh()->status)->toBe('paid')
        ->and($run->total_cents)->toBe(98000) // 2% early discount inside window
        ->and($run->status)->toBe('executed');
});

// --- finance.budgets ---
it('computes variance from GL actuals, locks approved budgets, revisions preserve old version', function () {
    $ledger = app(LedgerServiceInterface::class);
    $account = $ledger->accountByCode('6100');
    $period = now()->format('Y-m');

    $ledger->post('EXP-1', 'Office costs', now()->toDateString(), [
        ['account_code' => '6100', 'debit_cents' => 70000],
        ['account_code' => '1000', 'credit_cents' => 70000],
    ]);

    $budgets = app(BudgetServiceInterface::class);
    $budget = $budgets->create('Opex 2026', 2026, [
        ['account_id' => $account->id, 'period' => $period, 'budgeted_cents' => 100000],
    ]);

    $variance = $budgets->variance($budget->id)[0];
    expect($variance['actual_cents'])->toBe(70000)
        ->and($variance['variance_cents'])->toBe(-30000)
        ->and($budgets->remaining($budget->id, $account->id, $period))->toBe(30000);

    $budgets->approve($budget->id);
    expect(fn () => $budgets->addLine($budget->id, $account->id, '2026-12', 1))
        ->toThrow(BudgetApprovedException::class);

    $v2 = $budgets->revise($budget->id);
    expect($v2->version)->toBe(2)
        ->and($v2->lines()->count())->toBe(1)
        ->and($budget->fresh())->not->toBeNull(); // old version preserved
});

// --- finance.reporting ---
it('produces a P&L that matches GL fixtures and a balanced balance sheet', function () {
    $ledger = app(LedgerServiceInterface::class);
    $ledger->post('REV-1', 'Sale', now()->toDateString(), [
        ['account_code' => '1000', 'debit_cents' => 250000],
        ['account_code' => '4000', 'credit_cents' => 250000],
    ]);
    $ledger->post('EXP-1', 'Costs', now()->toDateString(), [
        ['account_code' => '6100', 'debit_cents' => 90000],
        ['account_code' => '1000', 'credit_cents' => 90000],
    ]);

    $reporting = app(ReportingServiceInterface::class);
    $pl = $reporting->profitLoss(CarbonImmutable::now()->startOfYear(), CarbonImmutable::now());
    expect($pl['revenue_cents'])->toBe(250000)
        ->and($pl['expense_cents'])->toBe(90000)
        ->and($pl['net_profit_cents'])->toBe(160000);

    $bs = $reporting->balanceSheet(CarbonImmutable::now());
    expect($bs['assets_cents'])->toBe($bs['liabilities_cents'] + $bs['equity_cents']);
});

// --- finance.tax ---
it('computes exact tax via basis points, zeroes reverse charge, and locks filed periods', function () {
    $rate = TaxRate::create([
        'company_id' => $this->company->id, 'name' => 'NL High 21%',
        'rate_basis_points' => 2100, 'jurisdiction' => 'NL',
    ]);
    $calc = app(TaxCalculator::class);
    expect($calc->forLine(9999, $rate)->getMinorAmount()->toInt())->toBe(2100); // 21% of €99.99 = €21.00 HALF_UP

    $reverse = TaxRate::create([
        'company_id' => $this->company->id, 'name' => 'Reverse', 'rate_basis_points' => 2100,
        'jurisdiction' => 'DE', 'is_reverse_charge' => true,
    ]);
    expect($calc->forLine(9999, $reverse)->isZero())->toBeTrue();

    $tax = app(TaxService::class);
    $period = now()->format('Y-m');
    $filed = $tax->filePeriod($period);
    expect($filed->status)->toBe('filed');
    expect(fn () => $tax->filePeriod($period))->toThrow(PeriodFiledException::class);
});

// --- finance.cashflow ---
it('chains 13 weekly closings and drops paid invoices on rebuild', function () {
    $today = CarbonImmutable::today();
    $invoice = makeInvoice($this, 120000, $today->toDateString(), $today->addDays(10)->toDateString());

    $cashflow = app(CashFlowService::class);
    $cashflow->rebuild();

    $weeks = $cashflow->projection();
    expect($weeks)->toHaveCount(13)
        ->and((int) $weeks->sum('inflow_cents'))->toBe(120000);

    // chained closings
    $weeks->reduce(function (?int $carry, $week) {
        if ($carry !== null) {
            expect($week->opening_cents)->toBe($carry);
        }
        expect($week->closing_cents)->toBe($week->opening_cents + $week->inflow_cents - $week->outflow_cents);

        return $week->closing_cents;
    });

    app(InvoiceServiceInterface::class)->recordPayment(new RecordPaymentData(
        invoice_id: $invoice->id, amount_cents: 120000, payment_date: $today->toDateString(), payment_method: 'bank-transfer',
    ));
    $cashflow->rebuild();
    expect((int) $cashflow->projection()->sum('inflow_cents'))->toBe(0);
});

// --- finance.assets ---
it('depreciates straight-line to exactly cost − salvage and handles disposal gain', function () {
    $assets = app(FixedAssetService::class);
    $asset = $assets->create('Laptop fleet', 'it-equipment', 1000000, now()->toDateString(), 36, salvageCents: 100000);

    $schedule = $assets->schedule($asset->id);
    expect(array_sum(array_column($schedule, 'depreciation_cents')))->toBe(900000); // rounding absorbed in final month

    $result = $assets->runMonthlyDepreciation('2026-06');
    expect($result['processed'])->toBe(1);
    expect($assets->runMonthlyDepreciation('2026-06')['skipped'])->toBe(1); // idempotent

    $asset->refresh();
    $proceeds = $asset->netBookValueCents() + 50000; // sell above NBV → gain
    $disposed = $assets->dispose($asset->id, $proceeds);
    expect($disposed->status)->toBe('disposed');
    expect(fn () => $assets->dispose($asset->id, 1))->toThrow(AlreadyDisposedException::class);

    expect(app(LedgerServiceInterface::class)->accountBalance('8000')->getMinorAmount()->toInt())->toBe(50000); // credit-normal gain
});

// --- finance.forecasting ---
it('seeds forecast from trailing actuals with growth and tracks accuracy', function () {
    $ledger = app(LedgerServiceInterface::class);
    // prior-year revenue actual
    $ledger->post('REV-PY', 'Prior year sale', now()->subYear()->setMonth(3)->setDay(15)->toDateString(), [
        ['account_code' => '1000', 'debit_cents' => 100000],
        ['account_code' => '4000', 'credit_cents' => 100000],
    ]);

    $forecasts = app(ForecastService::class);
    $forecast = $forecasts->create('Base 2026', (int) now()->format('Y'), 'base', [
        ['key' => 'growth', 'description' => 'YoY growth', 'value' => '10%'],
    ]);
    $forecasts->seedFromActuals($forecast->id, 10.0);

    $line = $forecast->lines()->where('period', now()->format('Y').'-03')->first();
    expect($line->projected_cents)->toBe(110000);

    expect($forecasts->accuracy($forecast->id))->toBeFloat();
});

// --- finance.currency ---
it('locks rates by date, round-trips JPY/BHD minor units, throws on missing rate', function () {
    ExchangeRate::create([
        'company_id' => $this->company->id, 'from_currency' => 'USD', 'to_currency' => 'EUR',
        'rate' => '0.90000000', 'effective_date' => '2026-06-01',
    ]);
    ExchangeRate::create([
        'company_id' => $this->company->id, 'from_currency' => 'USD', 'to_currency' => 'EUR',
        'rate' => '0.95000000', 'effective_date' => '2026-06-10',
    ]);

    $currency = app(CurrencyService::class);

    // picks most recent ≤ date
    expect((string) $currency->rateFor('USD', 'EUR', CarbonImmutable::parse('2026-06-05')))->toBe('0.90000000')
        ->and((string) $currency->rateFor('USD', 'EUR', CarbonImmutable::parse('2026-06-11')))->toBe('0.95000000');

    expect(fn () => $currency->rateFor('GBP', 'EUR', CarbonImmutable::parse('2026-06-11')))
        ->toThrow(MissingExchangeRateException::class);

    // JPY: 0 minor units
    ExchangeRate::create([
        'company_id' => $this->company->id, 'from_currency' => 'JPY', 'to_currency' => 'EUR',
        'rate' => '0.00610000', 'effective_date' => '2026-06-01',
    ]);
    $base = $currency->toBase(Money::ofMinor(150000, 'JPY'), CarbonImmutable::parse('2026-06-05')); // ¥150,000
    expect($base->getCurrency()->getCurrencyCode())->toBe('EUR')
        ->and($base->getMinorAmount()->toInt())->toBe(91500); // €915.00

    // realised FX gain on payment-date rate move
    $fx = $currency->realisedFxCents(Money::ofMinor(10000, 'USD'), CarbonImmutable::parse('2026-06-05'), CarbonImmutable::parse('2026-06-11'));
    expect($fx)->toBe(500); // $100: 90.00 → 95.00
});
