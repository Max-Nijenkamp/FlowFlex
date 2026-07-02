---
domain: marketing
module: campaigns
feature: audience-materialisation
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Audience Materialisation

Snapshot the recipient list at schedule time so the send is deterministic and suppression-clean.

## Behaviour

- At `draft → scheduled`, resolve the segment via `SegmentService::contacts()` (or use the manual list).
- Dedupe on `(campaign_id, contact_id)`; exclude anyone on `mkt_unsubscribes`; exclude `email_deliverable=false`.
- Assign A/B variant per `split_percent` (see [[ab-testing]]).
- Write one `mkt_campaign_recipients` row per surviving recipient with `status=pending`.

## UI

- **Kind**: background
- **Trigger**: `CampaignService::schedule` (invoked from [[compose-schedule]]'s "Send now" / "Schedule"). No dedicated page; results surface as the recipient count + funnel on the campaign view page.
- **States**: n/a (background); errors bubble to the composer as a toast (empty audience → block schedule).
- **Gating**: runs under the sender's `marketing.campaigns.send`.

## Data

- Owns / writes: `mkt_campaign_recipients` (own module).
- Reads: `SegmentService::contacts()`, contact deliverability, `mkt_unsubscribes` — read-only lookups.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: `SegmentService::contacts()` from [[../../../crm/customer-segments/_module|crm.segments]].
- Feeds: materialised rows consumed by `SendCampaignBatchJob` ([[tracking-suppression]]).
- Shared entity: `mkt_unsubscribes` (own module, shared with sequences).

## Unknowns

- Manual-list max size / chunking threshold not fixed. See [[../unknowns]].

## Related

- [[../_module|Campaigns]] · [[tracking-suppression]] · [[ab-testing]] · [[../architecture]]
