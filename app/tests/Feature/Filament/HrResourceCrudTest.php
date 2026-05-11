<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Core\BillingSubscription;
use App\Models\HR\Employee;
use App\Models\HR\LeavePolicy;
use App\Models\HR\LeaveRequest;
use App\Models\HR\OnboardingTemplate;
use App\Models\HR\PayrollRun;
use App\Models\User;
use App\Support\Services\CompanyContext;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function activateBilling(Company $company): void
{
    BillingSubscription::create([
        'company_id' => $company->id,
        'status'     => 'active',
    ]);
}

function activateModule(Company $company, string $moduleKey): void
{
    CompanyModuleSubscription::withoutGlobalScopes()->create([
        'company_id'   => $company->id,
        'module_key'   => $moduleKey,
        'status'       => 'active',
        'activated_at' => now(),
    ]);
}

// ---------------------------------------------------------------------------
// EmployeeResource — module: hr.profiles
// ---------------------------------------------------------------------------

describe('HR Employee Resource', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        activateBilling($this->company);
        activateModule($this->company, 'hr.profiles');
    });

    it('list page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/hr/employees')
            ->assertOk();
    });

    it('create page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/hr/employees/create')
            ->assertOk();
    });

    it('employee listing shows an existing employee name', function () {
        $employee = Employee::factory()->create([
            'company_id' => $this->company->id,
            'first_name' => 'Agatha',
            'last_name'  => 'Osei',
        ]);

        $this->actingAs($this->user)
            ->get('/hr/employees')
            ->assertOk()
            ->assertSee('Agatha');
    });

    it('edit page loads for an existing employee', function () {
        $employee = Employee::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAs($this->user)
            ->get("/hr/employees/{$employee->id}/edit")
            ->assertOk();
    });

    it('list page returns 403 without subscription', function () {
        // Create a fresh company with no module subscription
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);

        // Billing exists but module is not activated
        activateBilling($company);

        $this->actingAs($user)
            ->get('/hr/employees')
            ->assertForbidden();
    });

    it('list page returns 403 with no billing at all', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);

        $this->actingAs($user)
            ->get('/hr/employees')
            ->assertForbidden();
    });

    it('employees from another company are not visible on list page', function () {
        $otherCompany = Company::factory()->create(['status' => 'active']);
        Employee::factory()->create([
            'company_id' => $otherCompany->id,
            'first_name' => 'OtherCorp',
            'last_name'  => 'Person',
        ]);

        // Own company has no employees
        $this->actingAs($this->user)
            ->get('/hr/employees')
            ->assertOk()
            ->assertDontSee('OtherCorp');
    });
});

// ---------------------------------------------------------------------------
// LeaveRequestResource — module: hr.leave
// ---------------------------------------------------------------------------

describe('HR Leave Request Resource', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        activateBilling($this->company);
        activateModule($this->company, 'hr.leave');
    });

    it('list page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/hr/leave-requests')
            ->assertOk();
    });

    it('create page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/hr/leave-requests/create')
            ->assertOk();
    });

    it('list page shows an existing leave request', function () {
        $employee = Employee::factory()->create([
            'company_id' => $this->company->id,
            'first_name' => 'Kofi',
            'last_name'  => 'Mensah',
        ]);

        LeaveRequest::factory()->create([
            'company_id'  => $this->company->id,
            'employee_id' => $employee->id,
            'status'      => 'pending',
        ]);

        $this->actingAs($this->user)
            ->get('/hr/leave-requests')
            ->assertOk()
            ->assertSee('Kofi');
    });

    it('list page returns 403 without subscription', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);
        activateBilling($company);

        $this->actingAs($user)
            ->get('/hr/leave-requests')
            ->assertForbidden();
    });
});

// ---------------------------------------------------------------------------
// OnboardingTemplateResource — module: hr.onboarding
// ---------------------------------------------------------------------------

describe('HR Onboarding Template Resource', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        activateBilling($this->company);
        activateModule($this->company, 'hr.onboarding');
    });

    it('list page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/hr/onboarding-templates')
            ->assertOk();
    });

    it('create page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/hr/onboarding-templates/create')
            ->assertOk();
    });

    it('list page shows an existing onboarding template', function () {
        OnboardingTemplate::factory()->create([
            'company_id' => $this->company->id,
            'name'       => 'Standard Onboarding',
        ]);

        $this->actingAs($this->user)
            ->get('/hr/onboarding-templates')
            ->assertOk()
            ->assertSee('Standard Onboarding');
    });

    it('list page returns 403 without subscription', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);
        activateBilling($company);

        $this->actingAs($user)
            ->get('/hr/onboarding-templates')
            ->assertForbidden();
    });
});

// ---------------------------------------------------------------------------
// PayrollRunResource — module: hr.payroll
// ---------------------------------------------------------------------------

describe('HR Payroll Run Resource', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user    = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($this->company);
        activateBilling($this->company);
        activateModule($this->company, 'hr.payroll');
    });

    it('list page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/hr/payroll-runs')
            ->assertOk();
    });

    it('create page loads with active subscription', function () {
        $this->actingAs($this->user)
            ->get('/hr/payroll-runs/create')
            ->assertOk();
    });

    it('list page shows an existing payroll run', function () {
        PayrollRun::factory()->create([
            'company_id' => $this->company->id,
            'name'       => 'January 2026 Payroll',
        ]);

        $this->actingAs($this->user)
            ->get('/hr/payroll-runs')
            ->assertOk()
            ->assertSee('January 2026 Payroll');
    });

    it('edit page loads for an existing payroll run', function () {
        $run = PayrollRun::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAs($this->user)
            ->get("/hr/payroll-runs/{$run->id}/edit")
            ->assertOk();
    });

    it('list page returns 403 without subscription', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'active',
        ]);
        app(CompanyContext::class)->set($company);
        activateBilling($company);

        $this->actingAs($user)
            ->get('/hr/payroll-runs')
            ->assertForbidden();
    });
});
