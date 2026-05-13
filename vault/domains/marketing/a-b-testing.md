---
type: module
domain: Marketing
panel: marketing
module-key: marketing.ab-testing
status: planned
color: "#4ADE80"
---

# A/B Testing

> Configure, run, and conclude A/B tests on emails and landing pages with statistical significance tracking and one-click winner promotion.

**Panel:** `marketing`
**Module key:** `marketing.ab-testing`

## What It Does

A/B Testing provides the experimentation layer for marketing assets. Teams create a test by defining a control (variant A) and challenger (variant B), set a traffic or send split, define the success metric, and run the test for a defined duration. The module tracks results per variant in real time, calculates statistical significance using a Z-test, and surfaces a clear winner recommendation. The winning variant can be promoted in one click — pausing the losing variant and routing all future traffic or sends to the winner.

## Features

### Core
- Test types: email subject line, email content, landing page headline, landing page CTA, landing page layout
- Variant configuration: create variant B by duplicating the control and editing the specific element
- Traffic split: 50/50 default, configurable from 10/90 to 90/10
- Test duration: set a fixed end date or a target sample size
- Success metric: conversion rate (form submit), open rate (email), click rate (email), click-through rate (landing page)
- Results dashboard: visits/sends, conversions, conversion rate, and uplift per variant

### Advanced
- Statistical significance indicator: Z-test with configurable confidence threshold (95% or 99%)
- Early stop option: end test early if significance is reached before scheduled end date
- Multi-variant (A/B/n): up to 4 variants simultaneously with equal split distribution
- Segment-level results: break down results by traffic source, device type, or geography
- Test archive: full history of completed tests with winner, uplift, and date range

### AI-Powered
- Hypothesis generator: suggest A/B test ideas for a given asset based on CRO best practices
- Significance predictor: estimate required sample size to reach significance at desired confidence level

## Data Model

```erDiagram
    mkt_ab_tests {
        ulid id PK
        ulid company_id FK
        string name
        string asset_type
        ulid asset_id
        string success_metric
        integer confidence_threshold
        string status
        timestamp started_at
        timestamp ended_at
        ulid winner_variant_id FK
        timestamps timestamps
    }

    mkt_ab_variants {
        ulid id PK
        ulid test_id FK
        string label
        integer traffic_split_pct
        json config_overrides
        integer impressions
        integer conversions
        decimal conversion_rate
        boolean is_winner
        timestamps timestamps
    }

    mkt_ab_tests ||--o{ mkt_ab_variants : "has"
```

| Table | Purpose |
|---|---|
| `mkt_ab_tests` | Test configuration and result summary |
| `mkt_ab_variants` | Per-variant definition and live conversion counts |

## Permissions

```
marketing.ab-testing.view-any
marketing.ab-testing.create
marketing.ab-testing.manage
marketing.ab-testing.conclude
marketing.ab-testing.delete
```

## Filament

**Resource class:** `AbTestResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `AbTestResultsPage` (real-time variant comparison with significance gauge)
**Widgets:** `ActiveTestsWidget` (tests currently running with significance progress)
**Nav group:** Campaigns

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Google Optimize | Landing page A/B testing |
| VWO (Visual Website Optimizer) | A/B and multivariate testing |
| Optimizely | Web experimentation platform |
| Mailchimp A/B | Email subject line testing |

## Related

- [[email-marketing]] — subject line and content tests run against email sends
- [[landing-pages]] — headline, CTA, and layout tests on landing page variants
- [[analytics]] — test results feed into attribution and funnel analysis
- [[campaigns]] — tests scoped to a campaign context
