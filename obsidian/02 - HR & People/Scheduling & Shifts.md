---
tags: [flowflex, domain/hr, scheduling, shifts, phase/5]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-06
---

# Scheduling & Shifts

Shift planning for hourly workers, retail, hospitality, and any team with rotas. Connected directly to payroll and time tracking.

**Who uses it:** Shift managers, operations managers, employees
**Filament Panel:** `hr` or `operations` (configurable)
**Depends on:** [[Employee Profiles]]
**Phase:** 5
**Build complexity:** High — 2 resources, 2 pages, 5 tables

## Events Fired

- `ShiftPublished`
- `ShiftSwapRequested`
- `ShiftSwapApproved`
- `ClockIn`
- `ClockOut` → consumed by [[Time Tracking]] (creates time entry automatically)

## Events Consumed

- `LeaveApproved` (from [[Leave Management]]) → removes employee from rota for leave dates
- `EmployeeHired` (from [[Recruitment & ATS]]) → adds employee to scheduling system

## Features

- Drag-and-drop shift builder (week/fortnight view)
- Employee availability capture (employees submit their availability windows)
- Shift templates (recurring weekly rota saved as template)
- Skill-based scheduling (only show employees qualified for a shift type)
- Minimum rest time enforcement (e.g. 11 hours between shifts)
- Maximum hours per week warning (overtime threshold alerts)
- Shift swap requests (employee requests swap → manager approves)
- Rota publishing (employees notified when their schedule is published)
- Open shift posting (post an uncovered shift for eligible employees to claim)
- Clock-in / clock-out (mobile app with GPS location verification option)
- POS integration for clock-in (if Operations/POS module active)
- Attendance reporting (who was scheduled vs who actually showed up)

## Database Tables (5)

1. `shifts` — shift definitions (who, when, where, role)
2. `shift_templates` — reusable rota templates
3. `employee_availability` — availability declarations per employee
4. `shift_swaps` — swap request records
5. `attendance_records` — clock-in/out logs

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Time Tracking]]
- [[Payroll]]
- [[Point of Sale]]
