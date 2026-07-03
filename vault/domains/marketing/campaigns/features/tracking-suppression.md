---
domain: marketing
module: campaigns
feature: tracking-suppression
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Tracking & Suppression

Send the batch, track opens/clicks/bounces per recipient, and honour the mandatory unsubscribe.

## Behaviour

- `SendCampaignBatchJob` sends chunked, rate-limited, per-recipient with personalisation; injects tracking pixel, wrapped links, and an unsubscribe footer.
- Open pixel hit → `opened_at`; wrapped-link hit → `clicked_at` (then redirect); provider bounce → `bounced_at + status=failed`.
- Unsubscribe link → `UnsubscribeController` writes `mkt_unsubscribes` (email, company) and stamps `unsubscribed_at`; future sends (campaigns + sequences) exclude it.
- Resume-safe: re-run sends only `pending` recipients.

## UI

- **Kind**: background + widget
- **Page**: `CampaignStatsWidget` on the `CampaignResource` view page (`/marketing/campaigns/{id}`); the send itself is a background job; Track/Unsubscribe are public token endpoints (no in-app page).
- **Layout**: funnel widget — sent → delivered → opened → clicked, plus bounce/unsub counts (per variant).
- **Key interactions**: none in-app for the recipient side; marketer watches the funnel update.
- **States**: empty (not yet sent → "schedule to see stats") · loading (polling) · error (send failure banner + resume) · selected (variant funnel).
- **Gating**: widget reads with `marketing.campaigns.view-any`. Public token routes run outside the session guard, throttled ([[../security]]).

## Data

- Owns / writes: `mkt_campaign_recipients` status/timestamps, `mkt_unsubscribes` (own module).
- Reads: recipient rows (own).
- Cross-domain writes: none — no CRM timeline touch fired in v1 ([[../../../../security/data-ownership]], open in [[../unknowns]]).

## Relations

- Feeds: recipient statuses aggregated by [[../../marketing-analytics/_module|Marketing Analytics]].
- Shared entity: `mkt_unsubscribes` honoured by [[../../email-sequences/_module|Email Sequences]].

## Test Checklist

### Unit
- [ ] Open pixel hit stamps `opened_at`; wrapped-link hit stamps `clicked_at` then issues the redirect; provider bounce sets `bounced_at` + `status=failed`
- [ ] Unsubscribe writes an `mkt_unsubscribes` row (email, company) and stamps `unsubscribed_at`

### Feature (Pest)
- [ ] After unsubscribe, a subsequent campaign **and** sequence send excludes the address
- [ ] Re-running `SendCampaignBatchJob` sends only `pending` recipients (already-sent rows untouched)
- [ ] A public token from company A never resolves a company B recipient (no token → 404); tenant boundary holds without a session

### Livewire
- [ ] `CampaignStatsWidget` renders the per-variant funnel and reads only with `marketing.campaigns.view-any`

## Unknowns

- Should an open/click log a CRM activity touch? Currently no event fired. See [[../unknowns]].

## Related

- [[../_module|Campaigns]] · [[../security]] · [[../architecture]]
