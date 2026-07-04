<?php

declare(strict_types=1);

use App\Console\Commands\GenerateMonthlyInvoicesCommand;
use App\Console\Commands\ProcessDunningCommand;
use App\Contracts\BillingServiceInterface;
use App\Events\CompanySubscriptionSuspended;
use App\Events\ModuleActivated;
use App\Events\ModuleDeactivated;
use App\Mail\InvoiceMail;
use App\Models\BillingInvoice;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalogEntry;
use App\Models\User;
use App\States\Core\BillingInvoice\Open;
use App\States\Core\BillingInvoice\Paid;
use App\States\Core\BillingInvoice\PastDue;
use App\States\Core\BillingInvoice\Uncollectible;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\ModelStates\Exceptions\TransitionNotFound;

function billingCompany(int $users = 3): Company
{
    $company = setCompany(Company::factory()->create(['currency' => 'EUR']));
    User::factory()->count($users)->for($company)->create();

    ModuleCatalogEntry::query()->updateOrCreate(['module_key' => 'hr.leave'], ['domain' => 'hr', 'name' => 'Leave & absence', 'per_user_monthly_price' => 300, 'is_active' => true]);
    ModuleCatalogEntry::query()->updateOrCreate(['module_key' => 'crm.deals'], ['domain' => 'crm', 'name' => 'Deals', 'per_user_monthly_price' => 400, 'is_active' => true]);
    ModuleCatalogEntry::query()->updateOrCreate(['module_key' => 'core.audit'], ['domain' => 'core', 'name' => 'Audit log', 'per_user_monthly_price' => 0, 'is_active' => true]);

    foreach (['hr.leave', 'crm.deals', 'core.audit'] as $key) {
        CompanyModuleSubscription::query()->create([
            'company_id' => $company->id, 'module_key' => $key, 'activated_at' => now(),
        ]);
    }
    Cache::forget("company:{$company->id}:modules");

    return $company;
}

test('monthly invoice snapshots lines and totals with integer money math', function () {
    Mail::fake();
    $company = billingCompany(users: 3);

    $invoice = app(BillingServiceInterface::class)
        ->generateMonthlyInvoice($company->id, CarbonImmutable::parse('2026-06-15'));

    expect($invoice)->not->toBeNull()
        ->and((string) $invoice->status)->toBe('open')
        ->and($invoice->lines)->toHaveCount(2) // free module never billed
        ->and($invoice->total_cents)->toBe((300 + 400) * 3)
        ->and($invoice->lines->firstWhere('module_key', 'hr.leave')->line_total_cents)->toBe(900)
        ->and($invoice->period_start->toDateString())->toBe('2026-06-01');

    Mail::assertQueued(InvoiceMail::class);
});

test('invoice generation is idempotent per company and period', function () {
    Mail::fake();
    $company = billingCompany();

    $billing = app(BillingServiceInterface::class);
    $first = $billing->generateMonthlyInvoice($company->id, CarbonImmutable::parse('2026-06-15'));
    $second = $billing->generateMonthlyInvoice($company->id, CarbonImmutable::parse('2026-06-20'));

    expect($first)->not->toBeNull()
        ->and($second)->toBeNull()
        ->and(BillingInvoice::query()->count())->toBe(1);
});

test('the generate command sweeps all companies and re-runs skip existing', function () {
    Mail::fake();
    billingCompany();

    $this->artisan(GenerateMonthlyInvoicesCommand::class, ['--period' => '2026-06'])->assertSuccessful();
    $this->artisan(GenerateMonthlyInvoicesCommand::class, ['--period' => '2026-06'])->assertSuccessful();

    expect(BillingInvoice::query()->withoutGlobalScopes()->count())->toBe(1);
});

