---
tags: [brain, features]
last_updated: 2026-05-07
---

# Features

What actually works right now in each panel. Panel-by-panel feature list. For architecture intent see the Obsidian spec (`00-14/`). This file is implementation truth.

---

## Admin Panel (`/admin`)

**Guard:** `web` (User model — FlowFlex super-admin only)  
**Access:** FlowFlex staff only — not customer-facing

### Companies

- Create / edit / soft-delete companies
- Enable / disable company accounts (`is_enabled` flag)
- Set timezone, locale (language), currency
- Attach company logo — stored as `File`, served via signed S3 URL through `FileStorageService`
- Set arbitrary JSON settings via `settings` column
- View a company's activated modules (pivot via `CompanyModule`)
- View all tenants belonging to a company (TenantsRelationManager)

### Tenants

- Create / edit / disable tenant accounts
- Assign Spatie roles to tenants (RolesRelationManager)
- Filter tenants by company

### Users (super-admins)

- Create / edit other `User` accounts (FlowFlex admin staff)
- Web guard only — no workspace panel access

### Modules

- View all platform `Module` and `SubModule` definitions
- Enable / disable modules platform-wide (`is_available` flag)
- Module activation per company is managed from the Workspace panel

### Roles & Permissions

- Create custom roles (backed by Spatie Permission)
- Assign permissions to roles
- Permission naming convention: `{module}.{resource}.{action}` (see [[Patterns]])

### Marketing CMS

All content for the public marketing site — managed here, rendered at `/` routes:

| Resource | What it manages |
|---|---|
| Blog posts | Title, slug, body, category, SEO fields, draft/publish workflow |
| Blog categories | Name, slug, description |
| Changelog entries | Version, title, body, type (feature/fix/improvement/security), published_at |
| FAQ entries | Question, answer, category, sort_order |
| Help articles | Title, slug, body, category, SEO, display_order — serves `/help/{slug}` |
| Help categories | Name, slug, icon, nested parent_id — serves `/help` category nav |
| Contact submissions | Read-only — from public `/contact` form |
| Demo requests | Read-only lead pipeline — from public `/demo` form |
| Newsletter subscribers | Read-only email list |
| Open roles | Job listings for `/careers` page |
| Team members | About page bios, display_order |
| Testimonials | Social proof quotes, rating, featured flag |

### Dashboard Widgets

- `AdminStatsOverviewWidget` — total companies, tenants, users
- `RecentCompaniesWidget` — last 5 company sign-ups
- `MarketingStatsWidget` — blog post count, demo request count, newsletter subscriber count

---

## Workspace Panel (`/workspace`)

**Guard:** `tenant`  
**Access:** Every enabled tenant — regardless of which modules the company has activated

### Company Settings (`ManageCompany`)

- Edit company name, email, phone, website
- Change company logo (stored as File, served as signed URL via FileStorageService)
- Set timezone, locale, currency
- Toggle feature flags via the JSON `settings` column

### Team Management (`ManageTeam`)

- Invite new team members (creates `Tenant` row with `is_enabled = true`)
- Enable / disable existing members (blocks login without deleting)
- Assign Spatie roles to members
- View all team members with role badges

### API Keys (`ManageApiKeys`)

- Create named API keys (e.g. "Zapier Integration")
- Raw key shown exactly once at creation — stored as bcrypt hash + 8-char prefix
- Revoke keys (soft-delete — `deleted_at` set, scope filters via `withoutTrashed()`)
- Keys identified by `key_prefix` in the table

### Notification Preferences (`ManageNotificationPreferences`)

- Per-tenant per-channel (email / in-app) per-notification-type toggles
- E.g., "Don't send me email for LeaveApproved, but keep in-app on"

---

## HR Panel (`/hr`)

**Guard:** `tenant` · **Colour:** purple `#7C3AED`  
**Requires:** `hr` module activated on company

### Employee Profiles

- Full employee record: personal info, emergency contact, employment details
- `national_id_encrypted` stored with `encrypted` cast — never appears in logs or API responses
- Manager hierarchy: each employee can have a `manager_id` → self-referential BelongsTo
- Profile photo stored as `File`, served via signed URL
- Attached documents (EmployeeDocument) managed via `DocumentsRelationManager` on the edit page
- Custom field values (EmployeeCustomFieldValue) — workspace-defined extra fields

