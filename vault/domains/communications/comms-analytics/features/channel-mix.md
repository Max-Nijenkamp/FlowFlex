---
domain: communications
module: comms-analytics
feature: channel-mix
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Channel Mix & Volume

Message volume over time, channel-mix breakdown, busiest-hours heat-map, and the broadcast-performance section.

## Behaviour

- Volume: per-channel message counts over the date range.
- Channel mix: share of conversations/messages per channel.
- Heat-map: hour×day buckets in the company timezone.
- Broadcast performance: delivery/open funnel — rendered only when `comms.broadcast` is active.

## UI

- **Kind**: widget
- **Page**: `ChannelVolumeWidget` + `ChannelMixWidget` on `CommsAnalyticsDashboard` — Analytics nav group (apex charts).
- **Layout**: stacked volume chart + donut channel mix + heat-map grid; broadcast funnel section (conditional).
- **Key interactions**: date/channel filter; hover buckets for detail; polls 60s.
- **States**: empty (no data) · loading (chart skeletons) · error · broadcast-hidden (module inactive).
- **Gating**: `comms.analytics.view`.

## Data

- Owns / writes: nothing.
- Reads: `comms_conversations`, `comms_messages` (inbox); `comms_broadcasts` (broadcast) — read-only aggregate.
- Cross-domain writes: none ([[../../../security/data-ownership]]).

## Relations

- Consumes: inbox + broadcast data (read-only).
- Feeds: nothing.
- Shared entity: `comms_messages` (inbox), `comms_broadcasts` (broadcast).

## Related

- [[../_module|Comms Analytics]] · [[response-time-metrics]] · [[../../broadcast/_module|Broadcast]]