test('webhooks drive open to paid and open to past_due, idempotently', function () {
    Mail::fake();
    $company = billingCompany();

    $billing = app(BillingServiceInterface::class);
    $invoice = $billing->generateMonthlyInvoice($company->id, CarbonImmutable::parse('2026-06-15'));
    $invoice->update(['stripe_invoice_id' => 'in_test_123']);

    $billing->handleStripeWebhook(['type' => 'invoice.payment_succeeded', 'data' => ['object' => ['id' => 'in_test_123']]]);
    $billing->handleStripeWebhook(['type' => 'invoice.payment_succeeded', 'data' => ['object' => ['id' => 'in_test_123']]]);

    $fresh = $invoice->fresh();
    expect($fresh->status->equals(Paid::class))->toBeTrue()
        ->and($fresh->paid_at)->not->toBeNull();

    // a second invoice goes past due
    $other = billingCompany();
    $invoice2 = $billing->generateMonthlyInvoice($other->id, CarbonImmutable::parse('2026-06-15'));
    $invoice2->update(['stripe_invoice_id' => 'in_test_456']);

    $billing->handleStripeWebhook(['type' => 'invoice.payment_failed', 'data' => ['object' => ['id' => 'in_test_456']]]);

    $fresh2 = $invoice2->fresh();
    expect($fresh2->status->equals(PastDue::class))->toBeTrue()
        ->and($fresh2->next_retry_at)->not->toBeNull();
});

test('an illegal state jump is rejected by the machine', function () {
    Mail::fake();
    $company = billingCompany();
    $invoice = app(BillingServiceInterface::class)
        ->generateMonthlyInvoice($company->id, CarbonImmutable::parse('2026-06-15'));

    // open -> uncollectible is not an allowed transition
    expect(fn () => $invoice->status->transitionTo(Uncollectible::class))
        ->toThrow(TransitionNotFound::class);
});

test('dunning exhaustion moves the invoice to uncollectible and suspends the company', function () {
    Mail::fake();
    Event::fake([CompanySubscriptionSuspended::class]);
    $company = billingCompany();

    $billing = app(BillingServiceInterface::class);
    $invoice = $billing->generateMonthlyInvoice($company->id, CarbonImmutable::parse('2026-06-15'));
    $invoice->update(['stripe_invoice_id' => 'in_dun_1']);
    $billing->handleStripeWebhook(['type' => 'invoice.payment_failed', 'data' => ['object' => ['id' => 'in_dun_1']]]);

    // three due retries
    foreach (range(1, 3) as $i) {
        BillingInvoice::query()->withoutGlobalScopes()->whereKey($invoice->id)
            ->update(['next_retry_at' => now()->subMinute()]);
        $this->artisan(ProcessDunningCommand::class)->assertSuccessful();
    }

    $fresh = $invoice->fresh();
    expect($fresh->status->equals(Uncollectible::class))->toBeTrue()
        ->and($company->fresh()->subscription_status)->toBe('suspended');

    Event::assertDispatched(CompanySubscriptionSuspended::class, fn ($event): bool => $event->company_id === $company->id);
});

test('activate and deactivate fire module events and free modules stay locked on', function () {
    Event::fake([ModuleActivated::class, ModuleDeactivated::class]);
    $company = billingCompany();
    $actor = User::factory()->for($company)->create();

    $billing = app(BillingServiceInterface::class);

    ModuleCatalogEntry::factory()->create(['module_key' => 'finance.ledger', 'per_user_monthly_price' => 500]);
    $billing->activateModule('finance.ledger', $actor);
    Event::assertDispatched(ModuleActivated::class, fn ($event): bool => $event->module_key === 'finance.ledger');

    $billing->deactivateModule('finance.ledger');
    Event::assertDispatched(ModuleDeactivated::class);

    expect(fn () => $billing->deactivateModule('core.audit'))->toThrow(InvalidArgumentException::class);
});

test('mrr and churn compute from active paid subscriptions', function () {
    Mail::fake();
    $company = billingCompany(users: 2); // (300+400)*2 = 1400 cents

    $billing = app(BillingServiceInterface::class);

    expect($billing->mrr()->getMinorAmount()->toInt())->toBe(1400);

    $billing->deactivateModule('crm.deals');
    expect($billing->churnRate(CarbonImmutable::now()))->toBeGreaterThan(0.0);
});
