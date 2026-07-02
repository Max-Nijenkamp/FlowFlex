---
domain: crm
module: forecasting
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Forecasting — API

## DTOs

### SetQuotaData (input)

| Field | Type | Rules |
|---|---|---|
| owner_id | ulid | required, exists users |
| period | string | required, format `YYYY-MM` or `YYYY-Qn` |
| quota_cents | int | required, min:0 |

### ForecastData (output)

Per rep (or rolled up when `ownerId` null):

| Field | Type | Notes |
|---|---|---|
| quota_cents | int | Target |
| closed_cents | int | Closed-won value |
| commit_cents | int | Commit category total |
| best_case_cents | int | Best-case category total |
| weighted_pipeline_cents | int | Σ value × probability |
| attainment_percent | float | closed / quota × 100 |
| coverage_ratio | float | pipeline / quota |

## Public / Portal Endpoints

None. Forecasting is panel-internal only.
