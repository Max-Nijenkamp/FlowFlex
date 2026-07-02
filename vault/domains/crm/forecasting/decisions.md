---
domain: crm
module: forecasting
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — Decisions

## ADR: `forecast_category` column ownership *(assumed)*

**Context:** Forecast categories (commit / best-case / pipeline / closed) must attach to individual open deals.

**Decision:** This module adds a `forecast_category` column to `crm_deals` (owned by [[../deals/_module|Deals]]) via its own migration, and owns the column's semantics. Alternative — a separate `crm_deal_forecast_categories` join table — was rejected as over-engineered for a single nullable enum.

**Consequences:** Cross-module migration coupling; Deals must not repurpose the column. Flagged in [[unknowns]].

## ADR: Live-compute vs snapshot split

**Context:** Current forecast must be exact; historical accuracy needs a frozen record.

**Decision:** `SalesForecastService` computes the current forecast live from `crm_deals` on every request. A weekly command captures immutable snapshots into `crm_forecast_snapshots` for week-over-week trend and forecast-vs-actual accuracy.

**Consequences:** No stale current numbers; history is append-only and idempotent per week.
