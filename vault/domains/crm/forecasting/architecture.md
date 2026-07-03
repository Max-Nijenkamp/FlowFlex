---
domain: crm
module: forecasting
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Forecasting — Architecture

## State Machine

None. Forecast categories are a simple enum tag on open deals, not a lifecycle state machine.

## Services & Actions

| Class | Signature | Notes |
|---|---|---|
| `SalesForecastService` | `forecast(period, ?ownerId): ForecastData` | Live computation from `crm_deals`. Rolls up rep → team → company when `ownerId` is null. Monetary math via `brick/money`. |
| `SetForecastCategoryAction` | `run(dealId, category): void` | Tags a deal into commit / best-case / pipeline / closed. Open deals only. |
| `CaptureForecastSnapshotsCommand` | console command | Weekly snapshot per rep / category into `crm_forecast_snapshots`. |

Monetary amounts are integers (minor unit) and manipulated with `brick/money` — never raw float math. See [[../../../architecture/filament-patterns]] and [[../../../glossary]].

The `add_forecast_category_to_crm_deals` migration adds a `forecast_category` column to the `crm_deals` table owned by [[../deals/_module|Deals]]; this module owns the semantics of that column *(assumed)*.

## Events

None fired, none consumed.

## Filament Artifacts

**Nav group:** Pipeline

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `QuotaResource` | #1 CRUD resource | standard resource (no tweaks) | Quota per rep / period; list filters: rep, team, period |
| `ForecastPage` | #6 Dashboard custom page | [[../../../architecture/patterns/page-blueprints#Dashboard]] | Apex charts: attainment, coverage, week-over-week trend from snapshots; widget polling 30–60s |
| `ForecastWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | Team attainment summary; polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.forecasting.view-any') && BillingService::hasModule('crm.forecasting')`
per [[../../../architecture/filament-patterns]] #1. `ForecastPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. `view-own` vs `view-team` roll-up scoping is enforced in
`SalesForecastService`, not just the UI (see [[./security]]).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Quota CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Weighted-pipeline / attainment / coverage computation | n-a | read-only derived aggregation over `crm_deals` / `crm_pipeline_stages` — owns no writes |
| Forecast snapshot capture (`CaptureForecastSnapshotsCommand`) | n-a | append/upsert-only batch job, single writer per (owner, period, category, week) — no interactive concurrent edit |
| Forecast category set (`SetForecastCategoryAction`) | n-a (delegated) | writes `forecast_category` on `crm_deals` (owned by [[../deals/_module\|crm.deals]]) — the write is delegated to a deals-owned action/event pending the ownership ADR (see cross-domain warning in [[./_module]]) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job | Queue | Schedule | Idempotency |
|---|---|---|---|
| `CaptureForecastSnapshotsCommand` | crm *(assumed queue: default)* | Weekly Mon 06:00 | Upsert per (owner, period, category, week). |

See [[../../../infrastructure/queue-horizon]].

## Search & Realtime

None.
