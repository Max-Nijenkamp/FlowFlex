---
domain: customer-success
module: health-scores
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Health Scores — DTOs & API

## DTOs

### ConfigureHealthData (input)

| Field | Type | Validation |
|---|---|---|
| factor_weights | array | required; values sum to 100 ("Factor weights must total 100.") |
| tier_thresholds | array | required; `{green, amber}` ints, `green > amber`, 0–100 |

Written by the `HealthDashboardPage` configuration form; updates the single `cs_health_config` row.

### HealthScoreData (output)

`account_id`, `account_name`, `score`, `tier`, `factors[]` (each `{factor, value, weight, contribution}`), `calculated_at`

---

## Internal Read API

`cs.health` exposes `HealthScoreService::breakdown()` / `trend()` as an **internal read API** consumed by other CS modules (`cs.churn` primary risk signal, `cs.qbr`, `cs.analytics`). No HTTP surface — consumers call the service in-process, tenant-scoped.

---

## Public / Portal Endpoints

None planned for v1. All access is via the Filament CRM panel (authenticated, `crm` guard). Health scores are internal signals and are never exposed to public or portal surfaces.
