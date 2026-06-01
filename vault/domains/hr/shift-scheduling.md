---
type: module
domain: HR & People
panel: hr
module-key: hr.shifts
status: planned
color: "#4ADE80"
---

# Shift Scheduling

Shift creation, team schedule publishing, swap requests, and coverage gap detection. For companies with rotating shifts or hourly workers.

---

## Core Features

- Shift creation: start time, end time, role/position, employee assignment
- Weekly schedule view per team (calendar layout via `saade/filament-fullcalendar`)
- Schedule publishing: draft → published; employees notified on publish
- Swap requests: employee requests to swap shift with a colleague; manager approves
- Coverage gap detection: warn when a shift has no assigned employee
- Copy previous week's schedule to reduce weekly setup time

---

## Data Model

| Table | Key Columns |
|---|---|
| `hr_shifts` | company_id, employee_id, date, start_time, end_time, role, status (draft/published/cancelled) |
| `hr_shift_swap_requests` | company_id, requester_id, recipient_id, shift_id, status, manager_approved_at |

---

## Filament

- `ShiftSchedulePage` (custom page) — weekly calendar view, drag-and-drop assignment
- `ShiftSwapRequestResource` — list and approve swap requests

---

## Related

- [[domains/hr/time-attendance]]
- [[domains/hr/employee-profiles]]