### Departments

- Create / edit departments
- Assign a department manager (points to an Employee, not a Tenant)
- Employees assigned to departments via `department_id` FK

### Leave Management

- Define leave types (name, paid/unpaid flag, accrual rate, carryover cap, approval required, calendar color)
- Submit leave requests (employee selects type, start/end date, half-day toggle, notes)
- `total_days` auto-calculated in `mutateFormDataBeforeCreate()` from date range
- Approve / reject with reason — approver stored as `approved_by` tenant_id + `approved_at` timestamp
- Leave balance tracking per employee per leave type per year (`LeaveBalance`)

### Onboarding

- Create reusable onboarding templates with ordered tasks (relation name: `tasks`, NOT `templateTasks`)
- Assign a template to a new employee → creates an `OnboardingFlow` (clones template tasks)
- Track task completion per employee (OnboardingTask `is_completed` + `completed_at`)
- Manager check-ins recorded as `OnboardingCheckin` + `OnboardingCheckinResponse`

### Payroll

- Configure payroll entities: company PAYE registration, bank account, tax reference — both bank + tax stored encrypted (`_encrypted` suffix, `encrypted` cast)
- Define reusable pay elements (earnings, deductions, NI, pension — each flagged for taxability/pensionability)
- Create pay runs: select payroll entity, pay period, payment date
- Add employees to a pay run → `PayRunEmployee` per employee + `PayRunLine` per pay element
- Approve pay run (logs approver + timestamp)
- Payslip PDF generation: `GeneratePayslipPdf` queued job (**stub** — needs PDF package) → stores as `File` → sets `Payslip.generated_at`
- Salary history per employee (`SalaryRecord`) — current salary = latest by `effective_date`
- Contractor payments (ad-hoc, separate from payroll)
- Employee deductions (recurring or one-off)

### Configuration (HR)

- Public holidays (per country, mandatory flag — used to exclude from leave day counts)
- Tax configurations (per country, per tax year — tax bands for payroll calculation)

---

## Projects Panel (`/projects`)

**Guard:** `tenant` · **Colour:** slate  
**Requires:** `projects` module activated on company

### Task Management

- Create tasks: title, description, priority, status, assignee, due date, start date, estimated hours
- Assignee dropdown scoped to `company_id` (Tenant has no global scope)
- Sub-task hierarchy: `parent_id` (self-referential) — unlimited depth, `parent()`/`children()` relations
- Labels: BelongsToMany `TaskLabel` via `task_label_assignments` pivot — color-coded tags
- Task dependencies: finish-to-start, start-to-start, etc. (`TaskDependency`)
- Recurring tasks: `is_recurring` flag + `recurrence_rule` (iCal RRULE string)
- Automation rules: `TaskAutomation` triggers action on task state change; `TaskAutomationLog` records execution

### Time Tracking

- Log time entries against tasks: `started_at`, `ended_at`, `duration_minutes`, `is_billable`
- Group into weekly timesheets (`Timesheet` by `week_start`/`week_end`)
- Submit timesheet for approval
- Manager approves/rejects with notes (`TimesheetApproval`)
- All time tracking uses `auth('tenant')` guard — not `auth()`

### Document Management

- Nested folder structure (self-referential `DocumentFolder.parent_folder_id`)
- Upload documents → stored as `File` record + `Document` record
- Version history: uploading a new version creates `DocumentVersion`, updates `Document.current_file_id`
- Star documents (`is_starred` flag)
- Tag documents (JSON array `tags`)
- Shareable links: `DocumentShare` with `share_token`, optional `password_hash` (hashed cast), `expires_at`
- File access always via `FileStorageService::temporaryUrl()` — never raw S3 path

### Labels

- Manage task labels (name + hex color)
- **Permissions:** `projects.task-labels.*` — NOT `projects.tasks.*` (separate resource)

---

## Finance Panel (`/finance`)

**Guard:** `tenant` · **Colour:** emerald `#059669`  
**Requires:** `finance` module activated on company

### Invoicing

