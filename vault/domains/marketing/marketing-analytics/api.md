---
domain: marketing
module: marketing-analytics
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Marketing Analytics — API (DTOs)

Parent: [[_module]] · See also [[architecture]]

Output-only. No input DTOs (date range is method args), no tables, **fires/consumes no events**.

## DTOs

### MarketingMetricsData (output ← `MarketingAnalyticsService::metrics`)

| Section | Contents | Source module |
|---|---|---|
| campaignSeries | open/click/bounce/unsub over time | [[../campaigns/_module\|campaigns]] |
| formConversion | views vs submissions per form | [[../forms/_module\|forms]] (soft) |
| pageFunnel | visits → starts → conversions | [[../landing-pages/_module\|landing-pages]] (soft) |
| sequenceEngagement | per-step rates | [[../email-sequences/_module\|sequences]] (soft) |
| attribution | source/medium/campaign → contacts + value | [[../utm-tracking/_module\|utm]] (soft) |

Soft-dep sections are `null` when their module is inactive.

## Reads (cross-domain, all read-only)

Aggregates over each source module's tables/read models. Never writes.

## Events

None ([[../../../architecture/event-bus]]).

## Related

- [[_module]] · [[architecture]] · [[security]]
