<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalogEntry;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * Module gating (architecture/module-system.md). The single source of truth
 * every canAccess() consults: BillingService::hasModule('{module_key}').
 * Invoicing/Stripe arrive with the rest of core.billing-engine; gating ships
 * first because every other core module's access contract depends on it.
 */
class BillingService
{
    private const CACHE_TTL_MINUTES = 5;

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

        $companyId = app(CompanyContext::class)->current()->id;

        /** @var CompanyModuleSubscription|null $existing */
        $existing = CompanyModuleSubscription::query()
            ->where('module_key', $moduleKey)
            ->whereNull('deactivated_at')
            ->first();

        if ($existing !== null) {
            return $existing; // already active — idempotent
        }

        $subscription = new CompanyModuleSubscription([
            'company_id' => $companyId,
            'module_key' => $moduleKey,
            'activated_at' => now(),
            'activated_by' => $actor->id,
        ]);
        $subscription->save();

        Cache::forget("company:{$companyId}:modules");

        return $subscription;
    }

    public function deactivateModule(string $moduleKey): void
    {
        $entry = ModuleCatalogEntry::query()->where('module_key', $moduleKey)->first();

        if ($entry !== null && $entry->isFree()) {
            // Free core modules are always-on — never deactivatable.
            throw new InvalidArgumentException("Module [{$moduleKey}] is a free core module and cannot be deactivated.");
        }

        $companyId = app(CompanyContext::class)->current()->id;

        CompanyModuleSubscription::query()
            ->where('module_key', $moduleKey)
            ->whereNull('deactivated_at')
            ->update(['deactivated_at' => now()]);

        Cache::forget("company:{$companyId}:modules");
    }
}
