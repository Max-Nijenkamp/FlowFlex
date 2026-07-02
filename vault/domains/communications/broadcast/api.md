---
domain: communications
module: broadcast
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Broadcast — API / DTOs

## DTOs

### `CreateBroadcastData` (input)

| Field | Type | Rules |
|---|---|---|
| `title` | string | required |
| `channel` | enum | in set + active channel |
| `audience_type` | enum | segment / employee-group / manual |
| `audience_config` | jsonb | validated per `audience_type` |
| `body` | text | required; whatsapp → approved template ref |
| `scheduled_at` | timestamp nullable | future |

### Output — `BroadcastData`, `BroadcastStatsData`

Read models: broadcast header + the delivery funnel (sent/delivered/opened/failed).

## Service surface

| Method | Kind | Notes |
|---|---|---|
| `BroadcastService::schedule(CreateBroadcastData): BroadcastData` | command | materialises recipient snapshot |
| `BroadcastService::stats(broadcastId): BroadcastStatsData` | read | funnel |

## Public / Portal Endpoints

None of its own. Delivery/open callbacks arrive on the **channel modules'** webhooks (email/whatsapp/sms), which update `comms_broadcast_recipients.status`. Those webhooks are signature-verified + rate-limited ([[security]]).

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/patterns/dto-pattern]]
