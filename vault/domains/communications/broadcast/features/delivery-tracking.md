---
domain: communications
module: broadcast
feature: delivery-tracking
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Delivery Tracking

Per-recipient status and a delivery funnel, updated from channel callbacks.

## Behaviour

- `SendBroadcastBatchJob` marks each recipient `sent` (or `failed` + error) as it dispatches.
- Channel delivery/open callbacks update `status` to `delivered` / `opened` (open = email only).
- `BroadcastService::stats` aggregates the funnel (sent / delivered / opened / failed).

## UI

- **Kind**: widget
- **Page**: `BroadcastStatsWidget` (#6) on the `BroadcastResource` view page — Broadcast nav group.
- **Layout**: funnel bars + per-status counts; recipient table with status + error column.
- **Key interactions**: open a broadcast → live funnel; filter recipients by status.
- **States**: sending (partial counts, progress) · sent (final funnel) · failed (resumable banner) · empty (no recipients).
- **Gating**: `comms.broadcast.view-any`.

## Data

- Owns / writes: `comms_broadcast_recipients.status` (own module), updated by the batch job + callbacks.
- Reads: channel delivery callbacks (via the channel webhooks).
- Cross-domain writes: none.

## Relations

- Consumes: delivery/open callbacks from channel modules.
- Feeds: funnel data read by [[../../comms-analytics/_module|comms.analytics]] (broadcast performance section).
- Shared entity: none owned elsewhere.

## Test Checklist

### Unit
- [ ] `BroadcastService::stats` funnel counts match per-recipient statuses (sent / delivered / opened / failed)

### Feature (Pest)
- [ ] Batch send marks each recipient `sent` / `failed`; resume sends only `pending` (no double-send under row lock)
- [ ] Channel delivery/open callback updates recipient `status`; open recorded for email only

### Livewire
- [ ] Stats widget renders the live funnel; recipient table filters by status; visible only with `comms.broadcast.view-any`

## Related

- [[../_module|Broadcast]] · [[recipient-materialisation]] · [[../../comms-analytics/_module|Comms Analytics]]
