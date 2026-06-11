<?php

declare(strict_types=1);

use App\Contracts\BillingServiceInterface;
use App\Models\BillingInvoice;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('flowflex.modules', [
        'hr.payroll' => ['name' => 'Payroll', 'domain' => 'hr', 'per_user_monthly_price_cents' => 150],
        'crm.deals' => ['name' => 'Deals', 'domain' => 'crm', 'per_user_monthly_price_cents' => 100],
    ]);

    $this->billing = app(BillingServiceInterface::class);
    $this->company = Company::factory()->create();
    User::factory()->forCompany($this->company)->count(15)->create();

    CompanyModuleSubscription::factory()->forCompany($this->company)->module('hr.payroll')->create();
    CompanyModuleSubscription::factory()->forCompany($this->company)->module('crm.deals')->create();
    CompanyModuleSubscription::factory()->forCompany($this->company)->module('core.settings')->create(); // free — no line
});

it('computes the invoice to the cent: users × module price, free modules excluded', function () {
    $invoice = $this->billing->generateMonthlyInvoice($this->company->id, CarbonImmutable::parse('2026-05-01'));

    // 15 users × (€1.50 + €1.00) = €37.50
    expect($invoice->total_cents)->toBe(3750)
        ->and($invoice->lines)->toHaveCount(2)
        ->and(collect($invoice->lines)->firstWhere('module_key', 'hr.payroll')['line_total_cents'])->toBe(2250)
        ->and($invoice->status)->toBe('open');
});

it('is idempotent per company and period', function () {
    $first = $this->billing->generateMonthlyInvoice($this->company->id, CarbonImmutable::parse('2026-05-01'));
    $second = $this->billing->generateMonthlyInvoice($this->company->id, CarbonImmutable::parse('2026-05-01'));

    expect($second->id)->toBe($first->id);

    $this->setCompany($this->company);
    expect(BillingInvoice::count())->toBe(1);
});

it('keeps invoices isolated per company', function () {
    $this->billing->generateMonthlyInvoice($this->company->id, CarbonImmutable::parse('2026-05-01'));

    $other = Company::factory()->create();
    $this->setCompany($other);

    expect(BillingInvoice::count())->toBe(0);
});

it('runs the monthly command idempotently across all active companies', function () {
    $this->artisan('flowflex:generate-monthly-invoices', ['--period' => '2026-05'])->assertSuccessful();
    $this->artisan('flowflex:generate-monthly-invoices', ['--period' => '2026-05'])->assertSuccessful();

    $this->setCompany($this->company);
    expect(BillingInvoice::count())->toBe(1);
});
