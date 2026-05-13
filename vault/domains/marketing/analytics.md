---
type: module
domain: Marketing
panel: marketing
module-key: marketing.analytics
status: planned
color: "#4ADE80"
---

# Analytics

> UTM tracking, conversion funnels, and multi-touch channel attribution in a read-only reporting view.

**Panel:** `marketing`
**Module key:** `marketing.analytics`

## What It Does

Marketing Analytics is the read-only reporting layer that answers the question "which channels and campaigns drive revenue?" It ingests UTM-tagged traffic data, web events, email interactions, and CRM deal data to assemble multi-touch attribution reports. Conversion funnels show where prospects drop off between channel touchpoint, lead capture, opportunity, and won deal. Data is refreshed on a daily schedule and cannot be modified from this module.

## Features

### Core
- UTM traffic report: sessions, source, medium, campaign, and content breakdown over any date range
- Channel performance: visits, leads, opportunities, and won deals per source/medium combination
- Conversion funnel: configurable stages (visit → lead → MQL → SQL → opportunity → won) with drop-off rates at each step
- Campaign performance: per-campaign summary of spend, leads generated, pipeline created, revenue attributed
- Email analytics aggregation: delivered, opened, clicked, converted summary across all sends

### Advanced
- Multi-touch attribution: first-touch, last-touch, linear, time-decay, and W-shaped models — switch model without changing underlying data
- Revenue attribution: apply selected model to closed deals → revenue credit per channel
- Pipeline attribution: open pipeline influenced by each channel (leading indicator)
- Attribution window configuration: set the look-back window (30, 60, 90 days)
- Cohort analysis: group leads by acquisition month and track conversion rate over time
- Custom report builder: select dimensions (channel, campaign, geography) and metrics (leads, MQLs, revenue) to compose ad-hoc reports

### AI-Powered
- Insight summary: weekly AI narrative of "what changed this week and why"
- Budget recommendation: suggest reallocation of spend based on ROI per channel

## Data Model

```erDiagram
    mkt_touchpoints {
        ulid id PK
        ulid company_id FK
        ulid contact_id FK
        string session_id
        string channel
        string utm_source
        string utm_medium
        string utm_campaign
        string utm_content
        ulid campaign_id FK
        timestamp occurred_at
    }

    mkt_attribution_results {
        ulid id PK
        ulid company_id FK
        ulid opportunity_id FK
        ulid touchpoint_id FK
        string model
        decimal credit
        decimal attributed_revenue
        date calculated_on
    }

    mkt_funnel_definitions {
        ulid id PK
        ulid company_id FK
        string name
        json stages
        timestamps timestamps
    }

    mkt_touchpoints ||--o{ mkt_attribution_results : "credited in"
```

| Table | Purpose |
|---|---|
| `mkt_touchpoints` | Every marketing touch per contact session |
| `mkt_attribution_results` | Computed revenue credit per touchpoint per model |
| `mkt_funnel_definitions` | Configurable funnel stage definitions |

## Permissions

```
marketing.analytics.view-any
marketing.analytics.export
marketing.analytics.manage-funnels
marketing.analytics.manage-attribution-model
marketing.analytics.view-revenue
```

## Filament

**Resource class:** none (read-only pages only)
**Pages:** none
**Custom pages:** `MarketingAnalyticsPage` (main dashboard with UTM report, funnel, and attribution), `AttributionReportPage` (model comparison view), `CampaignReportPage` (per-campaign drill-down)
**Widgets:** `ChannelAttributionWidget`, `ConversionFunnelWidget`, `TopCampaignsWidget`
**Nav group:** Analytics

## Displaces

| Competitor | Feature Replaced |
|---|---|
| HubSpot Attribution Reporting | Multi-touch attribution and campaign ROI |
| Google Analytics 4 (UTM) | UTM campaign tracking and funnel reporting |
| Rockerbox | Multi-touch attribution for marketing teams |
| Triple Whale | Attributed revenue reporting |

## Related

- [[campaigns]] — campaign performance data sourced here
- [[email-marketing]] — email engagement feeds attribution
- [[lead-capture]] — lead submission events tracked as touchpoints
- [[social-scheduling]] — social link clicks tracked as touchpoints
- [[../analytics/INDEX]] — cross-domain BI builds on marketing attribution data
- [[../crm/INDEX]] — deal and opportunity data for revenue attribution
