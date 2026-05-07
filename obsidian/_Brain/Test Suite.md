---
tags: [brain, tests]
last_updated: 2026-05-07
---

# Test Suite

**580 passing · 0 skipped · 0 failing**  
Runner: Pest PHP v4 (PHPUnit 12.5)

```bash
# Run all tests (artisan hits 128M memory limit — use pest directly)
XDEBUG_MODE=off php -d memory_limit=768M vendor/bin/pest --no-coverage

# Run a single file
XDEBUG_MODE=off php -d memory_limit=768M vendor/bin/pest tests/Feature/Phase2/Hr/EmployeeTest.php --no-coverage

# Run a specific test by description
XDEBUG_MODE=off php -d memory_limit=768M vendor/bin/pest --filter "can create an employee" --no-coverage
```

---

## Structure

```
tests/
├── Unit/
│   ├── BelongsToCompanyTest.php         — trait auto-sets company_id; CompanyScope fires on tenant auth
│   ├── CompanyScopeTest.php             — scope only applies when tenant guard is authenticated
│   ├── GeneratePayslipPdfJobTest.php    — job fetches pay run, marks payslip.generated_at
│   ├── TotalDaysCalculationTest.php     — LeaveRequest total_days calc: weekdays + half-day logic
│   └── Models/
│       ├── Marketing/
│       │   ├── BlogPostTest.php         — slug uniqueness, published scope
│       │   ├── ChangelogEntryTest.php   — published scope, type cast
│       │   ├── DemoRequestTest.php      — required fields, is_contacted flag
│       │   ├── FaqEntryTest.php         — sort_order, is_published
│       │   └── OpenRoleTest.php         — open scope (is_published + not expired)
│
├── Feature/
│   ├── Phase1/
│   │   ├── AdminAuthTest.php            — User login/logout, wrong password, disabled account redirect
│   │   ├── TenantAuthTest.php           — Tenant login (tenant guard), wrong guard rejection, panel access
│   │   ├── MultiTenancyTest.php         — Company isolation: tenant A queries return only company A data
│   │   ├── RbacTest.php                 — Role/permission assignment, can/cannot checks
│   │   ├── ApiKeyAuthTest.php           — API key middleware: valid key, invalid key, missing header, expired key
│   │   ├── FileStorageTest.php          — File model CRUD, URL resolution via FileStorageService, raw S3 path never returned
│   │   ├── NotificationPreferenceTest.php — Per-tenant per-channel preference CRUD
│   │   └── WorkspaceSettingsTest.php    — Company settings JSON read/write
│   │
│   ├── Phase1_5/
│   │   └── MarketingRoutesTest.php      — /help returns 200; /help/{slug} returns 200 (published) or 404 (unpublished);
│   │                                       /modules/{key} returns 200 (available module) or 404 (unavailable/unknown)
│   │
│   ├── Phase2/
│   │   ├── Hr/
│   │   │   ├── DepartmentTest.php       — CRUD, manager assignment, company scope isolation
│   │   │   ├── EmployeeTest.php         — CRUD, full_name accessor (middle name null handling), company isolation
│   │   │   ├── LeaveTypeTest.php        — CRUD, is_paid/accrual fields, company scope
│   │   │   ├── LeaveRequestTest.php     — Create, approve, reject workflows, total_days calculation
│   │   │   ├── OnboardingTemplateTest.php — Template CRUD, task ordering via relation name `tasks` (not `templateTasks`)
│   │   │   ├── OnboardingFlowTest.php   — Flow creation from template, task completion tracking
│   │   │   ├── PayrollEntityTest.php    — CRUD, encrypted fields not logged
│   │   │   ├── PayElementTest.php       — CRUD, element_type enum validation
│   │   │   ├── PayRunTest.php           — Create, status transitions, payslip job dispatch on approve
│   │   │   ├── PayslipTest.php          — Payslip CRUD, pdf_file_id not in fillable
│   │   │   ├── SalaryRecordTest.php     — CRUD, effective_date ordering
│   │   │   ├── DeductionTest.php        — CRUD, recurring flag
│   │   │   └── ContractorPaymentTest.php — CRUD, tenant FK
│   │   └── Projects/
│   │       ├── TaskTest.php             — CRUD, status transitions, assignee scoping, label attach
│   │       ├── TaskLabelTest.php        — CRUD, permissions use projects.task-labels.* (not projects.tasks.*)
│   │       ├── TaskSubtaskTest.php      — parent_id FK, parent()/children() relations, company scope
│   │       ├── TimeEntryTest.php        — Create with auth('tenant') guard, duration calculation
│   │       ├── TimesheetTest.php        — CRUD, period validation, submit action
│   │       ├── DocumentFolderTest.php   — CRUD, self-referential parent/child nesting
│   │       └── DocumentTest.php        — Upload, URL via FileStorageService (never raw path), versioning
│   │
│   ├── Phase3/
│   │   ├── Finance/
│   │   │   ├── RecurringInvoiceResourceTest.php — list 200, no-auth redirect, no-permission 403,
│   │   │   │                                        create/update via Livewire, company isolation
│   │   │   └── ExpenseReportResourceTest.php    — same coverage pattern, approve action
│   │   ├── Crm/
│   │   │   ├── TicketSlaRuleResourceTest.php    — list /crm/ticket-sla-rules, CRUD via Livewire,
│   │   │   │                                       TicketPriority enum cast, company isolation
│   │   │   ├── ChatbotRuleResourceTest.php      — list /crm/chatbot-rules, create (trigger_keywords
│   │   │   │                                       passed as CSV string not array), update, array cast, isolation
│   │   │   ├── TicketSlaBreachTest.php          — CRUD, no SoftDeletes (check via class_uses_recursive),
│   │   │   │                                       ticket + SLA rule relations
│   │   │   ├── CsatSurveyTest.php               — CRUD, token NOT NULL + uniqueness, datetime cast
│   │   │   │                                       returns DateTimeInterface (CarbonImmutable)
│   │   │   ├── CrmActivityTest.php              — polymorphic morph (subject_type/subject_id), datetime cast
│   │   │   ├── DealNoteTest.php                 — CRUD, deal + tenant relations, company scope
│   │   │   └── SharedInboxTest.php              — SharedInbox + InboxEmail CRUD, message_id NOT NULL,
│   │   │                                           column is email_address (not email)
│   │   ├── FinanceModelsTest.php        — Invoice, Expense, ExpenseReport, CreditNote, MileageRate CRUD + ULID
│   │   ├── CrmModelsTest.php            — CrmContact, CrmCompany, Deal, Pipeline, Ticket, TicketSlaRule,
│   │   │                                  ChatbotRule, CsatSurvey, CrmActivity, DealNote, SharedInbox, InboxEmail
│   │   ├── FinanceApiTest.php           — GET /api/v1/finance/{invoices,expenses} — auth, pagination, isolation
│   │   └── CrmApiTest.php              — GET /api/v1/crm/{contacts,deals,tickets} — auth, pagination, isolation
│   │
│   └── Events/
│       ├── HrEventsTest.php             — LeaveApproved, PayRunGenerated fire with correct payload
│       ├── ProjectsEventsTest.php       — TaskCreated, TaskStatusChanged fire
│       ├── FinanceEventsTest.php        — InvoiceCreated, ExpenseSubmitted fire
│       └── CrmEventsTest.php           — TicketResolved, DealWon fire
```

