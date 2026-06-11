---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.shifts
status: complete
priority: v1
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [hr.time, hr.leave]
fires-events: []
consumes-events: [LeaveRequestApproved]
patterns: [custom-pages, events]
tables: [hr_shifts, hr_shift_swap_requests]
permission-prefix: hr.shifts
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Shift Scheduling

Shift creation, team schedule publishing, swap requests, and coverage gap detection. For companies with rotating shifts or hourly workers.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | shifts assigned to employees |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, publish notifications |
| Soft | [[domains/hr/leave-management\|hr.leave]] | consumes `LeaveRequestApproved` to block scheduling over leave |
| Soft | [[domains/hr/time-attendance\|hr.time]] | planned-vs-actual comparison |

---

## Core Features

- Shift creation: start time, end time, role/position, employee assignment
- Weekly schedule view per team (calendar layout via `saade/filament-fullcalendar`)
- Schedule publishing: draft → published; employees notified on publish
- Swap requests: employee requests to swap shift with a colleague; manager approves
- Coverage gap detection: warn when a shift has no assigned employee
- Copy previous week's schedule to reduce weekly setup time
- Leave blocking: approved leave makes the employee unassignable for the range

---

## Data Model

### hr_shifts

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| employee_id | ulid | nullable FK | null = unassigned (coverage gap) |
| date | date | not null | |
| start_time / end_time | time | end after start (overnight: end_next_day flag *(assumed)*) | |
| role | string | not null | position label |
| status | string | default `draft` | draft / published / cancelled |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, date, status)`, `(company_id, employee_id, date)`

### hr_shift_swap_requests

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| requester_id / recipient_id | ulid FK hr_employees | |
| shift_id | ulid FK | |
| status | string default `pending` | pending / accepted / approved / declined |
| manager_approved_at | timestamp nullable | |

Flow: requester asks → recipient accepts → manager approves → shifts swap *(assumed: simple status field, no spatie states — linear flow)*.

---

## DTOs

### CreateShiftData — date (required), start_time/end_time (required, valid span), role (required), employee_id (nullable; if set: no overlap with employee's other shifts, no approved leave on date)
### RequestSwapData — shift_id (own shift), recipient_id (different employee, no conflict on that date)

Messages: "This employee is on approved leave that day." · "This employee already has a shift overlapping this time."

## Services & Actions

Interface→Service: `ShiftServiceInterface` → `ShiftService`.

- `createShift(CreateShiftData $data): ShiftData` — throws `ShiftConflictException`, `EmployeeOnLeaveException`
- `publishWeek(CarbonImmutable $weekStart): int` — drafts → published, notifies assigned employees
- `copyWeek(CarbonImmutable $from, CarbonImmutable $to): int` — copies as drafts, skips employees with leave
- `requestSwap(RequestSwapData $data)` / `acceptSwap(...)` / `approveSwap(...)` — final approval reassigns shifts
- `coverageGaps(CarbonImmutable $weekStart): Collection<ShiftData>` — unassigned shifts

## Events

### Consumes: LeaveRequestApproved (from hr.leave)
Listener: `BlockShiftsOnLeaveListener` — queued, `WithCompanyContext`; unassigns the employee from published/draft shifts in the range + flags gaps + notifies manager (per [[architecture/event-bus]]).

---

## Filament

**Nav group:** Leave

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ShiftSchedulePage` | #4 Calendar custom page | fullcalendar week view, drag-drop assignment, gap highlighting, publish + copy-week actions; polling 30s |
| `ShiftSwapRequestResource` | #1 CRUD resource | pending swaps, approve action |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('hr.shifts.view-any') && BillingService::hasModule('hr.shifts')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`hr.shifts.view-any` · `hr.shifts.view` · `hr.shifts.create` · `hr.shifts.update` · `hr.shifts.publish` · `hr.shifts.request-swap` · `hr.shifts.approve-swap`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Overlapping shift for same employee rejected
- [ ] Assignment over approved leave rejected; LeaveRequestApproved unassigns existing
- [ ] Publish notifies only assigned employees of that week
- [ ] Copy-week skips employees on leave
- [ ] Swap full flow: request → accept → approve → shifts swapped
- [ ] Coverage gaps lists unassigned shifts

---

## Build Manifest

```
database/migrations/xxxx_create_hr_shifts_table.php
database/migrations/xxxx_create_hr_shift_swap_requests_table.php
app/Models/HR/{Shift,ShiftSwapRequest}.php
app/Data/HR/{CreateShiftData,RequestSwapData,ShiftData}.php
app/Contracts/HR/ShiftServiceInterface.php
app/Services/HR/ShiftService.php
app/Exceptions/HR/{ShiftConflictException,EmployeeOnLeaveException}.php
app/Listeners/HR/BlockShiftsOnLeaveListener.php
app/Filament/HR/Pages/ShiftSchedulePage.php
app/Filament/HR/Resources/ShiftSwapRequestResource.php
database/factories/HR/{ShiftFactory,ShiftSwapRequestFactory}.php
tests/Feature/HR/{ShiftSchedulingTest,ShiftSwapTest,LeaveBlockingTest}.php
```

---

## Related

- [[domains/hr/time-attendance]]
- [[domains/hr/leave-management]]
- [[domains/hr/employee-profiles]]
- [[architecture/event-bus]]
