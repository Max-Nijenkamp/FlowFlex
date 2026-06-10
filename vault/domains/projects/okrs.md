---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.okrs
status: planned
priority: p2
depends-on: [core.billing, core.rbac, core.notifications]
soft-depends: [projects.projects]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [proj_objectives, proj_key_results, proj_okr_checkins]
permission-prefix: projects.okrs
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# OKRs

Objectives and Key Results: company-level and team-level goal setting with progress tracking and quarterly check-ins.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, check-in reminders |
| Soft | [[domains/projects/projects\|projects.projects]] | objectives optionally link projects *(assumed)* — standalone otherwise |

---

## Core Features

- Objective: title, owner, quarter, year, description, parent objective (nested OKRs)
- Key Results: measurable outcomes linked to an objective (target value, current value, unit)
- Progress calculation: KR progress = current/target clamped 0–100; objective progress = average of KR progress (cascades up parents)
- Status indicators: on-track / at-risk / off-track — progress vs quarter time elapsed (>15pt behind = at-risk, >30 = off-track *(assumed)*)
- Check-in: weekly/fortnightly progress update on each KR with notes
- Hierarchy: company → department → team → individual OKRs (max depth 4 *(assumed)*)
- OKR dashboard: progress overview, health distribution, recent check-ins
- Check-in reminder notifications to KR owners

---

## Data Model

### proj_objectives

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| description | text nullable | |
| owner_id | ulid FK users | |
| quarter | int 1–4 | unique-ish `(company, owner, quarter, year, title)` not enforced |
| year | int | |
| parent_objective_id | ulid nullable FK self | cycle-checked |
| progress_percent | decimal(5,2) default 0 | computed cache |
| deleted_at | timestamp nullable | |

### proj_key_results

| Column | Type | Notes |
|---|---|---|
| id, objective_id FK, company_id | ulid | |
| title | string | |
| target_value / current_value | decimal(14,2) | start value support via baseline *(assumed: baseline_value column)* |
| baseline_value | decimal(14,2) default 0 | |
| unit | string | %, €, count… |
| progress_percent | decimal(5,2) default 0 | |

### proj_okr_checkins — id, key_result_id FK, company_id, user_id FK, current_value, notes, checked_in_at

---

## DTOs

### CreateObjectiveData — title (required), owner_id, quarter (1–4), year, parent_objective_id? (no cycle, depth ≤ 4), description?
### CheckInData — key_result_id (own or `projects.okrs.update-any`), current_value (numeric), notes?

## Services & Actions

- `OkrService::checkIn(CheckInData $data)` — records check-in, recomputes KR progress (`(current−baseline)/(target−baseline)` clamped), cascades objective + parent progress
- `OkrService::health(string $objectiveId): string`
- `CreateObjectiveAction` / `AddKeyResultAction`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `OkrCheckinReminderCommand` | notifications | weekly Mon 09:00 | KRs without check-in in 7d — re-run window-safe |

---

## Filament

**Nav group:** OKRs

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ObjectiveResource` | #1 CRUD resource (tree-ish list) | nested display, KR relation manager, check-in action |
| `OkrDashboardPage` | #6 dashboard page | quarter selector, health distribution, recent check-ins |

---

## Permissions

`projects.okrs.view-any` · `projects.okrs.create` · `projects.okrs.update-own` · `projects.okrs.update-any`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] KR progress math incl. baseline; clamped 0–100
- [ ] Objective progress = average of KRs; cascades to parent
- [ ] Hierarchy cycle + depth rejected
- [ ] Check-in by non-owner without update-any rejected
- [ ] Health boundaries vs time elapsed fixtures
- [ ] Reminder targets stale KRs only

---

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
app/Filament/Projects/Resources/ObjectiveResource.php
app/Filament/Projects/Pages/OkrDashboardPage.php
database/factories/Projects/{ObjectiveFactory,KeyResultFactory}.php
tests/Feature/Projects/OkrTest.php
```

---

## Related

- [[domains/projects/projects]]
- [[domains/hr/hr-analytics]]
