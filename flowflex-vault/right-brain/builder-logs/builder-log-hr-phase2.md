---
type: builder-log
module: hr-phase2
domain: HR & People
panel: hr
phase: 2
started: 2026-05-10
status: in-progress
color: "#F97316"
left_brain_source: "[[employee-profiles]], [[leave-management]], [[onboarding]], [[payroll]]"
last_updated: 2026-05-10
---

# Builder Log — HR Phase 2

Five modules built in one session: Employee Profiles, Leave Management, Onboarding, Payroll, HR Analytics.

---

## Sessions

### 2026-05-10 — Phase 2 HR & People — Full Build (5 modules)

**What was built:**

Migrations (10 total, range 100001–100010):
- `100001_create_employees_table` — ULID PK, self-referential manager_id FK (moved to separate Schema::table block — see gap below)
- `100002_create_leave_policies_table`
- `100003_create_leave_balances_table`
- `100004_create_leave_requests_table`
- `100005_create_onboarding_templates_table`
- `100006_create_onboarding_template_tasks_table`
- `100007_create_onboarding_checklists_table`
- `100008_create_onboarding_checklist_items_table`
- `100009_create_payroll_runs_table`
- `100010_create_payroll_entries_table`

Models (9 total, all in `app/Models/HR/`):
- `Employee` — BelongsToCompany, HasUlids, SoftDeletes; relations: user, manager, directReports, leaveRequests, onboardingChecklists, leaveBalances, payrollEntries
- `LeavePolicy` — relations: leaveBalances, leaveRequests
- `LeaveBalance` — relations: employee, policy; computed: remaining_days
- `LeaveRequest` — relations: employee, policy, approver
- `OnboardingTemplate` — relations: tasks, checklists
- `OnboardingTemplateTask` — relation: template
- `OnboardingChecklist` — relations: employee, template, items; computed: progress_percentage
- `OnboardingChecklistItem` — relations: checklist, assignee
- `PayrollRun` — relations: approver, entries
- `PayrollEntry` — relations: run, employee; computed: total_deductions, total_additions

Service Interfaces (4, all in `app/Contracts/HR/`):
- `EmployeeServiceInterface` — create(), update(), terminate()
- `LeaveServiceInterface` — requestLeave(), approve(), reject(), cancel(), calculateBalance()
- `OnboardingServiceInterface` — createChecklist(), completeItem(), getProgress()
- `PayrollServiceInterface` — createRun(), addEmployee(), calculateTotals(), approve()

Service Implementations (4, all in `app/Services/HR/`):
- `EmployeeService` — fires EmployeeHired/EmployeeTerminated events
- `LeaveService` — fires LeaveRequested/LeaveApproved/LeaveRejected events; manages balance pending/used days
- `OnboardingService` — creates checklist items from template tasks; auto-completes checklist when all required items done; fires EmployeeOnboardingStarted/EmployeeOnboardingCompleted
- `PayrollService` — fires PayrollRunApproved; calculates totals on approve

DTOs (4, all in `app/Data/HR/`):
- `CreateEmployeeData`, `UpdateEmployeeData`
- `RequestLeaveData`
- `CreatePayrollRunData`

Events (10, all in `app/Events/HR/`):
- `EmployeeHired`, `EmployeeTerminated`
- `LeaveRequested`, `LeaveApproved`, `LeaveRejected`
- `EmployeeOnboardingStarted`, `EmployeeOnboardingCompleted`
- `PayrollRunApproved`, `PayrollRunPaid`

Filament Resources (6, all in `app/Filament/Hr/Resources/`):
- `EmployeeResource` — nav group: Employees; status badge; avatar with UI Avatars fallback; filters: status, department, employment_type
- `LeavePolicyResource` — nav group: Leave; CRUD with toggles for is_paid/requires_approval/is_active
- `LeaveRequestResource` — nav group: Leave; approve/reject actions; inline status badge
- `OnboardingTemplateResource` — nav group: Employees; Repeater for tasks sub-resource
- `OnboardingChecklistResource` — nav group: Employees; progress_percentage column
- `PayrollRunResource` — nav group: Payroll; approve action; entries_count column

Filament Page + Widgets (HR Analytics):
- `HrAnalyticsPage` — nav group: Analytics; uses getView() not static $view (Filament 5 constraint)
- `HeadcountWidget` — StatsOverview: active employees, new hires this month, leavers this month
- `DepartmentBreakdownWidget` — TableWidget: department → headcount
- `LeaveStatsWidget` — StatsOverview: pending requests, approved this month

Service Provider:
- `app/Providers/HR/HrServiceProvider.php` — binds all 4 interfaces to implementations
- Registered in `bootstrap/providers.php`

Factories (7, all in `database/factories/HR/`):
- `EmployeeFactory`, `LeavePolicyFactory`, `LeaveBalanceFactory`, `LeaveRequestFactory`
- `OnboardingTemplateFactory`, `PayrollRunFactory`, `PayrollEntryFactory`

Tests (40 total, all pass):
- `tests/Feature/HR/EmployeeServiceTest.php` — 7 tests
- `tests/Feature/HR/LeaveServiceTest.php` — 7 tests
- `tests/Feature/HR/OnboardingServiceTest.php` — 5 tests
- `tests/Feature/HR/PayrollServiceTest.php` — 5 tests
- `tests/Feature/Filament/HrResourcesTest.php` — 12 tests
- `tests/Feature/Filament/HrPanelTest.php` — 4 tests (pre-existing, still pass)

Left-brain specs created:
- `flowflex-vault/left-brain/domains/02_hr/employee-profiles.md`
- `flowflex-vault/left-brain/domains/02_hr/leave-management.md`
- `flowflex-vault/left-brain/domains/02_hr/onboarding.md`
- `flowflex-vault/left-brain/domains/02_hr/payroll.md`

**Decisions:**
- Self-referential FK must be added in a separate `Schema::table()` block after initial `Schema::create()` on PostgreSQL — see [[decision-2026-05-10-postgresql-self-referential-fk]]
- Filament 5 `Page` uses `getView(): string` method, NOT `protected static string $view` (static declaration causes FatalError — incompatible property declaration)

**Problems encountered and solved:**
1. `employees` migration: PostgreSQL rejected self-referential FK inside `Schema::create` — "no unique constraint matching given keys". Fixed by moving `$table->foreign('manager_id')` to a separate `Schema::table('employees', ...)` call after table creation.
2. `200001_create_projects_table` had the same bug (built by Projects agent). Fixed that migration too to unblock all tests.
3. `HrAnalyticsPage` used `protected static string $view` — PHP fatal error "Cannot redeclare non static Filament\Pages\Page::$view as static". Fixed by replacing with `getView(): string` instance method.
4. `OnboardingService::getProgress()` returned `round()` result as `float 50.0` but test asserted integer `50`. Fixed by casting `(int) round(...)`.

**Test result:** 40 passed, 0 failed (76 assertions)

## Gaps Discovered

- [[gap_postgresql-self-referential-fk]] — self-referential FK must use separate Schema::table block on PostgreSQL