- Create invoices with line items (description, qty, unit price, discount per line, tax rate)
- Status workflow: Draft → Sent → PartiallyPaid → Paid → Overdue → WrittenOff
- Record payments against invoice (`InvoicePayment` — partial payments supported, summed to `paid_amount`)
- Issue credit notes linked to invoice (`CreditNote` — 1:1 with Invoice)
- Recurring invoice templates (`RecurringInvoice`): frequency, `next_run_at`, `template_data` JSON — scheduler generates invoice at due date
- Email event tracking (`InvoiceEmailEvent`): sent/opened/bounced/clicked

### Expense Management

- Submit expenses: amount, currency, category, date, vendor, optional scanned receipt (File)
- Mileage expenses: `mileage_km` field, reimbursed at company's active `MileageRate`
- Group expenses into `ExpenseReport` for batch approval
- Approve / reject with reason (approver logged via `approved_by` tenant_id + `approved_at`)
- Reimbursement tracked via `ExpenseStatus.Reimbursed` state

### Configuration (Finance)

- Expense categories with GL codes (for accounting integration)
- Mileage rates per vehicle type (car/van/motorcycle/cycle)

### Financial Reporting

- Custom Filament page: `FinancialReporting`
- Widget: `FinancialSummaryWidget` — P&L summary, revenue, expenses, outstanding invoices
- Permission: `finance.reports.view`

---

## CRM Panel (`/crm`)

**Guard:** `tenant` · **Colour:** blue `#2563EB`  
**Requires:** `crm` module activated on company

### Contact Management

- Create contact records: person-level (CrmContact) and company-level (CrmCompany)
- Contact types: Lead / Prospect / Customer / Partner / Other
- Link contacts to CRM companies
- Owner assignment (team member dropdown scoped to `company_id`)
- Custom field values per contact (`CrmContactCustomFieldValue`)
- Activity timeline via `CrmActivity` (polymorphic — calls, emails, meetings, notes)

### CRM Companies

- Separate from workspace `Company` — these are external customer organisations
- Industry, employee count, domain, website, phone, email
- Contacts linked, deals linked, tickets linked

### Sales Pipeline

- Multiple pipelines per company (`Pipeline`)
- Configurable stages per pipeline with win probability (`DealStage`)
- Create deals: linked to contact + company, value, currency, expected close date
- Move deals through stages (stage dropdown filters to selected pipeline)
- Mark won (`DealStatus::Won`) or lost (`DealStatus::Lost`) with reason
- Deal notes (`DealNote`) — append-only notes by team members
- Events fired: `DealWon`, `DealLost`

### Customer Support (Helpdesk)

- Create tickets linked to CRM contact and company
- Status: Open → InProgress → PendingCustomer → Resolved → Closed
- Priority: Low / Normal / High / Urgent (**no Medium — use Normal**)
- Assign to team member (scoped dropdown)
- Message thread: `TicketMessage` with `is_internal` flag (internal = team-only)
- SLA rules: `TicketSlaRule` defines `first_response_hours` + `resolution_hours` per priority
- SLA breaches: `TicketSlaBreach` records when threshold exceeded (no SoftDeletes — permanent audit)
- CSAT surveys: `CsatSurvey` sent after resolution (unique token, optional expiry)
- CSAT responses: `CsatResponse` with 1-5 rating + comment

### Chatbot Rules

- Keyword-triggered auto-response rules (`ChatbotRule`)
- `trigger_keywords` stored as JSON array — Filament form uses CSV string input with `dehydrateStateUsing()` to split
- `response_body` sent when keyword matched in incoming ticket
- `sort_order` determines evaluation priority

### Canned Responses

- Pre-written reply templates (`CannedResponse`) categorised by topic
- Support agents insert into ticket message thread

### Shared Inbox (Partial — Phase 8)

- `SharedInbox` and `InboxEmail` models are wired with full field sets
- `EmailReceivedInSharedInbox` event stubbed
- **Full inbox UI deferred to Phase 8 (Communications module)**

---

## Marketing Site (`/`, public)

