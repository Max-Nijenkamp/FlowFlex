---
domain: communications
module: comms-analytics
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `CommsAnalyticsDashboard` | Analytics | #6 dashboard page + apex charts | date range + channel filter; polling 60s. |
| `ChannelVolumeWidget` / `ResponseTimeWidget` / `AgentPerformanceWidget` / `ChannelMixWidget` | Analytics | #6 widgets | fed by `CommsAnalyticsService`. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.analytics.view-any')
        && BillingService::hasModule('comms.analytics');
}
```

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
