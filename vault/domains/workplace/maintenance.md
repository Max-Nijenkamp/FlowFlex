---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.maintenance
status: planned
color: "#4ADE80"
---

# Facility Maintenance

Report and track facility maintenance requests (broken AC, lighting, cleaning) with assignment and resolution.

## Core Features

- Maintenance request: location, category, description, priority, photo, reporter
- Category: HVAC, electrical, plumbing, cleaning, furniture, safety
- Status machine: `reported → assigned → in_progress → resolved → closed`
- Assignment to facility staff or external contractor
- Priority and SLA
- Photo attachments (before/after)
- Recurring/preventive maintenance schedules
- Maintenance history per location/asset

## Data Model

| Table | Key Columns |
|---|---|
| `wp_maintenance_requests` | company_id, location, category, description, priority, status, reporter_id, assignee_id, resolved_at |
| `wp_maintenance_schedules` | company_id, location, task, frequency, next_due_at |

## Filament

**Nav group:** Maintenance

- `MaintenanceRequestResource` — report, assign, resolve
- `MaintenanceScheduleResource` — preventive maintenance schedules
- Maintenance queue view

## Related

- [[domains/workplace/room-booking]]
- [[architecture/patterns/states]]
