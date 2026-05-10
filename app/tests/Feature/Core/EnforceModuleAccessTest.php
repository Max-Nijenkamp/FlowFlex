<?php

declare(strict_types=1);

use App\Http\Middleware\EnforceModuleAccess;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Core\BillingSubscription;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\Route;

describe('EnforceModuleAccess Middleware', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        app(CompanyContext::class)->set($this->company);

        // Register a test route protected by the middleware
        Route::get('/test-module-access', fn () => response('ok'))
            ->middleware(EnforceModuleAccess::class . ':hr.leave');
    });

    it('allows access to foundation modules without subscription', function () {
        Route::get('/test-foundation-access', fn () => response('ok'))
            ->middleware(EnforceModuleAccess::class . ':core.auth');

        $this->get('/test-foundation-access')->assertOk();
    });

    it('blocks access to non-foundation module without subscription', function () {
        $this->get('/test-module-access')->assertForbidden();
    });

    it('allows access when module subscription active and billing active', function () {
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

        $this->get('/test-module-access')->assertOk();
    });

    it('blocks access when module subscription active but billing inactive', function () {
        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'past_due',
        ]);
        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'hr.leave',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        $this->get('/test-module-access')->assertForbidden();
    });

    it('passes through when no company context is set', function () {
        app(CompanyContext::class)->clear();

        $this->get('/test-module-access')->assertOk();
    });
});
