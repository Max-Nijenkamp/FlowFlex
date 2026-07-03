---
domain: projects
module: time-tracking
feature: time-entry-timer
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Entry & Timer

Log time manually or via a start/stop timer against a task/project.

## Behaviour

- Manual entry: task/project, date (≤ today), minutes, description, billable flag.
- Timer: start on a task → `timer_started_at`; one running timer per user (`TimerAlreadyRunningException`); stop → computes minutes, creates the entry.

## UI

- **Kind**: simple-resource (entry CRUD) + a timer widget on task view / Kanban card.
- **Page**: `TimeEntryResource` at `/app/projects/time`; timer widget embedded on task detail + Kanban card.
- **Layout**: entry table (date, project, task, minutes, billable). Timer widget = start/stop button + live elapsed.
- **Key interactions**: start timer (blocks if one running → toast); stop → entry created; manual add form.
- **States**: empty (no entries → "Log your first entry") · loading · error (second timer / future date → toast) · selected (running timer highlighted).
- **Gating**: `projects.time.log-own` (own entries); `view-any` to see others.

## Data

- Owns / writes: `proj_time_entries`.
- Reads: `proj_tasks` / `proj_projects` (targets, membership).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes / Feeds: nothing (entries consumed read-side by projects/resources).
- Shared entity: `proj_tasks`, `proj_projects`, `users`.

## Test Checklist

### Unit
- [ ] Stop timer computes `minutes_logged` as an integer from `timer_started_at` → now (no float/decimal-hour math).
- [ ] `LogTimeData` rejects a future-dated entry and a `minutes_logged` < 1.

### Feature (Pest)
- [ ] Start a timer, stop it → a single entry is created with correct minutes; the running-timer marker clears.
- [ ] Starting a second timer while one runs raises `TimerAlreadyRunningException` (pessimistic single-running-timer guard holds under concurrent starts).
- [ ] Tenant + own-data scope: a user can only log against member tasks/projects; company A cannot log against company B.

### Livewire
- [ ] Timer control / `TimeEntryResource` requires `projects.time.log-own`; hidden when `projects.time` inactive.
- [ ] Second-timer / future-date attempts surface an error toast (no entry written).

## Unknowns

- Idle-detection / auto-stop; passive capture — see [[../unknowns]] + [[../../_opportunities]].

## Related

- [[../_module|Time Tracking]] · [[timesheet-approval|Timesheet & Approval]] · [[../../kanban/_module|Kanban]]
