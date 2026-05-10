<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Core\BillingSubscription;
use App\Models\ModuleCatalog;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class BillingService
{
    private ?StripeClient $stripe = null;

    private function stripe(): StripeClient
    {
        if ($this->stripe === null) {
            $key = config('services.stripe.secret');
            if (empty($key)) {
                throw new \RuntimeException('Stripe secret key not configured');
            }
            $this->stripe = new StripeClient($key);
        }

        return $this->stripe;
    }

    public function calculateMonthlyAmount(Company $company): float
    {
        $userCount = $company->users()->where('status', 'active')->count();

        $activeModuleKeys = CompanyModuleSubscription::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->pluck('module_key')
            ->toArray();

        $totalPerUser = ModuleCatalog::whereIn('module_key', $activeModuleKeys)
            ->where('is_active', true)
            ->sum('per_user_monthly_price');

        return round((float) $totalPerUser * $userCount, 2);
    }

    public function ensureStripeCustomer(Company $company): string
    {
        $subscription = BillingSubscription::withoutGlobalScopes()
            ->where('company_id', $company->id)->first();

        if ($subscription?->stripe_customer_id) {
            return $subscription->stripe_customer_id;
        }

        try {
            $customer = $this->stripe()->customers->create([
                'name'  => $company->name,
                'email' => $company->email,
                'metadata' => ['company_id' => $company->id],
            ]);

            BillingSubscription::withoutGlobalScopes()->updateOrCreate(
                ['company_id' => $company->id],
                ['stripe_customer_id' => $customer->id, 'status' => 'trialing'],
            );

            return $customer->id;
        } catch (ApiErrorException $e) {
            throw new \RuntimeException("Stripe customer creation failed: {$e->getMessage()}");
        }
    }

    public function isBillingActive(Company $company): bool
    {
        $subscription = BillingSubscription::withoutGlobalScopes()
            ->where('company_id', $company->id)->first();

        return $subscription && $subscription->isActive();
    }

    public function enforceModuleAccess(Company $company, string $moduleKey): bool
    {
        // Free foundation modules are always accessible
        $freeModules = ['core.auth', 'core.notifications', 'core.audit-log', 'core.file-storage', 'core.rbac'];
        if (in_array($moduleKey, $freeModules, true)) {
            return true;
        }

        if (! CompanyModuleSubscription::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('module_key', $moduleKey)
            ->where('status', 'active')
            ->exists()) {
            return false;
        }

        return $this->isBillingActive($company);
    }
}
