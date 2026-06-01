# /flowflex:start

Pre-build briefing. Run before writing any code for a module. Reads the spec, loads relevant patterns, checks open gaps, sets status to in-progress, outputs a build plan.

## Usage

```
/flowflex:start hr.leave
/flowflex:start finance.invoicing
/flowflex:start core.billing
```

## What This Does

### Step 1 — Parse module key

Split the module key on `.` to get domain and module name.
- `hr.leave` → domain: `hr`, module file: `leave-management.md`
- `finance.invoicing` → domain: `finance`, module file: `invoicing.md`

The module file name is the module key suffix converted to kebab-case with `-` expanded where needed. If the file doesn't match exactly, scan `vault/domains/{domain}/` for the file whose `module-key` frontmatter matches.

### Step 2 — Read the module spec

Read `vault/domains/{domain}/{module-name}.md` in full. Extract:
- What It Does (summary)
- Core Features (full list)
- Data Model (all tables + ERD if present)
- Filament section (resources, custom pages, widgets)
- Related links

### Step 3 — Read the domain index

Read `vault/domains/{domain}/_index.md`. Extract:
- Navigation groups for this domain's panel
- Other modules in the domain (for context — what comes before/after this module)
- Key patterns referenced

### Step 4 — Load relevant architecture patterns

Read these files based on what the module needs:

**Always read:**
- `vault/architecture/filament-patterns.md`
- `vault/architecture/multi-tenancy.md`
- `vault/architecture/patterns/belongs-to-company.md`
- `vault/architecture/patterns/dto-pattern.md`
- `vault/architecture/patterns/testing-pattern.md`
- `vault/architecture/module-system.md`

**Read if the module has these features:**

| Module has… | Read |
|---|---|
| Status field with transitions (leave status, invoice status, deal stage) | `vault/architecture/patterns/states.md` |
| Custom Filament page (board, calendar, dashboard) | `vault/architecture/patterns/custom-pages.md` |
| Multi-method domain service (EmployeeService, InvoiceService) | `vault/architecture/patterns/interface-service.md` |
| Single-step simple operation | `vault/architecture/patterns/actions-pattern.md` |
| Fires events consumed by other domains | `vault/architecture/event-bus.md` |
| File uploads or document storage | File upload section of `vault/architecture/security.md` |
| Encrypted fields (national ID, salary, IBAN) | `vault/architecture/patterns/encryption.md` |
| Full-text search | `vault/architecture/search.md` |
| Real-time notifications or live UI updates | `vault/architecture/websockets.md` |
| PDF generation (invoices, payslips, quotes) | `vault/architecture/packages.md` (spatie/laravel-pdf section) |
| Background jobs or scheduled tasks | `vault/architecture/queue-jobs.md` |
| Money arithmetic | `vault/architecture/packages.md` (brick/money section) |
| Outbound emails | `vault/architecture/email.md` |
| Sensitive data | `vault/architecture/patterns/encryption.md` |

### Step 5 — Check open gaps

Read `vault/build/gaps/INDEX.md`. Filter rows where `discovered-in` contains this domain or module key. List any open gaps that affect this build.

### Step 6 — Check domain permissions

Read `vault/architecture/domain-panels.md`. Find the section for this domain. Show the relevant permissions list.

### Step 7 — Set status to in-progress

Update `vault/domains/{domain}/{module-name}.md` frontmatter:
```yaml
status: in-progress
```

### Step 8 — Output the build briefing

Format:

```
## Build Briefing: {module-key}

### What to Build
{2-sentence summary from spec}

### Data Model
{tables to create, with key columns}

### Files to Create
**Migrations:**
- database/migrations/{timestamp}_create_{table}_table.php

**Models:**
- app/Models/{Domain}/{ModelName}.php

**States (if applicable):**
- app/States/{Domain}/{Model}/{Abstract}.php
- app/States/{Domain}/{Model}/{State}.php (one per state)

**Service or Actions:**
- app/Contracts/{Domain}/{Service}Interface.php (if Interface→Service)
- app/Services/{Domain}/{Service}.php
- app/Providers/{Domain}/{Domain}ServiceProvider.php
  OR
- app/Actions/{Domain}/{ActionName}.php (if simple action)

**DTOs:**
- app/Data/{Domain}/{Model}Data.php (output)
- app/Data/{Domain}/Create{Model}Data.php (input)

**Events (if cross-domain):**
- app/Events/{Domain}/{EventName}.php

**Filament:**
- app/Filament/{Domain}/Resources/{Model}Resource.php (CRUD)
  OR
- app/Filament/{Domain}/Pages/{PageName}Page.php (custom)
- app/Filament/{Domain}/Widgets/{Widget}Widget.php (if any)

**Mail (if emails):**
- app/Mail/{Domain}/{Name}Mail.php

**Tests:**
- tests/Feature/{Domain}/{Module}Test.php

### Patterns to Follow
{list only the patterns relevant to this module, from Step 4}

### Permissions to Register (in PermissionSeeder)
{list from domain-panels.md}

### Cross-Domain Events
{fires: / consumes:}

### Open Gaps
{list from Step 5, or "None open"}
```

---

## Example Output: `/flowflex:start hr.leave`

```
## Build Briefing: hr.leave

### What to Build
Leave request submission, multi-level approval workflow, leave balance tracking,
and a team calendar view. Employees submit via self-service; managers approve in /hr.

### Data Model
Tables to create:
- hr_leave_types (company_id, name, color, accrual_days_per_year, carry_over_days, requires_approval)
- hr_leave_balances (company_id, employee_id, leave_type_id, year, allocated_days, taken_days, pending_days)
- hr_leave_requests (company_id, employee_id, leave_type_id, start_date, end_date, days_requested, status, approved_by, approved_at)

### Patterns to Follow
- states.md — status machine: draft → submitted → approved | rejected | cancelled
- interface-service.md — LeaveService (multi-method: submit, approve, reject, balance calc)
- event-bus.md — fires LeaveRequestApproved (consumed by Payroll, Scheduling)
- email.md — LeaveApprovedMail, LeaveRejectedMail
- custom-pages.md — LeaveCalendarPage (saade/filament-fullcalendar)

### Permissions to Register
hr.leave.view-any, hr.leave.view, hr.leave.create, hr.leave.approve, hr.leave.reject

### Cross-Domain Events
Fires: LeaveRequestApproved (company_id, leave_request_id, employee_id, dates)
Consumed by: HR Payroll (deduction), HR Shift Scheduling

### Open Gaps
None open
```
