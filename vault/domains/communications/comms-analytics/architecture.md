---
domain: communications
module: comms-analytics
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Comms Analytics — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `CommsAnalyticsService::metrics` | `metrics(from, to, ?channel): CommsMetricsData` | Aggregate queries over inbox + broadcast data, N+1-free. First-response = first outbound after the first inbound per conversation. Cached. |

Read-only — no writes, no state machine, no jobs.

## Events

None fired or consumed. Pure read aggregator. See [[../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Analytics

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `CommsAnalyticsDashboard` | #6 dashboard page | [[../../../architecture/patterns/page-blueprints#Dashboard]] | date range + channel filter; polling 60s |
| `ChannelVolumeWidget` / `ResponseTimeWidget` / `AgentPerformanceWidget` / `ChannelMixWidget` | #6 dashboard widgets | [[../../../architecture/patterns/page-blueprints#Dashboard]] | fed by `CommsAnalyticsService`; busiest-hours heat-map via apexcharts heatmap |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('comms.analytics.view') && BillingService::hasModule('comms.analytics')`
per [[../../../architecture/filament-patterns]] #1. `CommsAnalyticsDashboard` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. The broadcast-performance section additionally hides when
`comms.broadcast` is inactive.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| (all) | n/a | Read-only aggregator — owns no tables and writes nothing; every path is a `CompanyScope`-bound aggregate query over inbox + broadcast data. No write to race |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:comms:metrics:{from}:{to}:{channel}` | 1 h historical / 15 min current | TTL only |

See [[../../../architecture/caching]].

## Implementation Notes (tense-softened)

- Metrics are designed as **aggregate queries** (no per-row loops) with the first-response measured as the first outbound after the first inbound per conversation.
- The heat-map is designed to bucket by the **company timezone**.
- The broadcast section is designed to **hide** when `comms.broadcast` is inactive.

## Related

- [[_module]] · [[api]] · [[../../../architecture/caching]]
