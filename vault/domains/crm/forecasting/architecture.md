---
domain: crm
module: forecasting
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

Nav group: **Pipeline**.

| Artifact | ui-strategy row | Purpose |
|---|---|---|
| `QuotaResource` | #1 CRUD | Quota per rep / period. |
| `ForecastPage` | #6 custom dashboard | Apex charts: attainment, coverage, week-over-week trend from snapshots. |
| `ForecastWidget` | #6 widget | Team attainment summary. |

Custom pages/widgets follow [[../../../architecture/ui-strategy]] and [[../../../architecture/filament-patterns]].

**Access contract:** `canAccess()` = `can('crm.forecasting.view-any') && hasModule('crm.forecasting')`.

## Jobs & Scheduling

| Job | Queue | Schedule | Idempotency |
|---|---|---|---|
| `CaptureForecastSnapshotsCommand` | crm *(assumed queue: default)* | Weekly Mon 06:00 | Upsert per (owner, period, category, week). |

See [[../../../infrastructure/queue-horizon]].

## Search & Realtime

None.
