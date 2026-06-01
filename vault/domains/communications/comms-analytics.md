---
type: module
domain: Communications
panel: comms
module-key: comms.analytics
status: planned
color: "#4ADE80"
---

# Comms Analytics

Response time, message volume by channel, resolution rate, and agent performance across the shared inbox.

## Core Features

- Message volume by channel over time
- Average first-response time and resolution time per channel
- Conversation resolution rate
- Agent performance: conversations handled, avg response time per agent
- Busiest hours/days heat-map
- Channel mix breakdown (which channels customers use most)
- Broadcast performance (delivery, open rates) from broadcast module

## Data Model

No additional tables. Aggregates from `comms_conversations`, `comms_messages`, `comms_broadcasts`.

## Filament

**Nav group:** Analytics

- `CommsAnalyticsDashboard` (custom dashboard) — chart widgets (leandrocfe/filament-apex-charts)
- Date range + channel filter

## Related

- [[domains/communications/shared-inbox]]
- [[domains/communications/broadcast]]
- [[architecture/performance]]
