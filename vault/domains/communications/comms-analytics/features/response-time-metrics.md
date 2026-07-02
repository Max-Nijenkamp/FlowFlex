---
domain: communications
module: comms-analytics
feature: response-time-metrics
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Response-time Metrics

First-response time, resolution time, and resolution rate per channel.

## Behaviour

- First-response = first outbound after the first inbound per conversation.
- Resolution time = inbound → status `resolved`.
- Resolution rate = resolved / total conversations in the window.
- All computed as aggregate queries, filtered by date range + optional channel, cached.

## UI

- **Kind**: widget
- **Page**: `ResponseTimeWidget` on `CommsAnalyticsDashboard` (`/comms/analytics`) — Analytics nav group.
- **Layout**: KPI tiles (avg first-response, avg resolution, resolution rate) + trend line.
- **Key interactions**: change date range / channel filter → widget recomputes (polls 60s).
- **States**: empty (no conversations in range) · loading (skeleton tiles) · error (query failure) · filtered.
- **Gating**: `comms.analytics.view`.

## Data

- Owns / writes: nothing.
- Reads: `comms_conversations`, `comms_messages` (owned by [[../../shared-inbox/_module|comms.inbox]], read-only aggregate).
- Cross-domain writes: none ([[../../../security/data-ownership]]).

## Relations

- Consumes: inbox conversation/message data (read-only).
- Feeds: nothing.
- Shared entity: `comms_conversations`, `comms_messages` (owned by the inbox).

## Related

- [[../_module|Comms Analytics]] · [[agent-performance]] · [[channel-mix]]
