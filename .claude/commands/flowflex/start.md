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

Read `vault/domains/{domain}/{module-name}.md` in full. v2 specs ([[_meta/spec-template]] format) carry everything you need: Dependencies, Data Model, State Machine, DTOs, Services & Actions, Events, Filament, Permissions, Test Checklist, Build Manifest.

### Step 3 — Check dependencies (v2 frontmatter)

Read the spec's `depends-on:` list. For each hard dependency, look up its `status:` (its spec frontmatter or `_meta/module-graph.md`).
- Any hard dep not `complete` → **warn prominently** in the briefing: `⚠️ Hard dependency {key} is {status}` and list what's blocked.
- `soft-depends` deps that aren't built: note the degraded behavior from the spec's `## Dependencies` table.

### Step 4 — Read the domain index + load architecture patterns

Read `vault/domains/{domain}/_index.md` (nav groups, sibling modules, intra-domain dependency graph).

**Always read:**
- `vault/architecture/filament-patterns.md`
- `vault/architecture/ui-strategy.md`
- `vault/architecture/multi-tenancy.md`
- `vault/architecture/patterns/belongs-to-company.md`
- `vault/architecture/patterns/dto-pattern.md`
- `vault/architecture/patterns/testing-pattern.md`
- `vault/architecture/module-system.md`

**Then read the files listed in the spec's `patterns:` frontmatter** — map each key through the lookup table in `patterns.md` (e.g. `states` → `vault/architecture/patterns/states.md`).

**Legacy fallback** (spec has no `patterns:` field — not yet migrated to v2): infer from the spec body:

| Module has… | Read |
|---|---|
| Status field with transitions | `vault/architecture/patterns/states.md` |
| Custom Filament page (board, calendar, dashboard) | `vault/architecture/patterns/custom-pages.md` |
| Multi-method domain service | `vault/architecture/patterns/interface-service.md` |
| Single-step simple operation | `vault/architecture/patterns/actions-pattern.md` |
| Fires/consumes cross-domain events | `vault/architecture/event-bus.md` |
| File uploads | File upload section of `vault/architecture/security.md` |
| Encrypted fields | `vault/architecture/patterns/encryption.md` |
| Full-text search | `vault/architecture/search.md` |
| Real-time updates | `vault/architecture/websockets.md` |
| PDF generation | `vault/architecture/packages.md` (spatie/laravel-pdf) |
| Background jobs / scheduled tasks | `vault/architecture/queue-jobs.md` |
| Money arithmetic | `vault/architecture/packages.md` (brick/money) |
| Outbound emails | `vault/architecture/email.md` |

If the module fires or consumes events (`fires-events:`/`consumes-events:` non-empty), always read `vault/architecture/event-bus.md` — payloads must match its listener contracts exactly.

### Step 5 — Check open gaps

Read `vault/build/gaps/INDEX.md`. Filter rows where `discovered-in` contains this domain or module key. List any open gaps that affect this build.

### Step 6 — Permissions

v2 spec: copy the full key list from the spec's `## Permissions` section.
Legacy spec: read `vault/architecture/domain-panels.md`, find this domain's section, show the permissions list.

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

### Dependencies
{hard deps + status; ⚠️ warnings for incomplete hard deps; soft deps + degraded behavior}

### Data Model
{tables to create, with key columns; 🔐 encrypted columns flagged}

### Files to Create
{v2 spec: copy the spec's ## Build Manifest verbatim — do NOT regenerate it.
Legacy spec: generate from the standard app/ layout:
migrations → Models/{Domain}/ → States/{Domain}/{Model}/ → Data/{Domain}/ →
Services|Actions/{Domain}/ (+Contracts+Providers if Interface→Service) →
Events/{Domain}/ → Filament/{Domain}/{Resources|Pages|Widgets}/ →
Mail/{Domain}/ → database/factories/{Domain}/ → tests/Feature/{Domain}/}

### UI Artifacts
{from spec ## Filament — artifact + ui-strategy row # + realtime choice}

### Patterns to Follow
{the spec's patterns: list (or legacy inference), one line each on why}

### Permissions to Register (in PermissionSeeder)
{v2: from spec ## Permissions; legacy: from domain-panels.md}

### Cross-Domain Events
{fires: / consumes: with payload source = event-bus.md contracts}

### Test Checklist
{v2 spec: copy ## Test Checklist; legacy: tenant-isolation + module-gating + inferred feature cases}

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