---

## Test Helper Functions

Defined in `tests/TestCase.php` or Pest `beforeEach`:

```php
// Create a company (no BelongsToCompany scope — workspace entity)
$company = makeCompany();

// Create a tenant for a company
$tenant = makeTenant($company);

// Authenticate as a tenant (for panel tests)
actingAs($tenant, 'tenant');

// Authenticate as super-admin
actingAs($user);  // web guard is default

// Grant permissions to a tenant
$tenant->givePermissionTo(['hr.employees.view', 'hr.employees.create']);

// Attach a module to a company (enables panel access)
attachModule($company, 'hr', 'hr');
```

---

## Conventions

- All tests use `RefreshDatabase` — SQLite in-memory (local) / PostgreSQL (CI)
- Every test creates its own `Company` + `Tenant` — no shared state between tests
- Cross-company isolation pattern:

```php
// Create two companies and assert data is isolated
$companyA = makeCompany();
$companyB = makeCompany();
$tenantA  = makeTenant($companyA);

Employee::withoutGlobalScopes()->create(['company_id' => $companyB->id, ...]);

actingAs($tenantA, 'tenant');
expect(Employee::all())->toHaveCount(0); // companyB's employee not visible to companyA
```

- Filament Livewire resource tests pattern:

```php
actingAs($tenant, 'tenant');
livewire(ListInvoices::class)->assertOk();
livewire(CreateInvoice::class)
    ->fillForm(['number' => 'INV-001', 'status' => 'draft', ...])
    ->call('create')
    ->assertHasNoFormErrors();
```

- Datetime cast assertions — app uses `Date::use(CarbonImmutable::class)`, so casts return `CarbonImmutable`, not `\Illuminate\Support\Carbon`:

```php
// CORRECT
expect($model->sent_at)->toBeInstanceOf(\DateTimeInterface::class);

// WRONG — will fail because CarbonImmutable != \Illuminate\Support\Carbon
expect($model->sent_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
```

