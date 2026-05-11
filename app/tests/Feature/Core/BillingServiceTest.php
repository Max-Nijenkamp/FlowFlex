<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Core\BillingSubscription;
use App\Models\ModuleCatalog;
use App\Services\Core\BillingService;

describe('Billing Service', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'trial']);
        $this->service = app(BillingService::class);
    });

    it('calculates zero amount when no paid modules', function () {
        $amount = $this->service->calculateMonthlyAmount($this->company);
        expect($amount)->toBe(0.0);
    });

    it('calculates monthly amount based on active modules and users', function () {
        ModuleCatalog::create([
            'module_key'             => 'hr.leave',
            'domain'                 => 'hr',
            'name'                   => 'Leave Management',
            'per_user_monthly_price' => 3.00,
            'is_active'              => true,
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'hr.leave',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        // 1 active user (company owner created in factory)
        $amount = $this->service->calculateMonthlyAmount($this->company);
        expect($amount)->toBe(0.0); // no active users yet — factory user has no status set

        // Create active user
        \App\Models\User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        $amount = $this->service->calculateMonthlyAmount($this->company);
        expect($amount)->toBe(3.0);
    });

    it('foundation modules are always accessible regardless of billing', function () {
        expect($this->service->enforceModuleAccess($this->company, 'core.auth'))->toBeTrue();
        expect($this->service->enforceModuleAccess($this->company, 'core.audit-log'))->toBeTrue();
    });

    it('non-foundation module requires active subscription', function () {
        expect($this->service->enforceModuleAccess($this->company, 'hr.leave'))->toBeFalse();
    });

    it('non-foundation module accessible when subscription active and module enabled', function () {
        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'hr.leave',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        expect($this->service->enforceModuleAccess($this->company, 'hr.leave'))->toBeTrue();
    });

    it('caches module access result for 60 seconds', function () {
        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'hr.leave',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        // First call populates the cache
        $resultFirst  = $this->service->enforceModuleAccess($this->company, 'hr.leave');
        // Second call should return same result (from cache)
        $resultSecond = $this->service->enforceModuleAccess($this->company, 'hr.leave');

        expect($resultFirst)->toBeTrue();
        expect($resultSecond)->toBe($resultFirst);

        // Verify cache key is actually set
        $cacheKey = "module_access.{$this->company->id}.hr.leave";
        expect(\Illuminate\Support\Facades\Cache::has($cacheKey))->toBeTrue();
    });
});
