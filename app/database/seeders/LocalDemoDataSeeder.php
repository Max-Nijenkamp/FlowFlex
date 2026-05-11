<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeavePolicy;
use App\Models\HR\LeaveRequest;
use App\Models\HR\OnboardingChecklist;
use App\Models\HR\OnboardingChecklistItem;
use App\Models\HR\OnboardingTemplate;
use App\Models\HR\OnboardingTemplateTask;
use App\Models\HR\PayrollEntry;
use App\Models\HR\PayrollRun;
use App\Models\Projects\KanbanBoard;
use App\Models\Projects\KanbanColumn;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectMilestone;
use App\Models\Projects\Sprint;
use App\Models\Projects\Task;
use App\Models\Projects\TimeEntry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Local-only demo data seeder for the FlowFlex Demo company.
 *
 * Seeds realistic HR and Projects data so you can test the UI without
 * creating everything by hand. Safe to re-run — skips if data already exists.
 *
 * Convention for all future phases: always create a LocalDemoDataSeeder
 * section for each new domain so the demo company stays fully populated.
 */
class LocalDemoDataSeeder extends Seeder
{
    private Company $company;

    private User $owner;

    public function run(): void
    {
        $this->company = Company::withoutGlobalScopes()
            ->where('slug', 'flowflex-demo')
            ->firstOrFail();

        $this->owner = User::withoutGlobalScopes()
            ->where('email', 'test@test.nl')
            ->firstOrFail();

        $this->seedHr();
        $this->seedProjects();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HR
    // ──────────────────────────────────────────────────────────────────────────

    private function seedHr(): void
    {
        if (Employee::withoutGlobalScopes()->where('company_id', $this->company->id)->exists()) {
            $this->command->info('HR demo data already exists — skipping.');

            return;
        }

        $this->command->info('Seeding HR demo data…');

        // ── Employees ──────────────────────────────────────────────────────────

        $cid = $this->company->id;

        $sarah = Employee::create([
            'company_id'      => $cid,
            'employee_number' => 'EMP-0001',
            'first_name'      => 'Sarah',
            'last_name'       => 'Chen',
            'email'           => 'sarah.chen@flowflex-demo.local',
            'job_title'       => 'Chief Executive Officer',
            'department'      => 'Leadership',
            'hire_date'       => '2022-01-01',
            'employment_type' => 'full_time',
            'status'          => 'active',
        ]);

        $mark = Employee::create([
            'company_id'      => $cid,
            'employee_number' => 'EMP-0002',
            'first_name'      => 'Mark',
            'last_name'       => 'Williams',
            'email'           => 'mark.williams@flowflex-demo.local',
            'job_title'       => 'Chief Technology Officer',
            'department'      => 'Engineering',
            'hire_date'       => '2022-03-01',
            'employment_type' => 'full_time',
            'manager_id'      => $sarah->id,
            'status'          => 'active',
        ]);

        $emma = Employee::create([
            'company_id'      => $cid,
            'employee_number' => 'EMP-0003',
            'first_name'      => 'Emma',
            'last_name'       => 'Thompson',
            'email'           => 'emma.thompson@flowflex-demo.local',
            'job_title'       => 'HR Manager',
            'department'      => 'HR',
            'hire_date'       => '2022-04-15',
            'employment_type' => 'full_time',
            'manager_id'      => $sarah->id,
            'status'          => 'active',
        ]);

        $alex = Employee::create([
            'company_id'      => $cid,
            'employee_number' => 'EMP-0004',
            'first_name'      => 'Alex',
            'last_name'       => 'Rodriguez',
            'email'           => 'alex.rodriguez@flowflex-demo.local',
            'job_title'       => 'Senior Software Engineer',
            'department'      => 'Engineering',
            'hire_date'       => '2022-06-01',
            'employment_type' => 'full_time',
            'manager_id'      => $mark->id,
            'status'          => 'active',
        ]);

        $jordan = Employee::create([
            'company_id'      => $cid,
            'employee_number' => 'EMP-0005',
            'first_name'      => 'Jordan',
            'last_name'       => 'Kim',
            'email'           => 'jordan.kim@flowflex-demo.local',
            'job_title'       => 'Software Engineer',
            'department'      => 'Engineering',
            'hire_date'       => '2023-01-16',
            'employment_type' => 'full_time',
            'manager_id'      => $mark->id,
            'status'          => 'active',
        ]);

        $lisa = Employee::create([
            'company_id'      => $cid,
            'employee_number' => 'EMP-0006',
            'first_name'      => 'Lisa',
            'last_name'       => 'van den Berg',
            'email'           => 'lisa.vandenberg@flowflex-demo.local',
            'job_title'       => 'Product Designer',
            'department'      => 'Engineering',
            'hire_date'       => '2023-03-01',
            'employment_type' => 'full_time',
            'manager_id'      => $mark->id,
            'status'          => 'active',
        ]);

        $david = Employee::create([
            'company_id'      => $cid,
            'employee_number' => 'EMP-0007',
            'first_name'      => 'David',
            'last_name'       => 'Santos',
            'email'           => 'david.santos@flowflex-demo.local',
            'job_title'       => 'Sales Manager',
            'department'      => 'Sales',
            'hire_date'       => '2022-09-01',
            'employment_type' => 'full_time',
            'manager_id'      => $sarah->id,
            'status'          => 'active',
        ]);

        $tom = Employee::create([
            'company_id'      => $cid,
            'employee_number' => 'EMP-0008',
            'first_name'      => 'Tom',
            'last_name'       => 'Baker',
            'email'           => 'tom.baker@flowflex-demo.local',
            'job_title'       => 'Junior Software Engineer',
            'department'      => 'Engineering',
            'hire_date'       => now()->toDateString(),
            'employment_type' => 'full_time',
            'manager_id'      => $mark->id,
            'status'          => 'active',
        ]);

        $activeEmployees = [$sarah, $mark, $emma, $alex, $jordan, $lisa, $david, $tom];

        // ── Leave Policies ─────────────────────────────────────────────────────

        $annualPolicy = LeavePolicy::create([
            'company_id'        => $cid,
            'name'              => 'Annual Leave',
            'leave_type'        => 'annual',
            'days_per_year'     => 25.0,
            'carry_over_days'   => 5.0,
            'is_paid'           => true,
            'requires_approval' => true,
            'min_notice_days'   => 7,
            'is_active'         => true,
        ]);

        $sickPolicy = LeavePolicy::create([
            'company_id'        => $cid,
            'name'              => 'Sick Leave',
            'leave_type'        => 'sick',
            'days_per_year'     => 10.0,
            'carry_over_days'   => 0.0,
            'is_paid'           => true,
            'requires_approval' => false,
            'min_notice_days'   => 0,
            'is_active'         => true,
        ]);

        // ── Leave Balances (all active employees, current year) ────────────────

        $year = now()->year;

        foreach ($activeEmployees as $emp) {
            LeaveBalance::create([
                'company_id'     => $cid,
                'employee_id'    => $emp->id,
                'policy_id'      => $annualPolicy->id,
                'year'           => $year,
                'allocated_days' => 25.0,
                'used_days'      => fake()->randomFloat(1, 0, 8),
                'pending_days'   => 0.0,
            ]);

            LeaveBalance::create([
                'company_id'     => $cid,
                'employee_id'    => $emp->id,
                'policy_id'      => $sickPolicy->id,
                'year'           => $year,
                'allocated_days' => 10.0,
                'used_days'      => fake()->randomFloat(1, 0, 3),
                'pending_days'   => 0.0,
            ]);
        }

        // ── Leave Requests ─────────────────────────────────────────────────────

        // Approved past request
        LeaveRequest::create([
            'company_id'     => $cid,
            'employee_id'    => $alex->id,
            'policy_id'      => $annualPolicy->id,
            'start_date'     => '2026-04-14',
            'end_date'       => '2026-04-18',
            'days_requested' => 5.0,
            'reason'         => 'Family holiday',
            'status'         => 'approved',
            'approved_at'    => '2026-04-01',
        ]);

        // Pending upcoming request
        LeaveRequest::create([
            'company_id'     => $cid,
            'employee_id'    => $jordan->id,
            'policy_id'      => $annualPolicy->id,
            'start_date'     => now()->addDays(14)->toDateString(),
            'end_date'       => now()->addDays(18)->toDateString(),
            'days_requested' => 5.0,
            'reason'         => 'Wedding anniversary trip',
            'status'         => 'pending',
        ]);

        // Rejected request
        LeaveRequest::create([
            'company_id'       => $cid,
            'employee_id'      => $lisa->id,
            'policy_id'        => $annualPolicy->id,
            'start_date'       => now()->addDays(5)->toDateString(),
            'end_date'         => now()->addDays(7)->toDateString(),
            'days_requested'   => 3.0,
            'status'           => 'rejected',
            'rejection_reason' => 'Critical sprint period — please reschedule after May 28.',
        ]);

        // ── Onboarding Template ────────────────────────────────────────────────

        $template = OnboardingTemplate::create([
            'company_id'  => $cid,
            'name'        => 'Engineering Onboarding',
            'description' => 'Standard onboarding checklist for new engineering team members.',
        ]);

        $templateTasks = [
            ['title' => 'Set up laptop and dev environment', 'sort_order' => 1, 'is_required' => true],
            ['title' => 'Complete HR paperwork and contract signing', 'sort_order' => 2, 'is_required' => true],
            ['title' => 'Review engineering handbook and coding standards', 'sort_order' => 3, 'is_required' => true],
            ['title' => 'Intro meeting with team lead', 'sort_order' => 4, 'is_required' => true],
            ['title' => 'Access provisioning: GitHub, Slack, Notion', 'sort_order' => 5, 'is_required' => true],
            ['title' => 'Shadow a sprint planning session', 'sort_order' => 6, 'is_required' => false],
            ['title' => 'First PR merged', 'sort_order' => 7, 'is_required' => false],
        ];

        foreach ($templateTasks as $taskData) {
            OnboardingTemplateTask::create(array_merge($taskData, ['template_id' => $template->id]));
        }

        // Tom Baker's active onboarding checklist
        $checklist = OnboardingChecklist::create([
            'company_id'              => $cid,
            'employee_id'             => $tom->id,
            'template_id'             => $template->id,
            'start_date'              => now()->toDateString(),
            'target_completion_date'  => now()->addDays(30)->toDateString(),
        ]);

        foreach ($templateTasks as $i => $taskData) {
            OnboardingChecklistItem::create([
                'checklist_id' => $checklist->id,
                'task_title'   => $taskData['title'],
                'is_required'  => $taskData['is_required'],
                'sort_order'   => $taskData['sort_order'],
                'is_completed' => $i < 2, // first 2 done
                'completed_at' => $i < 2 ? now() : null,
            ]);
        }

        // ── Payroll ────────────────────────────────────────────────────────────

        // April 2026 — approved
        $aprilRun = PayrollRun::create([
            'company_id'       => $cid,
            'name'             => 'April 2026 Payroll',
            'period_start'     => '2026-04-01',
            'period_end'       => '2026-04-30',
            'pay_date'         => '2026-05-05',
            'status'           => 'approved',
            'currency'         => 'EUR',
            'total_gross'      => 0,
            'total_net'        => 0,
            'total_deductions' => 0,
            'approved_at'      => '2026-05-01',
        ]);

        $totalGross = 0;
        $totalNet   = 0;
        $totalDed   = 0;

        $salaries = [5000, 4500, 3800, 4200, 3500, 3600, 4000, 3000];

        foreach ($activeEmployees as $i => $emp) {
            $gross = $salaries[$i];
            $tax   = round($gross * 0.20, 2);
            $pen   = round($gross * 0.08, 2);
            $ded   = $tax + $pen;
            $net   = $gross - $ded;

            PayrollEntry::create([
                'run_id'      => $aprilRun->id,
                'employee_id' => $emp->id,
                'gross_pay'   => $gross,
                'net_pay'     => $net,
                'deductions'  => [
                    ['type' => 'income_tax', 'amount' => $tax],
                    ['type' => 'pension', 'amount' => $pen],
                ],
                'additions'   => [],
            ]);

            $totalGross += $gross;
            $totalNet   += $net;
            $totalDed   += $ded;
        }

        $aprilRun->update([
            'total_gross'      => $totalGross,
            'total_net'        => $totalNet,
            'total_deductions' => $totalDed,
        ]);

        // May 2026 — draft
        PayrollRun::create([
            'company_id'       => $cid,
            'name'             => 'May 2026 Payroll',
            'period_start'     => '2026-05-01',
            'period_end'       => '2026-05-31',
            'pay_date'         => '2026-06-05',
            'status'           => 'draft',
            'currency'         => 'EUR',
            'total_gross'      => 0,
            'total_net'        => 0,
            'total_deductions' => 0,
        ]);

        $this->command->info('HR demo data seeded — 8 employees, 2 policies, 3 leave requests, payroll runs.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Projects
    // ──────────────────────────────────────────────────────────────────────────

    private function seedProjects(): void
    {
        if (Project::withoutGlobalScopes()->where('company_id', $this->company->id)->exists()) {
            $this->command->info('Projects demo data already exists — skipping.');

            return;
        }

        $this->command->info('Seeding Projects demo data…');

        $cid     = $this->company->id;
        $ownerId = $this->owner->id;

        // Get engineers as assignees (fall back to owner if HR not seeded)
        $engineers = User::withoutGlobalScopes()
            ->where('company_id', $cid)
            ->pluck('id')
            ->toArray();

        $assignees = empty($engineers) ? [$ownerId] : $engineers;

        // ── Project 1: FlowFlex Platform v2.0 (active) ────────────────────────

        $platform = Project::create([
            'company_id'  => $cid,
            'name'        => 'FlowFlex Platform v2.0',
            'description' => 'Core SaaS platform — HR, Projects, Finance, CRM modules.',
            'status'      => 'active',
            'priority'    => 'high',
            'owner_id'    => $ownerId,
            'start_date'  => '2026-03-01',
            'due_date'    => '2026-09-30',
        ]);

        // Kanban board
        $board = KanbanBoard::create([
            'company_id'  => $cid,
            'project_id'  => $platform->id,
            'name'        => 'Platform Development Board',
            'description' => 'Main kanban board for platform development.',
            'is_default'  => true,
        ]);

        $columns = [
            ['name' => 'Backlog',     'position' => 1, 'maps_to_status' => 'todo',        'wip_limit' => null],
            ['name' => 'In Progress', 'position' => 2, 'maps_to_status' => 'in_progress',  'wip_limit' => 3],
            ['name' => 'In Review',   'position' => 3, 'maps_to_status' => 'in_review',    'wip_limit' => 2],
            ['name' => 'Done',        'position' => 4, 'maps_to_status' => 'done',         'wip_limit' => null],
        ];

        foreach ($columns as $col) {
            KanbanColumn::create(array_merge($col, ['board_id' => $board->id]));
        }

        // Milestones
        ProjectMilestone::create([
            'company_id'   => $cid,
            'project_id'   => $platform->id,
            'name'         => 'Phase 2 Complete — HR & Projects',
            'description'  => 'All HR and Projects modules built, tested, and production-ready.',
            'due_date'     => '2026-05-31',
            'completed_at' => now(),
        ]);

        ProjectMilestone::create([
            'company_id'  => $cid,
            'project_id'  => $platform->id,
            'name'        => 'Beta Launch',
            'description' => 'First paying customer on the platform.',
            'due_date'    => '2026-07-01',
        ]);

        ProjectMilestone::create([
            'company_id'  => $cid,
            'project_id'  => $platform->id,
            'name'        => 'v1.0 Public Release',
            'description' => 'Finance, CRM, and Billing modules complete. Full public launch.',
            'due_date'    => '2026-09-30',
        ]);

        // Sprints
        $sprint1 = Sprint::create([
            'company_id' => $cid,
            'project_id' => $platform->id,
            'name'       => 'Sprint 1',
            'goal'       => 'Scaffold Phase 0 + Phase 1 core platform.',
            'start_date' => '2026-05-01',
            'end_date'   => '2026-05-14',
            'status'     => 'completed',
            'velocity'   => 34,
        ]);

        $sprint2 = Sprint::create([
            'company_id' => $cid,
            'project_id' => $platform->id,
            'name'       => 'Sprint 2',
            'goal'       => 'Complete HR & Projects Phase 2. Dashboards, kanban views, CRUD tests.',
            'start_date' => '2026-05-15',
            'end_date'   => '2026-05-28',
            'status'     => 'active',
        ]);

        $sprint3 = Sprint::create([
            'company_id' => $cid,
            'project_id' => $platform->id,
            'name'       => 'Sprint 3',
            'goal'       => 'Finance & CRM Phase 3 foundation. Billing integration.',
            'start_date' => '2026-06-01',
            'end_date'   => '2026-06-14',
            'status'     => 'planning',
        ]);

        // Tasks for platform project
        $sprint1Tasks = [];
        $sprint1Definitions = [
            ['title' => 'Set up Laravel 13 + Filament 5 scaffolding',            'status' => 'done', 'priority' => 'urgent', 'story_points' => 5],
            ['title' => 'Multi-tenancy layer with BelongsToCompany trait',        'status' => 'done', 'priority' => 'urgent', 'story_points' => 8],
            ['title' => 'PermissionSeeder with RBAC via spatie/laravel-permission','status' => 'done', 'priority' => 'high',   'story_points' => 5],
            ['title' => 'BillingService with module access enforcement',          'status' => 'done', 'priority' => 'high',   'story_points' => 5],
            ['title' => 'Core panel providers (admin + app)',                     'status' => 'done', 'priority' => 'high',   'story_points' => 3],
            ['title' => 'Docker compose setup (postgres, redis, mailpit, horizon)','status' => 'done', 'priority' => 'medium', 'story_points' => 3],
            ['title' => 'PHPUnit / Pest test foundation + CI',                   'status' => 'done', 'priority' => 'medium', 'story_points' => 5],
        ];

        foreach ($sprint1Definitions as $i => $def) {
            $task = Task::create(array_merge($def, [
                'company_id'   => $cid,
                'project_id'   => $platform->id,
                'assignee_id'  => $assignees[$i % count($assignees)],
                'created_by'   => $ownerId,
                'sort_order'   => $i + 1,
                'completed_at' => $def['status'] === 'done' ? now() : null,
            ]));
            $sprint1Tasks[] = $task->id;
        }

        $sprint1->tasks()->attach($sprint1Tasks);

        $sprint2Tasks = [];
        $sprint2Definitions = [
            ['title' => 'Employee Profiles resource with CRUD',                'status' => 'done',        'priority' => 'high',   'story_points' => 5],
            ['title' => 'Leave Management with race-condition guard',          'status' => 'done',        'priority' => 'high',   'story_points' => 8],
            ['title' => 'Sprint Kanban view page',                             'status' => 'done',        'priority' => 'high',   'story_points' => 5],
            ['title' => 'Dashboard widgets for all 3 panels',                  'status' => 'in_progress', 'priority' => 'medium', 'story_points' => 5],
            ['title' => 'Fix Filament\Tables\Actions namespace across HR',     'status' => 'in_progress', 'priority' => 'high',   'story_points' => 2],
            ['title' => 'CRUD tests for HR and Projects resources',            'status' => 'in_review',   'priority' => 'medium', 'story_points' => 5],
            ['title' => 'Security audit — dropdown data leak fixes',           'status' => 'done',        'priority' => 'urgent', 'story_points' => 3],
            ['title' => 'LocalDemoDataSeeder for all Phase 2 domains',         'status' => 'in_progress', 'priority' => 'medium', 'story_points' => 3],
        ];

        foreach ($sprint2Definitions as $i => $def) {
            $task = Task::create(array_merge($def, [
                'company_id'   => $cid,
                'project_id'   => $platform->id,
                'assignee_id'  => $assignees[$i % count($assignees)],
                'created_by'   => $ownerId,
                'sort_order'   => $i + 1,
                'completed_at' => $def['status'] === 'done' ? now() : null,
            ]));
            $sprint2Tasks[] = $task->id;
        }

        $sprint2->tasks()->attach($sprint2Tasks);

        // Backlog tasks (not in any sprint yet)
        $backlogDefinitions = [
            ['title' => 'Finance module — invoicing and expense tracking',    'priority' => 'high',   'story_points' => 13],
            ['title' => 'CRM module — contacts, pipeline, leads',             'priority' => 'high',   'story_points' => 13],
            ['title' => 'Billing integration — Stripe webhooks for SaaS',    'priority' => 'urgent', 'story_points' => 8],
            ['title' => 'Multi-currency support across all financial models', 'priority' => 'medium', 'story_points' => 5],
            ['title' => 'Email notification templates for all domain events', 'priority' => 'low',    'story_points' => 3],
        ];

        foreach ($backlogDefinitions as $i => $def) {
            Task::create(array_merge($def, [
                'company_id'  => $cid,
                'project_id'  => $platform->id,
                'assignee_id' => $assignees[$i % count($assignees)],
                'created_by'  => $ownerId,
                'status'      => 'todo',
                'sort_order'  => $i + 100,
            ]));
        }

        // Time entries — last 10 working days
        $timeEntryTasks = Task::withoutGlobalScopes()
            ->where('company_id', $cid)
            ->where('project_id', $platform->id)
            ->whereIn('status', ['done', 'in_progress'])
            ->limit(5)
            ->get();

        for ($d = 9; $d >= 0; $d--) {
            $date = now()->subDays($d);
            if ($date->isWeekend()) {
                continue;
            }

            foreach (array_slice($assignees, 0, 3) as $userId) {
                $task = $timeEntryTasks->isNotEmpty()
                    ? $timeEntryTasks->random()
                    : null;

                TimeEntry::create([
                    'company_id'  => $cid,
                    'user_id'     => $userId,
                    'project_id'  => $platform->id,
                    'task_id'     => $task?->id,
                    'date'        => $date->toDateString(),
                    'hours'       => fake()->randomFloat(1, 1, 8),
                    'description' => fake()->sentence(),
                    'is_billable' => true,
                ]);
            }
        }

        // ── Project 2: Website Redesign (active) ──────────────────────────────

        $website = Project::create([
            'company_id'  => $cid,
            'name'        => 'Company Website Redesign',
            'description' => 'Redesign the public-facing website with new branding and copy.',
            'status'      => 'active',
            'priority'    => 'medium',
            'owner_id'    => $ownerId,
            'start_date'  => '2026-05-01',
            'due_date'    => '2026-06-30',
        ]);

        $websiteSprint = Sprint::create([
            'company_id' => $cid,
            'project_id' => $website->id,
            'name'       => 'Sprint 1',
            'goal'       => 'Homepage + About page redesign.',
            'start_date' => '2026-05-20',
            'end_date'   => '2026-06-02',
            'status'     => 'planning',
        ]);

        $websiteTasks = [];
        $websiteTaskDefs = [
            ['title' => 'New brand identity and color palette',    'priority' => 'high',   'story_points' => 3],
            ['title' => 'Homepage wireframes and copy',            'priority' => 'high',   'story_points' => 5],
            ['title' => 'About page redesign',                     'priority' => 'medium', 'story_points' => 3],
            ['title' => 'SEO audit and metadata update',           'priority' => 'medium', 'story_points' => 2],
            ['title' => 'Mobile responsiveness review',            'priority' => 'low',    'story_points' => 2],
        ];

        foreach ($websiteTaskDefs as $i => $def) {
            $task = Task::create(array_merge($def, [
                'company_id'  => $cid,
                'project_id'  => $website->id,
                'assignee_id' => $assignees[$i % count($assignees)],
                'created_by'  => $ownerId,
                'status'      => 'todo',
                'sort_order'  => $i + 1,
            ]));
            $websiteTasks[] = $task->id;
        }

        $websiteSprint->tasks()->attach($websiteTasks);

        // ── Project 3: Q1 2026 Marketing Campaign (completed) ─────────────────

        $marketing = Project::create([
            'company_id'   => $cid,
            'name'         => 'Q1 2026 Marketing Campaign',
            'description'  => 'LinkedIn + email drip campaign for Q1 product launch.',
            'status'       => 'completed',
            'priority'     => 'medium',
            'owner_id'     => $ownerId,
            'start_date'   => '2026-01-01',
            'due_date'     => '2026-03-31',
            'completed_at' => '2026-03-28',
        ]);

        $completedTaskDefs = [
            'Define target audience and ICP',
            'Write LinkedIn post series (10 posts)',
            'Build email drip sequence (5 emails)',
            'Design campaign landing page',
            'Analyse campaign results and write report',
        ];

        foreach ($completedTaskDefs as $i => $title) {
            Task::create([
                'company_id'   => $cid,
                'project_id'   => $marketing->id,
                'title'        => $title,
                'assignee_id'  => $assignees[$i % count($assignees)],
                'created_by'   => $ownerId,
                'status'       => 'done',
                'priority'     => 'medium',
                'story_points' => 3,
                'sort_order'   => $i + 1,
                'completed_at' => now()->subDays(rand(5, 40)),
            ]);
        }

        $this->command->info('Projects demo data seeded — 3 projects, 3 sprints, 25 tasks, 1 kanban board, 3 milestones, time entries.');
    }
}
