---
domain: projects
module: okrs
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# OKRs

Objectives and Key Results: company- and team-level goal setting with progress tracking and quarterly check-ins.

## Module-key

| Field | Value |
|---|---|
| key | `projects.okrs` |
| priority | p2 |
| panel | projects |
| permission-prefix | `projects.okrs` |
| tables | `proj_objectives`, `proj_key_results`, `proj_okr_checkins` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, check-in reminders |
| Soft | [[../projects/_module\|projects.projects]] | objectives optionally link projects *(assumed)* — standalone otherwise |

## Core Features

- Objective: title, owner, quarter, year, description, parent objective (nested OKRs).
- Key Results: measurable outcomes (target/current/baseline value, unit).
- Progress: KR = (current−baseline)/(target−baseline) clamped 0–100; objective = average of KRs, cascades up parents.
- Status: on-track / at-risk / off-track vs quarter time elapsed (>15pt behind = at-risk, >30 = off-track *(assumed)*).
- Check-in: periodic KR progress update with notes.
- Hierarchy company → department → team → individual (max depth 4 *(assumed)*).
- OKR dashboard; check-in reminder notifications.

## See features/

- [[features/objectives-key-results|Objectives & Key Results]] — the hierarchy + KR progress math.
- [[features/checkins-dashboard|Check-ins & Dashboard]] — periodic check-ins, health, reminders.

## Build Manifest

```
database/migrations/xxxx_create_proj_objectives_table.php
database/migrations/xxxx_create_proj_key_results_table.php
database/migrations/xxxx_create_proj_okr_checkins_table.php
app/Models/Projects/{Objective,KeyResult,OkrCheckin}.php
app/Data/Projects/{CreateObjectiveData,CheckInData}.php
app/Services/Projects/OkrService.php
app/Actions/Projects/{CreateObjectiveAction,AddKeyResultAction}.php
app/Console/Commands/Projects/OkrCheckinReminderCommand.php
app/Filament/Projects/Resources/ObjectiveResource.php · Pages/OkrDashboardPage.php
database/factories/Projects/{ObjectiveFactory,KeyResultFactory}.php
tests/Feature/Projects/OkrTest.php
```

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] KR progress math incl. baseline; clamped 0–100.
- [ ] Objective progress = average of KRs; cascades to parent.
- [ ] Hierarchy cycle + depth rejected.
- [ ] Check-in by non-owner without `update-any` rejected.
- [ ] Health boundaries vs time elapsed fixtures.
- [ ] Reminder targets stale KRs only.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Commands | `NotificationService::notify` | core.notifications | weekly check-in reminders |
| Reads | optional project link | projects.projects | objectives may reference a project *(assumed)* |

**Data ownership:** `projects.okrs` writes only its three tables. Reminders go through the notifications service API; a project link is a stored foreign id, never a `proj_projects` write ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../projects/_module|Projects]] · [[../../hr/employee-feedback/_module|HR Feedback]]
- [[../../../glossary]]
