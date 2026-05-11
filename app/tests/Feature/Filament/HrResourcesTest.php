<?php

declare(strict_types=1);

use App\Filament\Hr\Resources\EmployeeResource;
use App\Filament\Hr\Resources\LeavePolicyResource;
use App\Filament\Hr\Resources\LeaveRequestResource;
use App\Filament\Hr\Resources\OnboardingChecklistResource;
use App\Filament\Hr\Resources\OnboardingTemplateResource;
use App\Filament\Hr\Resources\PayrollRunResource;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Core\BillingSubscription;
use App\Models\User;
use App\Support\Services\CompanyContext;

describe('HR Resource Access Control', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
    });

    it('canAccess returns false without subscription for employee resource', function () {
        $this->actingAs($this->user);

        expect(EmployeeResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without subscription for leave policy resource', function () {
        $this->actingAs($this->user);

        expect(LeavePolicyResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without subscription for leave request resource', function () {
        $this->actingAs($this->user);

        expect(LeaveRequestResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without subscription for payroll resource', function () {
        $this->actingAs($this->user);

        expect(PayrollRunResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without subscription for onboarding template resource', function () {
        $this->actingAs($this->user);

        expect(OnboardingTemplateResource::canAccess())->toBeFalse();
    });

    it('canAccess returns true for hr.profiles with active subscription and billing', function () {
        $this->actingAs($this->user);

        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'hr.profiles',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        expect(EmployeeResource::canAccess())->toBeTrue();
    });

    it('canAccess returns true for hr.leave with active subscription and billing', function () {
        $this->actingAs($this->user);

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

        expect(LeavePolicyResource::canAccess())->toBeTrue();
        expect(LeaveRequestResource::canAccess())->toBeTrue();
    });

    it('canAccess returns true for hr.payroll with active subscription and billing', function () {
        $this->actingAs($this->user);

        BillingSubscription::create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'hr.payroll',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        expect(PayrollRunResource::canAccess())->toBeTrue();
    });

    it('canAccess returns false without authentication', function () {
        expect(EmployeeResource::canAccess())->toBeFalse();
        expect(LeavePolicyResource::canAccess())->toBeFalse();
        expect(PayrollRunResource::canAccess())->toBeFalse();
    });

    it('canAccess returns false without company context', function () {
        app(CompanyContext::class)->clear();
        $this->actingAs($this->user);

        expect(EmployeeResource::canAccess())->toBeFalse();
        expect(LeavePolicyResource::canAccess())->toBeFalse();
        expect(PayrollRunResource::canAccess())->toBeFalse();
    });
});

describe('HR Panel pages load for authenticated user', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
    });

    it('hr dashboard loads for authenticated user', function () {
        $this->actingAs($this->user)
            ->get('/hr')
            ->assertOk();
    });

    it('hr login page is accessible', function () {
        $this->get('/hr/login')->assertOk();
    });
});
