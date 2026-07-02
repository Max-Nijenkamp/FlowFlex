---
domain: crm
module: revenue-intelligence
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Revenue Intelligence — API & DTOs

## Output DTOs

This module exposes output DTOs only — it computes and reports, it takes no external input writes.

### DealHealthData

| Field | Type | Notes |
|---|---|---|
| deal | DealData | The scored deal. |
| score | int | 0–100. |
| factors | array | `[{factor, score, weight, detail}]`. |
| risk_level | string | Derived from score / threshold. |

### WinLossAnalysisData

| Field | Type | Notes |
|---|---|---|
| reason_breakdown | array | Reasons by count. |
| competitor_table | array | Competitor win/loss counts. |
| conversion_funnel | array | Stage-to-stage conversion. |
| velocity_stats | array | Avg time per stage, cycle length. |

## Public / Portal Endpoints

None. All views are inside the `/crm` panel.

See [[../../../architecture/patterns/dto-pattern]].
