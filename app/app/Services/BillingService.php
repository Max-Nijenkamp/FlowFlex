<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\BillingServiceInterface;
use App\Events\CompanySubscriptionSuspended;
use App\Events\ModuleActivated;
use App\Events\ModuleDeactivated;
use App\Mail\InvoiceMail;
use App\Models\BillingInvoice;
use App\Models\BillingInvoiceLine;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalogEntry;
use App\Models\User;
use App\Services\Stripe\StripeBillingClient;
use App\States\Core\BillingInvoice\Open;
use App\States\Core\BillingInvoice\Paid;
use App\States\Core\BillingInvoice\PastDue;
use App\Support\Services\CompanyContext;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

/**
 * Billing engine (architecture/module-system.md + core.billing-engine).
 * The single gating source every canAccess() consults, plus invoicing,
 * webhook routing, dunning support, suspension and admin metrics.
 * All money is integer euro cents; arithmetic via brick/money.
 */
class BillingService implements BillingServiceInterface
{
    private const CACHE_TTL_MINUTES = 5;

    public function __construct(
        private readonly StripeBillingClient $stripe,
    ) {}

    public function hasModule(string $moduleKey): bool
    {
        $companyId = app(CompanyContext::class)->currentId();

        if ($companyId === null) {
            return false; // fail closed — no tenant, no modules
        }

        return in_array($moduleKey, $this->activeModules($companyId), true);
    }

    /** @return list<string> */
    public function activeModules(string $companyId): array
    {
        /** @var list<string> */
        return Cache::remember(
            "company:{$companyId}:modules",
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn (): array => CompanyModuleSubscription::query()
                ->withoutGlobalScopes() // explicit company_id filter below; callers may lack context
                ->where('company_id', $companyId)
                ->whereNull('deactivated_at')
                ->pluck('module_key')
                ->all(),
        );
    }

    public function activateModule(string $moduleKey, User $actor): CompanyModuleSubscription
    {
        $entry = ModuleCatalogEntry::query()
            ->where('module_key', $moduleKey)
            ->where('is_active', true)
            ->first();

        if ($entry === null) {
            throw new InvalidArgumentException("Module [{$moduleKey}] is not in the catalog.");
        }

        $company = app(CompanyContext::class)->current();

        return DB::transaction(function () use ($company, $moduleKey, $actor): CompanyModuleSubscription {
            /** @var CompanyModuleSubscription|null $existing */
            $existing = CompanyModuleSubscription::query()
                ->where('module_key', $moduleKey)
                ->whereNull('deactivated_at')
                ->lockForUpdate()
                ->first();

            if ($existing !== null) {
                return $existing; // already active — idempotent
            }

            $subscription = new CompanyModuleSubscription([
                'company_id' => $company->id,
                'module_key' => $moduleKey,
                'activated_at' => now(),
                'activated_by' => $actor->id,
            ]);
            $subscription->save();

            $this->stripe->syncSubscriptionItem($company, $moduleKey, active: true);

            Cache::forget("company:{$company->id}:modules");

            ModuleActivated::dispatch($company->id, $moduleKey, $actor->id);

            return $subscription;
        });
    }

    public function deactivateModule(string $moduleKey): void
    {
        $entry = ModuleCatalogEntry::query()->where('module_key', $moduleKey)->first();

        if ($entry !== null && $entry->isFree()) {
            // Free core modules are always-on — never deactivatable.
            throw new InvalidArgumentException("Module [{$moduleKey}] is a free core module and cannot be deactivated.");
        }

        $company = app(CompanyContext::class)->current();

        DB::transaction(function () use ($company, $moduleKey): void {
            /** @var Collection<int, CompanyModuleSubscription> $active */
            $active = CompanyModuleSubscription::query()
                ->where('module_key', $moduleKey)
                ->whereNull('deactivated_at')
                ->lockForUpdate()
                ->get();

            $active->each(fn (CompanyModuleSubscription $subscription) => $subscription->update(['deactivated_at' => now()]));

            $this->stripe->syncSubscriptionItem($company, $moduleKey, active: false);

            Cache::forget("company:{$company->id}:modules");

            ModuleDeactivated::dispatch($company->id, $moduleKey);
        });
    }

