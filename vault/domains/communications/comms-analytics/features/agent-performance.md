---
domain: communications
module: comms-analytics
feature: agent-performance
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Agent Performance

Per-agent conversations handled and average response time.

## Behaviour

- Group outbound messages / assigned conversations by agent within the window.
- Metrics per agent: conversations handled, avg response time.
- Filtered by date range + optional channel; cached.

## UI

- **Kind**: widget
- **Page**: `AgentPerformanceWidget` on `CommsAnalyticsDashboard` — Analytics nav group.
- **Layout**: sortable table (agent, handled, avg response time).
- **Key interactions**: sort columns; date/channel filter recomputes.
- **States**: empty (no agent activity) · loading (skeleton rows) · error · filtered.
- **Gating**: `comms.analytics.view`.

## Data

- Owns / writes: nothing.
- Reads: `comms_messages` (sent_by/assignee), `users` for names — read-only aggregate.
- Cross-domain writes: none ([[../../../security/data-ownership]]).

## Relations

- Consumes: inbox message/assignment data + RBAC user names (read-only).
- Feeds: nothing.
- Shared entity: `comms_messages` (inbox), `users` (RBAC).

## Related

- [[../_module|Comms Analytics]] · [[response-time-metrics]] · [[channel-mix]]
