---
domain: projects
module: okrs
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# OKRs — Architecture

## Progress Model

- KR progress = `(current − baseline) / (target − baseline)` clamped 0–100.
- Objective progress = average of its KR progress; cascades up parent objectives.
- Health = progress vs quarter time-elapsed: at-risk >15pt behind, off-track >30pt *(assumed)*.

## Hierarchy

`parent_objective_id` self-FK, cycle-checked, max depth 4 *(assumed)*: company → department → team → individual.

## Services & Actions

- `OkrService::checkIn(CheckInData)` — records check-in, recomputes KR progress, cascades objective + parent progress.
- `OkrService::health(objectiveId): string`.
- `CreateObjectiveAction` / `AddKeyResultAction` (cycle + depth guards).

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `OkrCheckinReminderCommand` | notifications | weekly Mon 09:00 | KRs without a check-in in 7d — window-safe re-run |

## Events

None cross-domain. Reminders use the notifications service API.

## Filament Artifacts

**Nav group:** OKRs

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ObjectiveResource` | #1 CRUD resource | tweaks: custom-header-actions (check-in) | nested/indented objective display; KR relation manager (target/current/baseline/unit + progress bar); list filters: quarter, owner, health *(assumed)* |
| `OkrDashboardPage` | #6 Dashboard custom page | [[../../../architecture/patterns/page-blueprints#Dashboard]] | quarter selector; health-distribution donut; per-objective progress; recent check-ins feed; widget polling 30–60s *(assumed)* |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('projects.okrs.view-any') && BillingService::hasModule('projects.okrs')`
per [[../../../architecture/filament-patterns]] #1. `OkrDashboardPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. The check-in action additionally requires
`projects.okrs.update-own` (own KRs) or `projects.okrs.update-any` (others'), enforced in `OkrService::checkIn`.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Objective / Key Result CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Check-in + progress cascade (`OkrService::checkIn`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the objective + its parent chain while recomputing cached `progress_percent`, so concurrent check-ins on sibling KRs don't clobber the roll-up per [[../../../architecture/patterns/states]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

None.
