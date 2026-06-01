---
type: module
domain: Marketing
panel: marketing
module-key: marketing.analytics
status: planned
color: "#4ADE80"
---

# Marketing Analytics

Campaign performance, form conversion rates, landing page visits, and UTM attribution dashboards.

## Core Features

- Campaign performance: open/click/bounce/unsubscribe rates over time
- Form conversion rates: views vs submissions per form
- Landing page funnel: visits → form starts → conversions
- Sequence performance: step-by-step engagement
- UTM attribution: which sources/campaigns drive contacts and conversions
- Channel comparison: email vs landing pages vs forms
- Lead source breakdown
- Export reports

## Data Model

No additional tables. Aggregates from `mkt_campaigns`, `mkt_form_submissions`, `mkt_landing_pages`, and UTM tracking data.

## Filament

**Nav group:** Analytics

- `MarketingDashboardPage` (custom dashboard) — chart widgets (leandrocfe/filament-apex-charts)

## Related

- [[domains/marketing/utm-tracking]]
- [[domains/marketing/campaigns]]
- [[architecture/performance]]
