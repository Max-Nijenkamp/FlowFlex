---
domain: marketing
module: campaigns
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Campaigns — API (DTOs)

Parent: [[_module]] · See also [[architecture]]

No cross-domain service contract exposed; **fires/consumes no events**. Input via `spatie/laravel-data` DTOs.

## DTOs

### CreateCampaignData (input → `CampaignService::schedule`)

| Field | Type | Validation |
|---|---|---|
| name | string | required |
| subject | string | required |
| subject_b | string? | required if A/B enabled |
| split_percent | int? | 10–50 when A/B |
| from_name / from_email | string | required; from_email valid |
| segment_id | ulid? | OR contact_ids[] — one required |
| contact_ids | ulid[]? | manual list alternative |
| content | text | required, purified |
| scheduled_at | datetime? | future; null = send now |

### CampaignStatsData (output ← `CampaignService::stats`)

Funnel per variant: `sent`, `delivered`, `opened`, `clicked`, `bounced`, `unsubscribed` counts + rates.

## Reads (cross-domain)

`SegmentService::contacts(segmentId)` from [[../../crm/customer-segments/_module|crm.segments]] — read-only recipient resolution at schedule time.

## Events

None fired, none consumed. See [[../../../architecture/event-bus]].

## Related

- [[_module]] · [[architecture]] · [[security]]
