---
domain: hr
module: shift-scheduling
feature: shift-calendar
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Shift Calendar

## Purpose

Weekly per-team schedule view and the entry point for creating, publishing, and copying shifts.

## Intended Behavior

- `ShiftSchedulePage` (Filament custom page, ui-strategy #4) renders a fullcalendar week view via `saade/filament-fullcalendar`.
- Drag-drop assignment, coverage-gap highlighting, 30s polling for near-live updates.
- Header actions: **Publish week** (`publishWeek`) and **Copy previous week** (`copyWeek`).

## Tables / Permissions / Events

- Tables: `hr_shifts`
- Permissions: `hr.shifts.view-any`, `hr.shifts.publish`
- Events: publish notifies assigned employees (via `core.notifications`)

## UI

- **Kind**: custom-page (calendar / roster grid, `saade/filament-fullcalendar`)
- **Page**: "Shift Schedule" (`/hr/shift-schedule`)
- **Layout**: `ShiftSchedulePage` weekly per-team fullcalendar; shifts as events by role/employee; coverage gaps highlighted; header actions **Publish week** and **Copy previous week**. 30s polling for near-live updates.
- **Key interactions**: drag-drop to assign/move shifts; publish the week (`publishWeek`, notifies assigned employees); copy previous week (`copyWeek`).
- **States**: empty ("No shifts this week — create or copy last week") · loading (calendar skeleton) · error (toast on publish/conflict failure) · selected (shift event popover with assign/edit).
- **Gating**: visible with `hr.shifts.view-any`; publish requires `hr.shifts.publish`. Custom page declares `canAccess()`.

## Data

- Owns / writes: `hr_shifts`
- Reads: reads `hr_employees` via EmployeeService (assignees); reads approved leave via LeaveService for gap/conflict rendering
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none *(leave conflicts handled in [[leave-conflict-blocking]])*
- Feeds: none (publish notifications go via `core.notifications`, not a domain event)
- Shared entity: `hr_employees` (read via EmployeeService)

## Test Checklist

### Unit
- [ ] Coverage-gap highlight list = shifts with null `employee_id` for the visible week
- [ ] `copyWeek` produces draft shifts in the target week

### Feature (Pest)
- [ ] `publishWeek` flips drafts → published and notifies assigned employees
- [ ] `copyWeek` skips employees on leave in the target week; company A cannot view company B schedules

### Livewire
- [ ] `ShiftSchedulePage` `canAccess()` gated by permission + `hasModule('hr.shifts')`
- [ ] Publish action requires `hr.shifts.publish`; drag-drop assign writes the shift

## Related

- [[../_module]] · [[../architecture]]
