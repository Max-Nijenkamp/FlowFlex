---
type: module
domain: Marketing
domain-key: marketing
panel: marketing
module-key: marketing.analytics
status: planned
priority: p3
depends-on: [marketing.campaigns, core.billing, core.rbac]
soft-depends: [marketing.forms, marketing.landing-pages, marketing.sequences, marketing.utm]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: marketing.analytics
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Marketing Analytics

Campaign performance, form conversion rates, landing page visits, and UTM attribution dashboards. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/marketing/campaigns\|marketing.campaigns]] | core metrics source |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | forms / landing-pages / sequences / utm | their sections hidden when inactive |

---

## Core Features

- Campaign performance: open/click/bounce/unsubscribe rates over time
- Form conversion rates: views vs submissions per form
- Landing page funnel: visits → form starts → conversions
- Sequence performance: step-by-step engagement
- UTM attribution: which sources/campaigns drive contacts and conversions
- Channel comparison: email vs landing pages vs forms
- Lead source breakdown
- Export reports (CSV)

---

## Data Model

No additional tables. Aggregates from `mkt_campaigns`, `mkt_form_submissions`, `mkt_landing_pages`, `mkt_utm_touches`, sequence enrolments.

## DTOs

Output only: `MarketingMetricsData` — campaign series, form conversion table, page funnel, sequence engagement, attribution.

## Services & Actions

- `MarketingAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): MarketingMetricsData` — aggregate queries, no N+1; soft-dep sections null when module inactive

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:marketing:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Analytics

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `MarketingDashboardPage` | #6 dashboard page + apex charts | date range filter; soft-dep widgets conditional; polling 60s |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('marketing.analytics.view-any') && BillingService::hasModule('marketing.analytics')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a rate limiter (throttle) on the CSV export action in the spec.

---

## Permissions

`marketing.analytics.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Campaign rate math over fixtures
- [ ] Form conversion = submissions/views
- [ ] Inactive soft-dep sections hidden, no errors
- [ ] CSV export

---

## Build Manifest

```
app/Data/Marketing/MarketingMetricsData.php
app/Services/Marketing/MarketingAnalyticsService.php
app/Filament/Marketing/Pages/MarketingDashboardPage.php
app/Filament/Marketing/Widgets/{CampaignPerformanceWidget,FormConversionWidget,AttributionWidget}.php
tests/Feature/Marketing/MarketingAnalyticsTest.php
```

---

## Related

- [[domains/marketing/utm-tracking]]
- [[domains/marketing/campaigns]]
- [[architecture/caching]]
