<?php

declare(strict_types=1);

use App\Contracts\Hr\EmployeeServiceInterface;
use App\Data\Hr\CreateEmployeeData;
use App\Events\Hr\EmployeeHired;
use App\Listeners\Hr\StartOnboardingOnHireListener;
use App\Mail\Hr\WelcomeMail;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\Hr\Department;
use App\Models\Hr\OnboardingPlan;
use App\Models\Hr\OnboardingTemplate;
use App\Models\User;
use App\Services\Hr\OnboardingService;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

function onboardingCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    CompanyModuleSubscription::query()->firstOrCreate(
        ['company_id' => $company->id, 'module_key' => 'hr.onboarding', 'deactivated_at' => null],
        ['activated_at' => now()],
    );
    Cache::forget("company:{$company->id}:modules");

    $template = OnboardingTemplate::factory()->create(['company_id' => $company->id]);
    foreach ([
        ['title' => 'Prepare laptop', 'assigned_role' => 'it', 'order' => 1],
        ['title' => 'Collect ID copy', 'assigned_role' => 'hr', 'order' => 2, 'due_days_after_start' => 5],
        ['title' => '30-day check-in', 'assigned_role' => 'manager', 'order' => 3, 'due_days_after_start' => 30],
    ] as $task) {
        $template->tasks()->create($task + ['company_id' => $company->id]);
    }

    return [$company, $owner, $template];
}

test('EmployeeHired generates a plan from the default template and queues the welcome mail', function () {
    Mail::fake();
    [$company] = onboardingCompany();

    $employee = app(EmployeeServiceInterface::class)->hire(new CreateEmployeeData(
        firstName: 'New', lastName: 'Hire', email: 'new@work.nl',
        jobTitle: 'Analyst', hireDate: now()->toDateString(),
    ));

    (new StartOnboardingOnHireListener)->handle(new EmployeeHired(
        $company->id, $employee->id, null, now()->toDateString(), 'Analyst',
    ));

    $plan = OnboardingPlan::query()->where('employee_id', $employee->id)->firstOrFail();
    expect($plan->planTasks()->count())->toBe(3)
        ->and($plan->progressPercent())->toBe(0);

    Mail::assertQueued(WelcomeMail::class);

    // Re-fired event never doubles the plan
    (new StartOnboardingOnHireListener)->handle(new EmployeeHired(
        $company->id, $employee->id, null, now()->toDateString(), 'Analyst',
    ));
    expect(OnboardingPlan::query()->where('employee_id', $employee->id)->count())->toBe(1);
});

test('completing or skipping the last task stamps the plan complete', function () {
    Mail::fake();
    [$company] = onboardingCompany();
    $service = app(OnboardingService::class);

    $employee = app(EmployeeServiceInterface::class)->hire(new CreateEmployeeData(
        firstName: 'Done', lastName: 'Soon', email: 'done@work.nl',
        jobTitle: 'Ops', hireDate: now()->toDateString(),
    ));
    $plan = $service->generatePlan($employee->fresh());

    $tasks = $plan->planTasks()->get();
    $service->completeTask($tasks[0]);
    $service->completeTask($tasks[1]);
    expect($plan->fresh()->completed_at)->toBeNull();

    $service->completeTask($tasks[2], skipped: true);
    expect($plan->fresh()->completed_at)->not->toBeNull()
        ->and($plan->fresh()->progressPercent())->toBe(100);
});

test('department templates win over the company default', function () {
    Mail::fake();
    [$company] = onboardingCompany();

    $department = Department::factory()->create(['company_id' => $company->id]);
    $departmentTemplate = OnboardingTemplate::factory()->create([
        'company_id' => $company->id, 'name' => 'Engineering onboarding',
        'department_id' => $department->id, 'is_default' => false,
    ]);
    $departmentTemplate->tasks()->create([
        'company_id' => $company->id, 'title' => 'Repo access', 'assigned_role' => 'it', 'order' => 1,
    ]);

    $employee = app(EmployeeServiceInterface::class)->hire(new CreateEmployeeData(
        firstName: 'Dev', lastName: 'Person', email: 'dev@work.nl',
        jobTitle: 'Engineer', hireDate: now()->toDateString(),
        departmentId: $department->id,
    ));

    $plan = app(OnboardingService::class)->generatePlan($employee->fresh());

    expect($plan->template_id)->toBe($departmentTemplate->id)
        ->and($plan->planTasks()->count())->toBe(1);
});

test('listener no-ops when hr.onboarding is inactive', function () {
    Mail::fake();
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);
    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);
    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');
    test()->actingAs($owner);

    OnboardingTemplate::factory()->create(['company_id' => $company->id]);

    $employee = app(EmployeeServiceInterface::class)->hire(new CreateEmployeeData(
        firstName: 'No', lastName: 'Module', email: 'nomod@work.nl',
        jobTitle: 'X', hireDate: now()->toDateString(),
    ));

    (new StartOnboardingOnHireListener)->handle(new EmployeeHired(
        $company->id, $employee->id, null, now()->toDateString(), 'X',
    ));

    expect(OnboardingPlan::query()->count())->toBe(0);
});
