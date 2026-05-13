---
type: module
domain: Projects & Work
panel: projects
module-key: projects.time
status: planned
color: "#4ADE80"
---

# Time Tracking

> Time entries logged against tasks and projects — timesheets, billable vs non-billable hours, and project cost reporting.

**Panel:** `projects`
**Module key:** `projects.time`

## What It Does

Time Tracking lets team members log time against specific tasks or projects. Each time entry records who worked, on which task or project, for how long, and whether the time is billable. Weekly timesheets aggregate entries for manager approval. Project time reports show total hours logged per task, per person, and per project — enabling project cost calculations (hours × hourly rate) and client billing (billable hours for professional services companies). Time data from this module is separate from HR Time & Attendance — this module tracks project work, not employment attendance.

## Features

### Core
- Time entry: log hours against a task or project with date, description, duration (decimal hours), and billable flag
- Timer: optional live timer — start when beginning work, stop when done — creates a time entry automatically
- Timesheet view: employee sees their own entries grouped by week — submit for manager approval at week end
- Billable flag: mark time entries as billable or non-billable — billable entries feed into Finance invoicing for client billing
- Task total: hours logged against a task shown on task detail (actual vs estimate)

### Advanced
- Hourly rates: per-employee billable rate stored on employee record — used for automatic project cost calculation
- Project cost report: total hours × rate per person per project — shows against project budget
- Client billing export: list of billable time entries in a date range, formatted for invoice input in Finance Invoicing module
- Approval workflow: weekly timesheet submitted by employee → approved or rejected by manager
- Time budget: project-level time budget (hours) — progress bar shown on project dashboard as hours are logged

### AI-Powered
- Missing time alerts: AI detects team members who have not logged time for more than 2 business days and sends a gentle reminder
- Time vs estimate analysis: when actual hours significantly exceed estimate on a task type, AI suggests adjusting estimate templates for that task category

## Data Model

```erDiagram
    proj_time_entries {
        ulid id PK
        ulid company_id FK
        ulid task_id FK
        ulid project_id FK
        ulid user_id FK
        date entry_date
        decimal hours
        string description
        boolean is_billable
        decimal hourly_rate
        string status
        ulid timesheet_id FK
        timestamps created_at/updated_at
    }

    proj_timesheets {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        date week_start
        string status
        ulid approved_by FK
        timestamp approved_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `is_billable` | True for client-facing professional services time |
| `hourly_rate` | Copied from employee record at entry time |
| `status` | draft / submitted / approved / rejected |

## Permissions

- `projects.time.log-own`
- `projects.time.view-own`
- `projects.time.view-team`
- `projects.time.approve-timesheets`
- `projects.time.view-reports`

## Filament

- **Resource:** `TimeEntryResource`, `TimesheetResource`
- **Pages:** `ListTimeEntries`, `ListTimesheets`, `ViewTimesheet`
- **Custom pages:** `ProjectTimeReportPage` — hours by person and task for a project
- **Widgets:** `WeeklyHoursWidget` — current user's hours this week vs last week on dashboard
- **Nav group:** Resources (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Harvest | Time tracking for projects |
| Toggl Track | Project time logging |
| Clockify | Team time tracking |
| Timely | Automatic time tracking |

## Implementation Notes

**Filament:** `TimeEntryResource` and `TimesheetResource` are standard Resources. `ProjectTimeReportPage` is a custom `Page` that renders aggregated hours data as a read-only table and summary cards — not a standard ListRecords. `ViewTimesheet` uses a standard Filament `RelationManager` to show the timesheet's entries inline.

**Real-time timer:** The live timer widget on the time entry create form requires client-side JavaScript. Alpine.js maintains a `startedAt` timestamp in `localStorage` and ticks a displayed duration counter every second. When the user clicks Stop, it computes the elapsed decimal hours and submits the Livewire `createEntry` action with the calculated duration. The timer state must survive page refreshes — store `{taskId, projectId, startedAt}` in `localStorage` keyed by `user_id` so the timer resumes correctly if the browser is refreshed. No Reverb required.

**Background jobs:** `WeeklyHoursWidget` aggregates time entries for the current user. This query runs per page load — add a `Cache::remember()` with a 15-minute TTL keyed on `user.{id}.weekly_hours` to avoid repeated aggregations on busy dashboards.

**PDF/Export:** Client billing export (billable time entries formatted for invoice input) produces a CSV file download from a Livewire action — no PDF package needed for the export itself. Timesheet PDF (if needed for manager approval records) can use `barryvdh/laravel-dompdf` — this package is not currently in the stack and must be added to `composer.json` if timesheet PDF is in scope.

**AI features:** Missing time alerts run as a scheduled job (`MissingTimeAlertJob`) dispatched daily via the Laravel scheduler. It queries users who have not logged any entries in the last 2 business days and fires a `TimeReminderNotification`. This is pure PHP — no LLM required. Time vs estimate analysis calls OpenAI GPT-4o only when the deviation threshold is crossed and a suggestion template needs to be generated.

## Related

- [[tasks]]
- [[sprints]]
- [[milestones]]
- [[portfolios]]
