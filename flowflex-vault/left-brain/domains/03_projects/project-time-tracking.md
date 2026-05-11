---
type: module
domain: Projects & Work
panel: projects
phase: 2
status: in-progress
cssclasses: domain-projects
migration_range: 202500–202999
last_updated: 2026-05-10
right_brain_log: "[[builder-log-projects-phase2]]"
---

# Project Time Tracking

Log hours against tasks and projects. Timer or manual entry. Timesheets for approval. Feeds utilisation reports in PSA and billable hours in client invoicing.

---

## Time Entry Methods

**Timer**: start/stop while working:
- One-click start on any task
- Timer visible in toolbar (global, across all views)
- Stop → entry auto-created for logged time

**Manual entry**:
- Select date, task, duration, description
- Bulk entry: fill weekly timesheet grid

**Import**: from calendar (meetings auto-suggest as time entries).

---

## Time Entry Fields

| Field | |
|---|---|
| Date | When the work was done |
| Task | Link to specific task (or project-level if no task) |
| Hours | Decimal (1.5 = 1h30m) |
| Description | What was done |
| Billable | Yes/No (drives invoicing) |
| Billing rate | Override or use project default |

---

## Timesheets

Weekly timesheet view:
- Rows: tasks worked on
- Columns: Mon–Sun
- Cells: hours entered
- Total hours per day, per week
- Submit for approval at week-end

Manager approval:
- Review team's timesheets
- Approve / reject with comments
- Approved timesheets locked (no editing)

---

## Billable vs Non-Billable

Project configured as billable or non-billable.
Task-level override available.
Billable hours → available in PSA [[agency-billing-intelligence]] for client invoicing.

---

## Time Reports

- **Utilisation report**: billable hours / total hours % by person and team
- **Project burn**: actual hours logged vs budgeted hours
- **Client summary**: total billable hours per client per period
- **Overtime**: hours logged > standard work hours

---

## Data Model

### `proj_time_entries`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| user_id | ulid | FK |
| task_id | ulid | nullable FK |
| project_id | ulid | FK |
| logged_date | date | |
| hours | decimal(8,2) | |
| description | text | nullable |
| is_billable | boolean | |
| billing_rate | decimal(10,2) | nullable |
| timesheet_id | ulid | nullable FK |

### `proj_timesheets`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| user_id | ulid | FK |
| week_start | date | |
| status | enum | draft/submitted/approved/rejected |
| approved_by | ulid | nullable FK |

---

## Migration

```
202500_create_proj_time_entries_table
202501_create_proj_timesheets_table
```

---

## Related

- [[MOC_Projects]]
- [[task-management]]
- [[project-budget-costs]]
- [[MOC_PSA]] — utilisation + billing
