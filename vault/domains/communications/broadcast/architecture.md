---
domain: communications
module: broadcast
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Broadcast — Architecture

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `BroadcastService::schedule` | `schedule(CreateBroadcastData): BroadcastData` | Materialises recipients at schedule time — dedupe, exclude SMS opt-outs, exclude `email_deliverable=false`. Transitions `draft → scheduled`. |
| `SendBroadcastBatchJob` | queued (chained) | On the `notifications` queue; chunked; per-recipient try/catch + status update; personalisation substitution; rate-limited per channel; resume-safe (only `pending` recipients). |
| `BroadcastService::stats` | `stats(broadcastId): BroadcastStatsData` | Funnel counts from recipient statuses. |
| `DispatchScheduledBroadcastsCommand` | scheduled (5 min) | Picks up `scheduled` broadcasts whose time has arrived; transitions `scheduled → sending`; dispatches the batch chain. |

Delivery/open callbacks (channel webhooks) update `comms_broadcast_recipients.status`.

## State Machine

| State | → | Trigger | Side effects |
|---|---|---|---|
| `draft` | `scheduled` | `comms.broadcast.send` | recipients materialised (snapshot) |
| `scheduled` | `sending` | scheduler reaches time / send-now | batched job chain dispatched |
| `sending` | `sent` | all batches done | counts finalised |
| `sending` | `failed` | infrastructure failure | resumable (pending recipients only) |
| `draft`/`scheduled` | cancelled *(assumed)* | creator | |

`spatie/laravel-model-states` — classes in `app/States/Comms/Broadcast/`.

## Events

None fired or consumed. Sending goes through channel drivers; audiences via read APIs. See [[../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `BroadcastResource` | Broadcast | #1 CRUD resource | audience builder + composer + preview; delivery funnel on the view page. |
| `BroadcastStatsWidget` | Broadcast | #6 widget | funnel per broadcast. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.broadcast.view-any')
        && BillingService::hasModule('comms.broadcast');
}
```

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `DispatchScheduledBroadcastsCommand` | default | every 5 min | `scheduled → sending` transition guard |
| `SendBroadcastBatchJob` | notifications | chained | recipient `pending` guard — resume-safe |

See [[../../../architecture/queue-jobs]].

## Implementation Notes (tense-softened)

- Recipients are designed to be **snapshotted at schedule time** (address + name), so audience changes after scheduling don't alter the send.
- Sending is designed to be **resumable**: a mid-send failure re-runs only `pending` recipients, so no one is messaged twice.
- Per-channel rate limits are designed to keep sends within provider throughput (chunk ~100/min per channel *(assumed)*).

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/patterns/states]]
