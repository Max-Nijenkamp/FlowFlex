<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveType;
use App\Models\Hr\OnboardingTemplate;
use App\Models\Hr\OnboardingTemplateTask;
use App\Models\Hr\PayElement;
use App\Models\Hr\PayrollEntity;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\Projects\Task;
use App\Models\Projects\TaskLabel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Local development seed data — only runs when APP_ENV=local.
 * Creates a demo company with a workspace admin + HR manager tenant,
 * activates HR and Projects modules, and seeds realistic sample data.
 */
class LocalDevSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment() !== 'local') {
            $this->command->warn('LocalDevSeeder skipped — APP_ENV is not local.');
            return;
        }

        $this->command->info('Seeding local dev data...');

        // ── Company ──────────────────────────────────────────────────────────
        $company = Company::withoutGlobalScopes()->firstOrCreate(
            ['slug' => 'acme-corp'],
            [
                'name'       => 'Acme Corp',
                'email'      => 'admin@acme-corp.com',
                'timezone'   => 'Europe/Amsterdam',
                'locale'     => 'en',
                'currency'   => 'EUR',
                'is_enabled' => true,
            ]
        );

        // ── Tenants ──────────────────────────────────────────────────────────
        $admin = Tenant::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'test@test.nl'],
            [
                'company_id' => $company->id,
                'first_name' => 'Test',
                'last_name'  => 'Admin',
                'password'   => Hash::make('test1234'),
                'is_enabled' => true,
            ]
        );

        $hrManager = Tenant::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'hr@acme-corp.com'],
            [
                'company_id' => $company->id,
                'first_name' => 'Sarah',
                'last_name'  => 'Jones',
                'password'   => Hash::make('test1234'),
                'is_enabled' => true,
            ]
        );

        // ── Assign roles ─────────────────────────────────────────────────────
        $admin->assignRole('workspace-admin');
        $hrManager->assignRole('hr-manager');

        // ── Activate modules ─────────────────────────────────────────────────
        $panelsToActivate = ['hr', 'projects', 'workspace'];
        $modules = Module::whereIn('panel_id', $panelsToActivate)->get();
        $pivotData = $modules->mapWithKeys(fn ($m) => [
            $m->id => ['is_enabled' => true, 'enabled_at' => now()],
        ])->toArray();
        $company->modules()->syncWithoutDetaching($pivotData);

        // ── HR: Departments ──────────────────────────────────────────────────
        $engineering = Department::withoutGlobalScopes()->firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Engineering'],
            ['description' => 'Software engineering team']
        );

        $hr = Department::withoutGlobalScopes()->firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Human Resources'],
            ['description' => 'People & culture']
        );

        // ── HR: Leave Types ──────────────────────────────────────────────────
        $leaveTypes = [
            ['name' => 'Annual Leave', 'code' => 'ANNUAL', 'is_paid' => true,  'color' => '#10B981'],
            ['name' => 'Sick Leave',   'code' => 'SICK',   'is_paid' => true,  'color' => '#EF4444'],
            ['name' => 'Unpaid Leave', 'code' => 'UNPAID', 'is_paid' => false, 'color' => '#6B7280'],
        ];
        foreach ($leaveTypes as $lt) {
            LeaveType::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'code' => $lt['code']],
                $lt
            );
        }

        // ── HR: Payroll Entity ───────────────────────────────────────────────
        $payrollEntity = PayrollEntity::withoutGlobalScopes()->firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Acme Corp Payroll'],
            [
                'country_code' => 'NL',
                'is_default'   => true,
            ]
        );

        // ── HR: Pay Elements ─────────────────────────────────────────────────
        $payElements = [
            ['name' => 'Basic Salary', 'element_type' => 'basic_salary', 'is_taxable' => true],
            ['name' => 'Overtime Pay', 'element_type' => 'overtime',     'is_taxable' => true],
            ['name' => 'Tax Deduction', 'element_type' => 'deduction',   'is_taxable' => false],
        ];
        foreach ($payElements as $pe) {
            PayElement::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'name' => $pe['name']],
                $pe
            );
        }

        // ── HR: Employees ────────────────────────────────────────────────────
        $employee1 = Employee::withoutGlobalScopes()->firstOrCreate(
            ['company_id' => $company->id, 'email' => 'john.doe@acme-corp.com'],
            [
                'first_name'        => 'John',
                'last_name'         => 'Doe',
                'department_id'     => $engineering->id,
                'employment_status' => 'active',
                'employment_type'   => 'full_time',
                'start_date'        => '2023-01-15',
                'job_title'         => 'Senior Engineer',
            ]
        );

        $employee2 = Employee::withoutGlobalScopes()->firstOrCreate(
            ['company_id' => $company->id, 'email' => 'jane.smith@acme-corp.com'],
            [
                'first_name'        => 'Jane',
                'last_name'         => 'Smith',
                'department_id'     => $hr->id,
                'employment_status' => 'active',
                'employment_type'   => 'full_time',
                'start_date'        => '2022-06-01',
                'job_title'         => 'HR Manager',
            ]
        );

        // ── HR: Onboarding Template ──────────────────────────────────────────
        $template = OnboardingTemplate::withoutGlobalScopes()->firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Standard Onboarding'],
            ['description' => 'Default onboarding flow for all new hires', 'is_active' => true]
        );

        $templateTasks = [
            ['title' => 'Sign employment contract', 'task_type' => 'document_upload',  'due_day_offset' => 0,  'sort_order' => 1],
            ['title' => 'IT setup & equipment',     'task_type' => 'read_acknowledge', 'due_day_offset' => 1,  'sort_order' => 2],
            ['title' => 'Benefits enrollment',      'task_type' => 'form_fill',        'due_day_offset' => 3,  'sort_order' => 3],
            ['title' => 'Meet the team',            'task_type' => 'read_acknowledge', 'due_day_offset' => 5,  'sort_order' => 4],
            ['title' => '30-day check-in',          'task_type' => 'form_fill',        'due_day_offset' => 30, 'sort_order' => 5],
        ];

        foreach ($templateTasks as $tt) {
            OnboardingTemplateTask::withoutGlobalScopes()->firstOrCreate(
                ['template_id' => $template->id, 'title' => $tt['title']],
                array_merge($tt, ['company_id' => $company->id])
            );
        }

        // ── Projects: Task Labels ────────────────────────────────────────────
        $labels = [
            ['name' => 'Bug',      'color' => '#EF4444'],
            ['name' => 'Feature',  'color' => '#10B981'],
            ['name' => 'Docs',     'color' => '#3B82F6'],
            ['name' => 'Urgent',   'color' => '#F59E0B'],
        ];
        foreach ($labels as $label) {
            TaskLabel::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'name' => $label['name']],
                $label
            );
        }

        // ── Projects: Sample Tasks ───────────────────────────────────────────
        $tasks = [
            ['title' => 'Set up CI/CD pipeline',       'status' => 'todo',        'priority' => 'p2_high'],
            ['title' => 'Design system documentation',  'status' => 'in_progress', 'priority' => 'p3_medium'],
            ['title' => 'Fix login redirect bug',       'status' => 'in_progress', 'priority' => 'p2_high'],
            ['title' => 'Write API integration tests',  'status' => 'todo',        'priority' => 'p3_medium'],
            ['title' => 'Onboard new team member',      'status' => 'done',        'priority' => 'p4_low'],
        ];
        foreach ($tasks as $task) {
            Task::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'title' => $task['title']],
                [
                    'assignee_tenant_id' => $admin->id,
                    'status'             => $task['status'],
                    'priority'           => $task['priority'],
                ]
            );
        }

        $this->command->info("✓ Company: {$company->name} (slug: {$company->slug})");
        $this->command->info("✓ Admin tenant: test@test.nl / test1234");
        $this->command->info("✓ HR manager:   hr@acme-corp.com / test1234");
        $this->command->info("✓ Modules activated: HR, Projects, Workspace");
        $this->command->info("✓ Sample HR + Projects data created");
        $this->command->info("");
        $this->command->info("Admin panel:     http://127.0.0.1:8000/admin  (test@test.nl / test1234)");
        $this->command->info("Workspace panel: http://127.0.0.1:8000/app/settings");
        $this->command->info("HR panel:        http://127.0.0.1:8000/app/hr");
        $this->command->info("Projects panel:  http://127.0.0.1:8000/app/projects");
    }
}
