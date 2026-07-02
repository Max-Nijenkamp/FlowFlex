---
domain: communications
module: comms-analytics
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Comms Analytics — API / DTOs

> Thin — this module owns no tables and exposes no endpoints. Output-only DTO.

## DTOs

### `CommsMetricsData` (output only)

Series + breakdowns + agent table + broadcast funnel:

| Section | Shape |
|---|---|
| volume | per-channel time series |
| response-time | avg first-response + resolution per channel |
| resolution-rate | resolved / total |
| agents | per-agent handled + avg response time |
| heat-map | hour×day buckets (company tz) |
| channel-mix | share per channel |
| broadcast | delivery/open funnel (when `comms.broadcast` active) |

## Service surface

| Method | Kind | Notes |
|---|---|---|
| `CommsAnalyticsService::metrics(from, to, ?channel): CommsMetricsData` | read | cached aggregate |

## Public / Portal Endpoints

None.

## Related

- [[_module]] · [[architecture]]
