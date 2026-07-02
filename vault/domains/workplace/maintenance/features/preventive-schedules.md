---
domain: workplace
module: maintenance
feature: preventive-schedules
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Preventive Schedules

Recurring maintenance tasks that auto-create requests when due.

## Behaviour

- A schedule defines location, task, category, `frequency` (weekly/monthly/quarterly), `next_due_at`, `is_active`.
- `RunMaintenanceSchedulesCommand` (daily 06:00) turns due schedules into `reported` requests and advances `next_due_at` transactionally.
- Exactly one request per due date (idempotent); generated requests carry `schedule_id`.
- Maintenance history per location combines reactive + preventive requests.

## UI

- **Kind**: simple-resource
- **Page**: `MaintenanceScheduleResource` list/form at `/workplace/maintenance/schedules`.
- **Layout**: table (location, task, frequency, next due, active); form for the schedule fields.
- **Key interactions**: create/edit schedule; toggle active; next-due shown; generated requests link back via `schedule_id`.
- **States**: empty (no schedules → "add a preventive task" CTA) · loading (skeleton) · error (toast) · selected (row → edit).
- **Gating**: `workplace.maintenance.manage-schedules`.

## Data

- Owns / writes: `wp_maintenance_schedules` + `wp_maintenance_requests` (generated) — both own module.
- Reads: nothing cross-domain.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: preventive vs reactive split read by [[../../workplace-analytics/_module|Workplace Analytics]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] `next_due_at` advance per frequency (weekly/monthly/quarterly)

### Feature (Pest)
- [ ] Daily command creates exactly one request per due schedule (re-run idempotent, cursor transactional)
- [ ] Inactive schedule skipped; generated request carries `schedule_id`

### Livewire
- [ ] Schedule form validates; active toggle works
- [ ] Denied without `workplace.maintenance.manage-schedules`

## Related

- [[../_module|Facility Maintenance]] · [[report-request]] · [[../architecture]]