---

## Factories

All states documented here. For full factory code see `database/factories/`.

```
database/factories/
├── CompanyFactory.php
├── TenantFactory.php
├── UserFactory.php
│
├── Hr/
│   ├── DepartmentFactory.php
│   ├── EmployeeFactory.php              states: active(), terminated(), forCompany($company)
│   ├── LeaveTypeFactory.php             states: unpaid()
│   ├── LeaveRequestFactory.php          states: approved(), rejected()
│   ├── PayrollEntityFactory.php         states: default()
│   ├── PayElementFactory.php
│   ├── PayRunFactory.php                states: approved(), processed()
│   ├── SalaryRecordFactory.php
│   ├── OnboardingTemplateFactory.php
│   ├── OnboardingFlowFactory.php        states: completed()
│   ├── DeductionFactory.php
│   └── ContractorPaymentFactory.php     states: processed()
│
├── Projects/
│   ├── TaskFactory.php                  states: done(), inProgress(), forCompany($company)
│   ├── TaskLabelFactory.php
│   ├── TimesheetFactory.php             states: submitted(), approved()
│   ├── TimeEntryFactory.php             states: approved()
│   ├── DocumentFolderFactory.php
│   └── DocumentFactory.php             states: starred()
│
├── Finance/
│   ├── InvoiceFactory.php              states: draft(), sent(), paid()
│   ├── ExpenseFactory.php              states: approved(), rejected()
│   ├── ExpenseCategoryFactory.php      states: inactive()
│   ├── CreditNoteFactory.php           states: forInvoice($invoice)
│   ├── MileageRateFactory.php          states: inactive()
│   ├── RecurringInvoiceFactory.php     (no states)
│   └── ExpenseReportFactory.php        states: submitted()
│
└── Crm/
    ├── CrmContactFactory.php           states: lead(), customer()
    ├── CrmCompanyFactory.php
    ├── PipelineFactory.php             states: default()
    ├── DealFactory.php                 states: won(), lost()
    ├── TicketFactory.php               states: resolved(), high()
    ├── CannedResponseFactory.php       states: private()
    ├── TicketSlaRuleFactory.php        states: inactive()
    ├── TicketSlaBreachFactory.php
    ├── CsatSurveyFactory.php           states: sent()
    ├── CsatResponseFactory.php
    ├── ChatbotRuleFactory.php          states: inactive()
    ├── CrmContactCustomFieldFactory.php   states: dropdown()
    ├── CrmContactCustomFieldValueFactory.php
    ├── CrmActivityFactory.php
    ├── DealNoteFactory.php
    ├── SharedInboxFactory.php          states: inactive()
    └── InboxEmailFactory.php           states: read(), archived()
```

---

## Known Pitfalls in Tests

| Pitfall | Correct Pattern |
|---|---|
| `OnboardingTemplate::tasks()` relation | Relation name is `tasks`, NOT `templateTasks` |
| `TicketPriority::Medium` | Does not exist. Use `TicketPriority::Normal` |
| `TaskPriority` backing values | Use `'p3_medium'` not `'medium'` — enum has `p1_critical`, `p2_high`, `p3_medium`, `p4_low` |
| `ChatbotRule.trigger_keywords` in Livewire | Pass as CSV string `'word1, word2'` — form does `explode()`. NOT array `['word1', 'word2']` |
| `SharedInbox.email_address` | Column is `email_address`, NOT `email` |
| `CsatSurvey.token` | NOT NULL — must include in every fixture |
| `InboxEmail.message_id` | NOT NULL — must include in every fixture |
| `CrmActivity` columns | Use `subject_type`, `subject_id`, `description` — not `crm_contact_id`, `subject` |
| `TicketSlaBreach.usingSoftDeletes()` | Method doesn't exist on Eloquent — use `in_array(SoftDeletes::class, class_uses_recursive($model))` |
| Datetime cast assertions | Use `\DateTimeInterface::class` — app uses CarbonImmutable, not Carbon |
| Auth guard in Projects | Use `auth('tenant')->id()` not `auth()->id()` |
| Memory limit with artisan test | Use `XDEBUG_MODE=off php -d memory_limit=768M vendor/bin/pest` |
| `HelpArticle.body` | NOT NULL — always include in fixtures |

---

## Coverage Gaps (known, acceptable)

- `TicketMessage` model CRUD — no dedicated test
- `DealStage` model CRUD — no dedicated test
- Finance/CRM events test coverage — events test suite covers HR + Projects only
- No browser/E2E tests — Pest covers backend; Filament UI tested via Livewire component tests
