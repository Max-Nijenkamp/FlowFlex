---
domain: marketing
module: campaigns
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Campaigns

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `CampaignResource` | #1 CRUD resource | tweaks: state-badge-column, view-page-tabs, custom-header-actions (send / test-send) | composer, audience picker, stats on view page; send + test-send are comms actions → each names the `panel-action` limiter ([[./security]]) |
| `CampaignStatsWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | open/click funnel per variant; widget polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('marketing.campaigns.view-any') && BillingService::hasModule('marketing.campaigns')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state this explicitly — Filament does not auto-gate them; this module has none (resource + widget only). The public Track / Click / Unsubscribe surfaces run outside the panel and the session guard on signed/opaque per-recipient tokens ([[./security]]), not Filament artifacts.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Campaign CRUD (draft form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Schedule (`draft → scheduled`, materialises recipient snapshot) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read status, validate, write per [[../../../architecture/patterns/states]] |
| Dispatch / send transitions (`scheduled → sending → sent`/`failed`) | Pessimistic | status transition guard under `lockForUpdate()` per [[../../../architecture/patterns/states]]; scheduler + batch-job `pending`-only guard makes re-runs resume-safe |
| Recipient tracking stamps (`opened_at` / `clicked_at` / `bounced_at` / `unsubscribed_at` from public token endpoints) | n/a | Single-writer per recipient row keyed by an opaque token; monotonic timestamp stamps, no concurrent editors — last-write-wins is correct here |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `DispatchScheduledCampaignsCommand` | default | every 5 min | status transition guard |
| `SendCampaignBatchJob` | notifications | chained | recipient `pending` guard — resume-safe |

See [[../../../architecture/queue-jobs]].

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/patterns/states]] · [[../../../architecture/email]]
