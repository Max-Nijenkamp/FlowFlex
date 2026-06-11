---
type: module
domain: Communications
domain-key: communications
panel: comms
module-key: comms.analytics
status: planned
priority: p2
depends-on: [comms.inbox, core.billing, core.rbac]
soft-depends: [comms.broadcast]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: comms.analytics
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Comms Analytics

Response time, message volume by channel, resolution rate, and agent performance across the shared inbox. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/communications/shared-inbox\|comms.inbox]] | all metrics |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/communications/broadcast\|comms.broadcast]] | broadcast performance section; hidden without it |

---

## Core Features

- Message volume by channel over time
- Average first-response time and resolution time per channel
- Conversation resolution rate
- Agent performance: conversations handled, avg response time per agent
- Busiest hours/days heat-map
- Channel mix breakdown (which channels customers use most)
- Broadcast performance (delivery, open rates) from broadcast module

---

## Data Model

No additional tables. Aggregates from `comms_conversations`, `comms_messages`, `comms_broadcasts`.

## DTOs

Output only: `CommsMetricsData` — series + breakdowns + agent table + broadcast funnel.

## Services & Actions

- `CommsAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to, ?string $channel): CommsMetricsData` — aggregate queries, no N+1 (first-response = first outbound after first inbound per conversation)

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:comms:metrics:{from}:{to}:{channel}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Analytics

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CommsAnalyticsDashboard` | #6 dashboard page + apex charts | date range + channel filter; polling 60s |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('comms.analytics.view-any') && BillingService::hasModule('comms.analytics')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`comms.analytics.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] First-response/resolution math over fixtures
- [ ] Channel filter restricts all widgets
- [ ] Broadcast section hidden when module inactive
- [ ] Heat-map buckets correct (timezone-aware)

---

## Build Manifest

```
app/Data/Comms/CommsMetricsData.php
app/Services/Comms/CommsAnalyticsService.php
app/Filament/Comms/Pages/CommsAnalyticsDashboard.php
app/Filament/Comms/Widgets/{ChannelVolumeWidget,ResponseTimeWidget,AgentPerformanceWidget,ChannelMixWidget}.php
tests/Feature/Comms/CommsAnalyticsTest.php
```

---

## Related

- [[domains/communications/shared-inbox]]
- [[domains/communications/broadcast]]
- [[architecture/caching]]
