---
domain: marketing
module: campaigns
feature: ab-testing
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: A/B Subject Testing

Test two subject lines on a split of the audience; winner measured by open rate.

## Behaviour

- Campaign carries `subject` + `subject_b` + `split_percent` (10–50).
- At materialisation, recipients are randomly tagged `variant = a | b` per the split.
- Each variant sends with its subject; opens tracked per variant.
- Winner surfaced in `CampaignStatsData` by open rate *(assumed — auto-send-to-remainder not specced, see [[../unknowns]])*.

## UI

- **Kind**: simple-resource
- **Page**: within `CampaignResource` form (A/B toggle reveals `subject_b` + split slider) + `CampaignStatsWidget` per-variant funnel on the view page.
- **Layout**: toggle + second subject field + split-percent slider; stats show two funnels side by side.
- **Key interactions**: enable A/B → enter subject_b + split → schedule; view page compares variant open/click rates.
- **States**: empty (A/B off → single subject) · loading (stats fetch) · error (subject_b required when A/B on) · selected (winning variant highlighted).
- **Gating**: `marketing.campaigns.create` to configure; `marketing.campaigns.view-any` to read stats.

## Data

- Owns / writes: `mkt_campaigns.subject_b/split_percent`, `mkt_campaign_recipients.variant` (own module).
- Reads: none cross-domain.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Feeds: variant column consumed by [[tracking-suppression]] stats aggregation and [[../../marketing-analytics/_module|Marketing Analytics]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Variant assignment splits recipients `a`/`b` in the configured `split_percent` ratio (deterministic given a seed)
- [ ] Winner-by-open-rate picks the higher `opened/sent` variant; ties resolve to `a` *(assumed)*

### Feature (Pest)
- [ ] Scheduling an A/B campaign tags each `mkt_campaign_recipients` row with a `variant`; `CampaignStatsData` returns two funnels
- [ ] `subject_b` required (validation) when A/B is enabled; `split_percent` accepted only in 10–50

### Livewire
- [ ] A/B toggle reveals the `subject_b` field + split slider; leaving `subject_b` empty blocks schedule with a field error
- [ ] Stats widget highlights the winning variant; configuring needs `marketing.campaigns.create`, reading needs `marketing.campaigns.view-any`

## Unknowns

> [!warning] UNVERIFIED
> Does the winner auto-send to the un-sent remainder, or is the split the entire audience? Unspecced. See [[../unknowns]].

## Related

- [[../_module|Campaigns]] · [[audience-materialisation]] · [[tracking-suppression]]
