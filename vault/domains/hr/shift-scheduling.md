---
type: module
domain: HR & People
panel: hr
module-key: hr.shifts
status: planned
color: "#4ADE80"
---

# Shift Scheduling

> Shift creation, assignment, swap requests, and published schedules — weekly schedule management for shift-based and flexible workforces.

**Panel:** `hr`
**Module key:** `hr.shifts`

## What It Does

Shift Scheduling is designed for companies with shift-based operations — retail, logistics, healthcare, hospitality, manufacturing. Managers create shift templates and build weekly schedules by assigning employees to shifts. Published schedules are visible to employees via Self-Service. Employees can request shift swaps with a colleague; the manager approves the swap. Absence filling alerts notify available employees when a shift becomes vacant. Scheduled hours feed into Time & Attendance for payroll calculation.

## Features

### Core
- Shift templates: reusable shift definitions (e.g. "Morning 07:00–15:00", "Evening 15:00–23:00") with location and role
- Weekly schedule builder: drag-and-drop grid — rows are employees, columns are days, cells are assigned shifts
- Schedule publish: draft schedule invisible to employees until manager publishes it
- Published schedule visible to employees in Self-Service portal
- Shift assignment: one employee per shift (or multiple employees per shared shift)

### Advanced
- Shift swap requests: employee requests a swap with a named colleague — both parties accept, then manager approves
- Vacancy filling: when an employee calls in sick, manager publishes a vacancy notification — employees who match the shift role can claim it
- Overtime alert: scheduling engine warns when an employee would exceed their contracted weekly hours if assigned a shift
- Minimum rest period: configurable minimum hours between consecutive shifts — violation flagged during schedule building
- Schedule templates: save a full-week schedule as a template and reapply in future weeks

### AI-Powered
- Auto-scheduling: AI proposes a full-week schedule based on employee availability, contracted hours, role requirements, and leave records — manager reviews and adjusts before publishing
- Demand-based scheduling: if integrated with Sales or Operations data, AI adjusts suggested staffing levels based on expected demand (e.g. busier Saturday forecast → more staff)

## Data Model

```erDiagram
    shift_templates {
        ulid id PK
        ulid company_id FK
        string name
        time start_time
        time end_time
        string location
        string role
        timestamps created_at/updated_at
    }

    scheduled_shifts {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        ulid shift_template_id FK
        date shift_date
        time start_time
        time end_time
        string status
        timestamps created_at/updated_at
    }

    shift_swap_requests {
        ulid id PK
        ulid scheduled_shift_id FK
        ulid requesting_employee_id FK
        ulid target_employee_id FK
        string status
        ulid approved_by FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `scheduled_shifts.status` | draft / published / cancelled |
| `shift_swap_requests.status` | pending / accepted / rejected / approved |

## Permissions

- `hr.shifts.view-own`
- `hr.shifts.view-team-schedule`
- `hr.shifts.manage-schedule`
- `hr.shifts.approve-swap`
- `hr.shifts.publish-schedule`

## Filament

- **Resource:** `ShiftTemplateResource`
- **Pages:** `ListShiftTemplates`
- **Custom pages:** `ShiftSchedulePage` — weekly grid schedule builder with drag-and-drop
- **Widgets:** `WeeklyScheduleWidget` — current week assignment summary on HR dashboard
- **Nav group:** Leave (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Deputy | Shift scheduling and workforce management |
| When I Work | Employee scheduling |
| Rotaready | Shift management and scheduling |
| Humanity (Shiftboard) | Workforce scheduling |

## Implementation Notes

**Filament:** `ShiftSchedulePage` is the core custom `Page` — a weekly grid where rows are employees and columns are days (Mon–Sun). This is not achievable with standard Filament tables. The grid is a Livewire component rendered as an HTML `<table>` with cells as Livewire-driven dropdowns (click cell → select shift template → assign). Drag-and-drop (move an assigned shift from one cell to another) uses SortableJS or a custom Alpine.js drag handler posting to `Livewire::dispatch('shiftMoved', ...)`. The grid must show the current week by default with prev/next week navigation.

**Real-time:** Reverb broadcasting is beneficial for teams where multiple managers co-build a schedule. Broadcast `ShiftAssigned` and `ShiftRemoved` events on `schedule.{company_id}.{week_start}` private channel. For MVP, optimistic local updates without broadcasting is acceptable.

**Notifications to employees:** When a schedule is published, all affected employees are notified via `SchedulePublishedNotification` — dispatched as a queued job that fans out one notification per employee. Employees with the `hr.shifts.view-own` permission see their assigned shifts in the employee self-service portal.

**Overtime alert:** The scheduling engine checks `contracted_hours_per_week` on the employee record (from `employee-profiles` module) against the sum of shift durations for the week. If the new assignment would exceed contracted hours, a Livewire validation error is shown inline on the grid cell before the assignment is saved.

**AI auto-scheduling:** Calls `app/Services/AI/ShiftScheduleService.php` with a structured input: employee list, their availabilities, contracted hours, leave records, and shift template requirements. The service calls OpenAI GPT-4o with a prompt returning a JSON assignment map `{employee_id: [{shift_template_id, date}]}`. The AI response is previewed by the manager before confirmation — it does not auto-save.

**Missing from data model:** Employee availability is not captured in the data model. Add `employee_availability {ulid id, ulid employee_id, ulid company_id, string day_of_week, time available_from, time available_until, boolean is_recurring}` — required for both the auto-scheduling AI input and the vacancy filling feature (only show available employees for a vacancy).

## Related

- [[employee-profiles]]
- [[time-attendance]]
- [[leave-management]]
- [[employee-self-service]]
