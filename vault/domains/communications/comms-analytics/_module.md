---
domain: communications
module: comms-analytics
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Comms Analytics

Response time, message volume by channel, resolution rate, and agent performance across the shared inbox. **Owns no tables** — it aggregates read-only from other comms modules.

## Module-key

`comms.analytics`

**Priority:** p2  
**Panel:** comms  
**Permission prefix:** `comms.analytics`  
**Tables:** — (none — read-only aggregator)  
**Patterns:** custom-pages

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../shared-inbox/_module\|comms.inbox]] | all metrics source from inbox data |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Soft | [[../broadcast/_module\|comms.broadcast]] | broadcast-performance section; hidden without it |

## Core Features

- Message volume by channel over time.
- Average first-response time + resolution time per channel.
- Conversation resolution rate.
- Agent performance: conversations handled, avg response time per agent.
- Busiest hours/days heat-map (timezone-aware).
- Channel mix breakdown (which channels customers use most).
- Broadcast performance (delivery, open rates) — from the broadcast module.

## See features/

- [[features/response-time-metrics|Response-time Metrics]] — first-response + resolution time per channel.
- [[features/agent-performance|Agent Performance]] — per-agent handled + response time.
- [[features/channel-mix|Channel Mix & Volume]] — volume over time + channel breakdown + heat-map.

## Build Manifest

```
app/Data/Comms/CommsMetricsData.php
app/Services/Comms/CommsAnalyticsService.php
app/Filament/Comms/Pages/CommsAnalyticsDashboard.php
app/Filament/Comms/Widgets/{ChannelVolumeWidget,ResponseTimeWidget,AgentPerformanceWidget,ChannelMixWidget}.php
tests/Feature/Comms/CommsAnalyticsTest.php
```

## Test Checklist

- [ ] Tenant isolation: every metric is `CompanyScope`-bound; company A never sees company B inbox/broadcast data.
- [ ] Module gating: dashboard + widgets hidden when `comms.analytics` inactive.
- [ ] First-response / resolution math over fixtures.
- [ ] Channel filter restricts all widgets.
- [ ] Broadcast section hidden when the module is inactive.
- [ ] Heat-map buckets correct (timezone-aware).

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | aggregate queries | [[../shared-inbox/_module\|comms.inbox]] | `comms_conversations`, `comms_messages` (read-only) |
| Reads | broadcast funnel | [[../broadcast/_module\|comms.broadcast]] | `comms_broadcasts` (read-only; section hidden without module) |

No events, no writes.

**Data ownership:** `comms.analytics` **owns no tables** and **writes nothing**. It runs read-only aggregate queries over inbox + broadcast data and caches the result. This is the canonical read-only cross-domain pattern ([[../../../security/data-ownership]]).

## Related

- [[architecture]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../shared-inbox/_module|Shared Inbox]] · [[../broadcast/_module|Broadcast]] · [[../../../architecture/caching]]