**Tech:** Inertia.js + Vue 3 (deviation from spec's Blade + Livewire — built this way intentionally)  
**Routes:** `routes/web.php`  
**Controller:** `app/Http/Controllers/Marketing/MarketingController.php`  
**Pages:** `resources/js/Pages/Marketing/`

| Page | Route | Vue File | Data passed |
|---|---|---|---|
| Homepage | `/` | `Welcome.vue` | domains (grouped modules), moduleCount, domainCount |
| Pricing | `/pricing` | `Marketing/Pricing.vue` | — |
| Features overview | `/features` | `Marketing/Features.vue` | domains |
| Module detail | `/modules/{key}` | `Marketing/Module.vue` | module (with sub_modules) |
| About | `/about` | `Marketing/About.vue` | team members |
| Blog index | `/blog` | `Marketing/Blog/Index.vue` | paginated posts |
| Blog post | `/blog/{slug}` | `Marketing/Blog/Post.vue` | post |
| Request demo | `/demo` | `Marketing/Demo.vue` | — |
| Help centre | `/help` | `Marketing/Help.vue` | categories (with articles) |
| Help article | `/help/{slug}` | `Marketing/HelpArticle.vue` | article (with category) |
| Changelog | `/changelog` | `Marketing/Changelog.vue` | changelog entries |
| Careers | `/careers` | `Marketing/Careers.vue` | open roles |
| Contact | `/contact` | `Marketing/Contact.vue` | — |
| Status | `/status` | `Marketing/Status.vue` | — |
| Privacy | `/legal/privacy` | `Marketing/Legal/Privacy.vue` | — |
| Terms | `/legal/terms` | `Marketing/Legal/Terms.vue` | — |
| Cookie policy | `/legal/cookies` | `Marketing/Legal/Cookies.vue` | — |
| DPA | `/legal/dpa` | `Marketing/Legal/Dpa.vue` | — |
| AUP | `/legal/aup` | `Marketing/Legal/Aup.vue` | — |
| Security | `/security` | `Marketing/Security.vue` | — |

Additional routes: `POST /contact` → `ContactController::store()`, `POST /demo` → `DemoController::store()`

---

## REST API (`/api/v1`)

**Spec:** `01 - Core Platform/API & Integrations Layer.md`  
**Auth:** `Authorization: Bearer {raw_api_key}` header  
**Middleware stack:** `AuthenticateApiKey` → `throttle:60,1`  
**All routes:** Read-only GET only. No write endpoints in v1.  
**Company context:** `$request->attributes->get('api_company')` — never `auth()->user()`

| Endpoint | Description | Returns |
|---|---|---|
| `GET /api/v1/health` | Platform health — unauthenticated, rate-limited 60/min | `{status, timestamp}` |
| `GET /api/v1/me` | Authenticated company + active modules | company + modules array |
| `GET /api/v1/modules` | All active modules for the company | module list |
| `GET /api/v1/hr/employees` | Paginated employee list | employees + meta |
| `GET /api/v1/hr/employees/{id}` | Single employee | employee |
| `GET /api/v1/hr/leave-requests` | Paginated leave requests | requests + meta |
| `GET /api/v1/hr/leave-requests/{id}` | Single leave request | request |
| `GET /api/v1/projects/tasks` | Paginated tasks | tasks + meta |
| `GET /api/v1/projects/tasks/{id}` | Single task | task |
| `GET /api/v1/projects/time-entries` | Paginated time entries | entries + meta |
| `GET /api/v1/projects/time-entries/{id}` | Single time entry | entry |
| `GET /api/v1/finance/invoices` | Paginated invoices | invoices + meta |
| `GET /api/v1/finance/invoices/{id}` | Single invoice | invoice |
| `GET /api/v1/finance/expenses` | Paginated expenses | expenses + meta |
| `GET /api/v1/finance/expenses/{id}` | Single expense | expense |
| `GET /api/v1/crm/contacts` | Paginated CRM contacts | contacts + meta |
| `GET /api/v1/crm/contacts/{id}` | Single contact | contact |
| `GET /api/v1/crm/deals` | Paginated deals | deals + meta |
| `GET /api/v1/crm/deals/{id}` | Single deal | deal |
| `GET /api/v1/crm/tickets` | Paginated tickets | tickets + meta |
| `GET /api/v1/crm/tickets/{id}` | Single ticket | ticket |

**Pagination meta:** All list endpoints return `{ data: [...], meta: { total, per_page, current_page, last_page } }`
