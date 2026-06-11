<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\BillingServiceInterface;
use App\Data\ActivateModuleData;
use App\Data\BillingInvoiceData;
use App\Events\CompanySubscriptionSuspended;
use App\Events\ModuleActivated;
use App\Exceptions\CannotDeactivateCoreModuleException;
use App\Exceptions\ModuleAlreadyActiveException;
use App\Models\BillingInvoice;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalog;
use App\Models\User;
use App\States\BillingInvoice\Open;
use App\States\BillingInvoice\Paid;
use App\States\BillingInvoice\PastDue;
use App\States\BillingInvoice\Uncollectible;
use App\Support\Services\CompanyContext;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BillingService implements BillingServiceInterface
{
    public function hasModule(string $moduleKey): bool
    {
        return in_array($moduleKey, $this->activeModuleKeys(), true);
    }

    public function activeModuleKeys(): array
    {
        $companyId = app(CompanyContext::class)->currentId();

        if ($companyId === null) {
            return [];
        }

        /** @var list<string> */
        return Cache::remember(
            "company:{$companyId}:modules",
            now()->addMinutes(5),
            fn () => CompanyModuleSubscription::query()
                ->whereNull('deactivated_at')
                ->pluck('module_key')
                ->all(),
        );
    }

    public function activateModule(ActivateModuleData $data): void
    {
        $catalog = ModuleCatalog::entry($data->module_key);

        if ($catalog === null || ! $catalog['is_active']) {
            throw new InvalidArgumentException("Unknown or inactive module [{$data->module_key}].");
        }

        $alreadyActive = CompanyModuleSubscription::query()
            ->where('module_key', $data->module_key)
            ->whereNull('deactivated_at')
            ->exists();

        if ($alreadyActive) {
            throw new ModuleAlreadyActiveException($data->module_key);
        }

        $companyId = app(CompanyContext::class)->current()->id;

        CompanyModuleSubscription::create([
            'module_key' => $data->module_key,
            'activated_at' => now(),
            'activated_by' => Auth::guard('web')->id(),
        ]);

        $this->bustModuleCache($companyId);

        event(new ModuleActivated(
            company_id: $companyId,
            module_key: $data->module_key,
            activated_by: (string) Auth::guard('web')->id(),
        ));
    }

    public function deactivateModule(string $moduleKey): void
    {
        if (ModuleCatalog::isFreeCore($moduleKey)) {
            throw new CannotDeactivateCoreModuleException($moduleKey);
        }

        CompanyModuleSubscription::query()
            ->where('module_key', $moduleKey)
            ->whereNull('deactivated_at')
            ->update(['deactivated_at' => now()]);

        $this->bustModuleCache(app(CompanyContext::class)->current()->id);
    }

    public function seedFreeCoreModules(string $companyId): void
    {
        foreach (ModuleCatalog::freeCoreModules() as $key) {
            CompanyModuleSubscription::query()->withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $companyId, 'module_key' => $key, 'deactivated_at' => null],
                ['activated_at' => now()],
            );
        }

        $this->bustModuleCache($companyId);
    }

    public function generateMonthlyInvoice(string $companyId, CarbonImmutable $period): BillingInvoiceData
    {
        $company = Company::query()->withoutGlobalScopes()->findOrFail($companyId);
        $periodStart = $period->startOfMonth();
        $periodEnd = $period->endOfMonth();

        // Idempotent: existing invoice for the period is returned as-is.
        $existing = BillingInvoice::query()->withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->first();

        if ($existing instanceof BillingInvoice) {
            return BillingInvoiceData::fromModel($existing->load('lines'));
        }

        $currency = 'EUR';
        $userCount = User::query()->withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->count();

        $paidModules = CompanyModuleSubscription::query()->withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereNull('deactivated_at')
            ->pluck('module_key')
            ->filter(fn (string $key) => ModuleCatalog::priceCents($key) > 0);

        return DB::transaction(function () use ($companyId, $periodStart, $periodEnd, $currency, $userCount, $paidModules) {
            $total = Money::ofMinor(0, $currency);

            $invoice = BillingInvoice::query()->withoutGlobalScopes()->create([
                'company_id' => $companyId,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_cents' => 0,
                'currency' => $currency,
            ]);

            foreach ($paidModules as $moduleKey) {
                $unit = Money::ofMinor(ModuleCatalog::priceCents($moduleKey), $currency);
                $lineTotal = $unit->multipliedBy($userCount);
                $total = $total->plus($lineTotal);

                $invoice->lines()->create([
                    'company_id' => $companyId,
                    'module_key' => $moduleKey,
                    'module_name' => (string) (ModuleCatalog::entry($moduleKey)['name'] ?? $moduleKey),
                    'user_count' => $userCount,
                    'unit_price_cents' => $unit->getMinorAmount()->toInt(),
                    'line_total_cents' => $lineTotal->getMinorAmount()->toInt(),
                ]);
            }

            $invoice->update(['total_cents' => $total->getMinorAmount()->toInt()]);
            $invoice->status->transitionTo(Open::class);

            return BillingInvoiceData::fromModel($invoice->refresh()->load('lines'));
        });
    }

    public function handleStripeWebhook(array $event): void
    {
        $stripeInvoiceId = (string) data_get($event, 'data.object.id', '');

        $invoice = BillingInvoice::query()->withoutGlobalScopes()
            ->where('stripe_invoice_id', $stripeInvoiceId)
            ->first();

        if ($invoice === null) {
            return; // not ours — ignore
        }

        match ((string) data_get($event, 'type')) {
            'invoice.payment_succeeded' => $this->markPaid($invoice),
            'invoice.payment_failed' => $this->markPastDue($invoice),
            default => null,
        };
    }

    public function suspend(string $companyId, string $reason): void
    {
        Company::query()->withoutGlobalScopes()
            ->whereKey($companyId)
            ->update(['subscription_status' => 'suspended']);

        event(new CompanySubscriptionSuspended(company_id: $companyId, reason: $reason));
    }

    private function markPaid(BillingInvoice $invoice): void
    {
        $invoice->status->transitionTo(Paid::class);
        $invoice->forceFill(['paid_at' => now(), 'next_dunning_at' => null])->save();
    }

    private function markPastDue(BillingInvoice $invoice): void
    {
        if (! $invoice->status->equals(PastDue::class)) {
            $invoice->status->transitionTo(PastDue::class);
        }

        /** @var list<int> $schedule */
        $schedule = config('flowflex.dunning_retry_days', [3, 7, 14]);
        $attempt = $invoice->dunning_attempts;

        if ($attempt >= count($schedule)) {
            // Dunning exhausted — uncollectible + suspension.
            $invoice->status->transitionTo(Uncollectible::class);
            $invoice->forceFill(['next_dunning_at' => null])->save();
            $this->suspend($invoice->company_id, 'payment_failed');

            return;
        }

        $invoice->forceFill([
            'dunning_attempts' => $attempt + 1,
            'next_dunning_at' => now()->addDays($schedule[$attempt]),
        ])->save();
    }

    private function bustModuleCache(string $companyId): void
    {
        Cache::forget("company:{$companyId}:modules");
    }
}
