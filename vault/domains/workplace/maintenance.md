---
type: module
domain: Workplace & Facility
domain-key: workplace
panel: workplace
module-key: workplace.maintenance
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.files, core.notifications]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [states]
tables: [wp_maintenance_requests, wp_maintenance_schedules]
permission-prefix: workplace.maintenance
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Facility Maintenance

Report and track facility maintenance requests (broken AC, lighting, cleaning) with assignment and resolution.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, photos, assignment notifications |

---

## Core Features

- Maintenance request: location, category, description, priority, photo, reporter (any user)
- Category: HVAC, electrical, plumbing, cleaning, furniture, safety
- Status machine: `reported → assigned → in_progress → resolved → closed`
- Assignment to facility staff or external contractor (free-text contractor *(assumed)*)
- Priority + simple SLA (resolution target days per priority, overdue flag *(assumed)*)
- Photo attachments (before/after via Media Library)
- Recurring/preventive maintenance schedules → auto-create requests at `next_due_at`
- Maintenance history per location

---

## Data Model

### wp_maintenance_requests

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| location | string | |
| category | string | in set |
| description | text | |
| priority | string | urgent/high/normal/low |
| status | string default `reported` | state machine |
| reporter_id | ulid FK users | |
| assignee_id | ulid nullable | staff |
| contractor | string nullable | external |
| schedule_id | ulid nullable | preventive origin |
| resolved_at / closed_at | timestamp nullable | |
| deleted_at | timestamp nullable | |

### wp_maintenance_schedules — id, company_id (indexed), location, task, category, frequency (weekly/monthly/quarterly), next_due_at, is_active

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `reported` | `assigned` | `workplace.maintenance.assign` | assignee notified |
| `assigned` | `in_progress` | assignee | |
| `in_progress` | `resolved` | assignee | reporter notified; after photo prompt |
| `resolved` | `closed` | reporter confirm or auto 7d *(assumed)* | |
| any open | `reported` (reopen) | reporter | |

---

## DTOs

### ReportMaintenanceData — location (required), category (in set), description (required), priority, photos[]
### CreateScheduleData — location, task, category, frequency, next_due_at

## Services & Actions

- `AssignMaintenanceAction` / `ResolveMaintenanceAction`
- `RunMaintenanceSchedulesCommand` — due schedules → request + advance next_due_at (transactional)

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RunMaintenanceSchedulesCommand` | default | daily 06:00 | next_due_at advanced transactionally |
| Auto-close resolved | default | daily | 7d guard |

---

## Filament

**Nav group:** Maintenance

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `MaintenanceRequestResource` | #1 CRUD resource | queue tabs (open/assigned/overdue), assign/resolve actions |
| `MaintenanceScheduleResource` | #1 CRUD resource | preventive schedules |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('workplace.maintenance.view-any') && BillingService::hasModule('workplace.maintenance')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Upload contract** (medium): Add an upload note: restrict to image MIME types (jpg/png/webp), set a max size, and store under companies/{company_id}/maintenance/ for tenant isolation.

---

## Permissions

`workplace.maintenance.report` (all users) · `workplace.maintenance.assign` · `workplace.maintenance.resolve` · `workplace.maintenance.manage-schedules`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Reporter sees own requests; staff sees all
- [ ] Transitions per machine; resolve notifies reporter
- [ ] Schedule run creates request once + advances next_due_at
- [ ] Overdue flag per priority SLA
- [ ] Photos tenant-scoped

---

## Build Manifest

```
database/migrations/xxxx_create_wp_maintenance_requests_table.php
database/migrations/xxxx_create_wp_maintenance_schedules_table.php
app/Models/Workplace/{MaintenanceRequest,MaintenanceSchedule}.php
app/States/Workplace/MaintenanceRequest/{MaintenanceState,Reported,Assigned,InProgress,Resolved,Closed}.php
app/Data/Workplace/{ReportMaintenanceData,CreateScheduleData}.php
app/Actions/Workplace/{AssignMaintenanceAction,ResolveMaintenanceAction}.php
app/Console/Commands/Workplace/RunMaintenanceSchedulesCommand.php
app/Filament/Workplace/Resources/{MaintenanceRequestResource,MaintenanceScheduleResource}.php
database/factories/Workplace/MaintenanceRequestFactory.php
tests/Feature/Workplace/MaintenanceTest.php
```

---

## Related

- [[domains/workplace/room-booking]]
- [[architecture/patterns/states]]
