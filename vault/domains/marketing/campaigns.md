---
type: module
domain: Marketing
panel: marketing
module-key: marketing.campaigns
status: planned
color: "#4ADE80"
---

# Campaigns

> Plan, execute, and measure multi-channel marketing campaigns from a single workspace.

**Panel:** `marketing`
**Module key:** `marketing.campaigns`

## What It Does

Campaigns is the top-level organiser for all marketing activity. A campaign groups related emails, social posts, landing pages, ads, and content under one goal — with a defined budget, target audience, date range, and KPI set. Every asset created in other marketing modules can be attached to a campaign, giving a unified view of what was sent, to whom, and what it achieved.

## Features

### Core
- Campaign creation with name, type (brand, demand-gen, product launch, event, ABM), goal, start/end dates, and budget
- Campaign brief: objective, target audience, messaging pillars, CTA, success metrics
- Asset association: link emails, landing pages, social posts, ads, and content items
- Status workflow: draft → active → paused → complete → archived
- Budget tracking: planned vs actual spend per campaign
- Team assignments: campaign owner, contributors per asset type

### Advanced
- Multi-campaign calendar view (month/quarter) across all campaign types
- Campaign templates: pre-built structures for common playbooks (webinar, product launch, ABM sequence)
- Campaign cloning: duplicate a past campaign with all its structure intact
- UTM auto-generation: every campaign gets a default UTM set applied to all linked assets
- Approval workflow: draft → legal/brand review → approved → live
- Campaign tagging and filtering: segment by region, product line, channel, quarter

### AI-Powered
- Audience size estimator: predict reachable audience based on segment criteria
- Budget allocation suggestion: recommend spend split across channels based on historical attribution data
- Performance forecast: estimate expected leads and pipeline contribution before launch

## Data Model

```erDiagram
    mkt_campaigns {
        ulid id PK
        ulid company_id FK
        string name
        string type
        string status
        text brief
        decimal budget_planned
        decimal budget_actual
        date starts_at
        date ends_at
        ulid owner_id FK
        json utm_defaults
        json tags
        timestamps timestamps
        softDeletes deleted_at
    }

    mkt_campaign_assets {
        ulid id PK
        ulid campaign_id FK
        string asset_type
        ulid asset_id
        string label
        timestamps timestamps
    }

    mkt_campaign_members {
        ulid id PK
        ulid campaign_id FK
        ulid user_id FK
        string role
        timestamps timestamps
    }

    mkt_campaigns ||--o{ mkt_campaign_assets : "has"
    mkt_campaigns ||--o{ mkt_campaign_members : "has"
```

| Table | Purpose |
|---|---|
| `mkt_campaigns` | Campaign header: goal, budget, dates, status |
| `mkt_campaign_assets` | Polymorphic join to emails, pages, social posts |
| `mkt_campaign_members` | Team assignments per campaign |

## Permissions

```
marketing.campaigns.view-any
marketing.campaigns.create
marketing.campaigns.update
marketing.campaigns.delete
marketing.campaigns.approve
```

## Filament

**Resource class:** `CampaignResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `CampaignCalendarPage` (calendar view of all campaigns), `CampaignBriefPage` (rich brief editor)
**Widgets:** `CampaignPerformanceSummaryWidget` (KPIs for the active campaign in view)
**Nav group:** Campaigns

## Displaces

| Competitor | Feature Replaced |
|---|---|
| HubSpot Campaigns | Campaign planning, asset grouping, reporting |
| Marketo Programs | Program structure and asset association |
| Asana (marketing use) | Campaign project management and brief |

## Related

- [[email-marketing]] — email sends attached to campaigns
- [[social-scheduling]] — social posts tagged to campaigns
- [[landing-pages]] — campaign landing pages
- [[analytics]] — campaign attribution reporting
- [[../crm/INDEX]] — campaigns linked to pipeline and deals
