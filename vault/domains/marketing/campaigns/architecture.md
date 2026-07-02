---
domain: marketing
module: campaigns
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Campaigns — Architecture

Parent: [[_module]]

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `CampaignService::schedule` | `schedule(CreateCampaignData): CampaignData` | Materialises recipients from the segment; excludes suppression list + `email_deliverable=false`; assigns A/B variants by `split_percent`. Transitions `draft → scheduled`. |
| `SendCampaignBatchJob` | queued (chained) | On the `notifications` queue; chunked; per-recipient try/catch + status update; personalisation substitution; injects tracking pixel + wrapped links + unsubscribe footer; rate-limited; resume-safe (only `pending`). |
| `CampaignService::stats` | `stats(campaignId): CampaignStatsData` | Funnel per variant (open/click/bounce/unsub). |
| `DispatchScheduledCampaignsCommand` | scheduled (5 min) | Picks up `scheduled` campaigns whose time has arrived; `scheduled → sending`; dispatches the batch chain. |

Public token controllers (`TrackOpenController`, `TrackClickController`, `UnsubscribeController`) update `mkt_campaign_recipients` and write to `mkt_unsubscribes`.

## State Machine

| State | → | Trigger | Side effects |
|---|---|---|---|
| `draft` | `scheduled` | `marketing.campaigns.send` | recipients materialised (snapshot), variants assigned |
| `scheduled` | `sending` | scheduler reaches time / send-now | batched job chain dispatched |
| `sending` | `sent` | all batches done | counts finalised |
| `sending` | `failed` | infrastructure failure | resumable (pending recipients only) |

`spatie/laravel-model-states` — classes in `app/States/Marketing/Campaign/`.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `CampaignResource` | Campaigns | #1 CRUD resource | composer, audience picker, test-send, stats on view page |
| `CampaignStatsWidget` | Campaigns | #6 widget | open/click funnel per variant |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('marketing.campaigns.view-any')
        && BillingService::hasModule('marketing.campaigns');
}
```

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `DispatchScheduledCampaignsCommand` | default | every 5 min | status transition guard |
| `SendCampaignBatchJob` | notifications | chained | recipient `pending` guard — resume-safe |

See [[../../../architecture/queue-jobs]].

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/patterns/states]] · [[../../../architecture/email]]