    /**
     * sum(module_price_per_user) x active_user_count, snapshotted into
     * invoice + lines. Idempotent per (company_id, period_start) unique.
     */
    public function generateMonthlyInvoice(string $companyId, CarbonImmutable $period): ?BillingInvoice
    {
        $periodStart = $period->startOfMonth();
        $periodEnd = $period->endOfMonth();

        $exists = BillingInvoice::query()
            ->withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->exists();

        if ($exists) {
            return null; // re-run skips existing (idempotent)
        }

        /** @var Company $company */
        $company = Company::query()->findOrFail($companyId);
        $userCount = $company->users()->count();

        $activeKeys = $this->activeModules($companyId);
        $entries = ModuleCatalogEntry::query()
            ->whereIn('module_key', $activeKeys)
            ->where('per_user_monthly_price', '>', 0)
            ->get();

        if ($entries->isEmpty() || $userCount === 0) {
            return null; // nothing billable this period
        }

        return DB::transaction(function () use ($company, $entries, $userCount, $periodStart, $periodEnd): BillingInvoice {
            $total = Money::ofMinor(0, $company->currency);

            $invoice = new BillingInvoice([
                'company_id' => $company->id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'total_cents' => 0,
                'currency' => $company->currency,
            ]);
            $invoice->save();

            foreach ($entries as $entry) {
                $lineTotal = Money::ofMinor($entry->per_user_monthly_price, $company->currency)
                    ->multipliedBy($userCount);

                $line = new BillingInvoiceLine([
                    'invoice_id' => $invoice->id,
                    'company_id' => $company->id,
                    'module_key' => $entry->module_key,
                    'module_name' => $entry->name,
                    'user_count' => $userCount,
                    'unit_price_cents' => $entry->per_user_monthly_price,
                    'line_total_cents' => $lineTotal->getMinorAmount()->toInt(),
                ]);
                $line->save();

                $total = $total->plus($lineTotal);
            }

            $invoice->update(['total_cents' => $total->getMinorAmount()->toInt()]);

            // draft -> open: Stripe invoice created (id null when disabled)
            $invoice->update(['stripe_invoice_id' => $this->stripe->createInvoice($invoice)]);
            $invoice->status->transitionTo(Open::class);

            $recipient = $company->users()->first();

            Mail::to($recipient !== null ? $recipient->email : 'billing@flowflex.nl')
                ->queue(new InvoiceMail($invoice->id, $total->formatToLocale('nl_NL'), $periodStart->format('F Y')));

            return $invoice->refresh();
        });
    }

    /** @param array<string, mixed> $event */
    public function handleStripeWebhook(array $event): void
    {
        $type = (string) ($event['type'] ?? '');
        $stripeInvoiceId = (string) data_get($event, 'data.object.id', '');

        if ($stripeInvoiceId === '') {
            return;
        }

        DB::transaction(function () use ($type, $stripeInvoiceId): void {
            /** @var BillingInvoice|null $invoice */
            $invoice = BillingInvoice::query()
                ->withoutGlobalScopes()
                ->where('stripe_invoice_id', $stripeInvoiceId)
                ->lockForUpdate()
                ->first();

            if ($invoice === null) {
                return;
            }

            match ($type) {
                'invoice.payment_succeeded' => $this->markPaid($invoice),
                'invoice.payment_failed' => $this->markPastDue($invoice),
                default => null,
            };
        });
    }

    private function markPaid(BillingInvoice $invoice): void
    {
        if ($invoice->status->equals(Paid::class)) {
            return; // duplicate webhook delivery — idempotent
        }

        $invoice->status->transitionTo(Paid::class);
        $invoice->update(['paid_at' => now(), 'next_retry_at' => null]);
    }

    private function markPastDue(BillingInvoice $invoice): void
    {
        if (! $invoice->status->equals(Open::class)) {
            return;
        }

        $invoice->status->transitionTo(PastDue::class);
        // dunning: 3 attempts over 14 days (days 4 / 9 / 14)
        $invoice->update(['dunning_attempts' => 0, 'next_retry_at' => now()->addDays(4)]);
    }

    /** Provisioning: activate every free core module for a new company. */
    public function seedFreeCoreModules(Company $company): void
    {
        ModuleCatalogEntry::query()
            ->where('per_user_monthly_price', 0)
            ->where('is_active', true)
            ->pluck('module_key')
            ->each(function (string $key) use ($company): void {
                CompanyModuleSubscription::query()->firstOrCreate(
                    ['company_id' => $company->id, 'module_key' => $key, 'deactivated_at' => null],
                    ['activated_at' => now()],
                );
            });

        Cache::forget("company:{$company->id}:modules");
    }

    public function suspend(string $companyId, string $reason): void
    {
        Company::query()->whereKey($companyId)->update(['subscription_status' => 'suspended']);

        Cache::forget("company:{$companyId}:modules");

        CompanySubscriptionSuspended::dispatch($companyId, $reason);
    }

    /** Monthly recurring revenue across all companies: active paid modules x users. */
    public function mrr(): Money
    {
        $total = Money::ofMinor(0, 'EUR');

        $priceByKey = ModuleCatalogEntry::query()
            ->where('per_user_monthly_price', '>', 0)
            ->pluck('per_user_monthly_price', 'module_key');

        Company::query()->get()->each(function (Company $company) use (&$total, $priceByKey): void {
            $userCount = $company->users()->count();

            foreach ($this->activeModules($company->id) as $key) {
                $price = $priceByKey[$key] ?? 0;

                if ($price > 0) {
                    $total = $total->plus(Money::ofMinor($price * $userCount, 'EUR'));
                }
            }
        });

        return $total;
    }

    /** Fraction of companies with any paid deactivation in the period. */
    public function churnRate(CarbonImmutable $period): float
    {
        $companies = Company::query()->count();

        if ($companies === 0) {
            return 0.0;
        }

        $churned = CompanyModuleSubscription::query()
            ->withoutGlobalScopes()
            ->whereBetween('deactivated_at', [$period->startOfMonth(), $period->endOfMonth()])
            ->distinct('company_id')
            ->count('company_id');

        return round($churned / $companies, 4);
    }
}
