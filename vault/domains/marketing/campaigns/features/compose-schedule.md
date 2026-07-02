---
domain: marketing
module: campaigns
feature: compose-schedule
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Compose & Schedule

Build a campaign — pick audience, write the email, preview, and send now or schedule.

## Behaviour

- Pick audience (CRM segment or manual contact list), set from name/email + subject.
- Compose body in Tiptap (purified) with `{{first_name}}` merge fields.
- Preview renders against a sample contact.
- Send now → `draft → scheduled` immediately; or set `scheduled_at` (picked up by `DispatchScheduledCampaignsCommand`).

## UI

- **Kind**: simple-resource
- **Page**: `CampaignResource` (`/marketing/campaigns`) — Campaigns nav group.
- **Layout**: table (name, status, scheduled_at, sent counts) + form (audience picker → composer → preview panel); view page shows the funnel.
- **Key interactions**: pick audience → compose → test-send → "Send now" / "Schedule"; status badge tracks lifecycle.
- **States**: empty (no campaigns → CTA) · loading (preview render skeleton) · error (no audience / invalid from_email) · selected (view page funnel).
- **Gating**: `marketing.campaigns.create`; send requires `marketing.campaigns.send`.

## Data

- Owns / writes: `mkt_campaigns`, `mkt_campaign_recipients` (own module).
- Reads: `SegmentService::contacts()` (CRM segments), contact deliverability flag — read-only.
- Cross-domain writes: none — audiences read via CRM service ([[../../../../security/data-ownership]]).

## Relations

- Reads: `SegmentService::contacts()` from [[../../../crm/customer-segments/_module|crm.segments]].
- Feeds: `CampaignService::schedule` → [[audience-materialisation]] → [[tracking-suppression]].
- Shared entity: CRM `contacts` (owned by crm, read-only).

## Unknowns

- Block email builder vs Tiptap-only for v1 *(assumed Tiptap)*. See [[../unknowns]].

## Related

- [[../_module|Campaigns]] · [[audience-materialisation]] · [[../architecture]]
