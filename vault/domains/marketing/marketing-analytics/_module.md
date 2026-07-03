---
domain: marketing
module: marketing-analytics
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Marketing Analytics

Campaign performance, form conversion, landing-page funnel, sequence engagement, and UTM attribution — one read-only dashboard. **Owns no tables.**

- **module-key:** `marketing.analytics` · **panel:** marketing · **priority:** p3
- **fires-events:** none · **consumes-events:** none
- **tables:** none — pure aggregation over other marketing modules' tables

## Module-key

**Priority:** p3
**Panel:** /marketing
**Permission prefix:** `marketing.analytics`
**Tables:** none — pure aggregation over other marketing modules' tables

## What it does

- Campaign performance: open/click/bounce/unsubscribe rates over time.
- Form conversion: views vs submissions per form.
- Landing-page funnel: visits → form starts → conversions.
- Sequence performance: step-by-step engagement.
- UTM attribution: sources/campaigns driving contacts + conversions.
- Channel comparison (email vs landing vs forms); lead-source breakdown; CSV export.

Soft-dep sections are **hidden when the source module is inactive** — no errors, just an absent section.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../campaigns/_module\|marketing.campaigns]] | core metrics source |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Soft | [[../forms/_module\|forms]] / [[../landing-pages/_module\|landing-pages]] / [[../email-sequences/_module\|sequences]] / [[../utm-tracking/_module\|utm]] | their sections hidden when inactive |

## Sibling notes

- [[architecture]] — `MarketingAnalyticsService`, dashboard page, widgets, caching
- [[api]] — `MarketingMetricsData` output DTO (no input DTOs, no tables)
- [[security]] — read-only gating, export throttle, cross-domain read boundary
- [[decisions]] · [[unknowns]]
- [[features/marketing-dashboard]]

> No `data-model.md`: this module owns **no tables**. It reads (never writes) `mkt_campaigns`, `mkt_form_submissions`, `mkt_landing_pages`, `mkt_utm_touches`, sequence enrolments — each owned by its own module.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | campaign stats | [[../campaigns/_module\|marketing.campaigns]] | open/click/bounce (read-only) |
| Reads | form conversion | [[../forms/_module\|marketing.forms]] | views vs submissions |
| Reads | page funnel | [[../landing-pages/_module\|marketing.landing-pages]] | visits/conversions |
| Reads | step engagement | [[../email-sequences/_module\|marketing.sequences]] | per-step rates |
| Reads | attribution | [[../utm-tracking/_module\|marketing.utm]] | source/campaign |

No cross-domain **domain events** fired or consumed ([[../../../architecture/event-bus]]).

**Data ownership:** owns and writes **nothing**. It aggregates read-only across the other marketing modules' tables via their read models/services. A pure read surface — the cleanest possible data-ownership posture ([[../../../security/data-ownership]]).

## Build Manifest

```
app/Data/Marketing/MarketingMetricsData.php
app/Services/Marketing/MarketingAnalyticsService.php
app/Filament/Marketing/Pages/MarketingDashboardPage.php
app/Filament/Marketing/Widgets/{CampaignPerformanceWidget,FormConversionWidget,AttributionWidget}.php
tests/Feature/Marketing/MarketingAnalyticsTest.php
```

## Related

- [[../utm-tracking/_module|UTM Tracking]] · [[../campaigns/_module|Campaigns]] · [[../../../architecture/caching]]
