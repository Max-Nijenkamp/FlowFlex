---
type: module
domain: Marketing
panel: marketing
module-key: marketing.landing-pages
status: planned
color: "#4ADE80"
---

# Landing Pages

> Build conversion-focused campaign pages with a drag-and-drop editor, embedded lead forms, and built-in A/B testing.

**Panel:** `marketing`
**Module key:** `marketing.landing-pages`

## What It Does

Landing Pages provides a dedicated builder for standalone campaign pages — no site navigation, single focused CTA, and matched message to the ad that drove the click. Pages are distinct from CMS pages: they exist to convert a visitor into a lead or customer. Each page integrates with Lead Capture for form submissions, feeds contacts into CRM, and supports A/B testing of variants. Published pages live on a FlowFlex subdomain or a custom domain.

## Features

### Core
- Drag-and-drop block editor: hero, features grid, social proof, pricing table, FAQ, CTA, form, video, countdown timer
- Mobile responsive preview at 375 px, 768 px, and 1280 px breakpoints
- Template library: lead generation, webinar registration, free trial, product launch, ebook download, event registration
- Publish to `pages.{company}.flowflex.io` subdomain or a custom domain
- SEO controls: title tag, meta description, remove from sitemap toggle
- Page status: draft → published → archived

### Advanced
- Custom HTML/CSS/JS block for advanced customisation needs
- UTM parameter pass-through: preserve campaign attribution from ad click through to form submission
- Exit-intent popup: detect mouse leaving viewport and show a retention offer
- Sticky CTA bar: scrolls with page for persistent conversion prompt
- Tracking pixel support: Facebook Pixel, Google Tag Manager, LinkedIn Insight Tag
- Embed mode: embed page as iframe or JS snippet on an existing website

### AI-Powered
- Copy assistant: generate headline, subheadline, and body copy from a campaign brief
- CRO suggestions: flag low-contrast CTAs, missing trust signals, or long form fields

## Data Model

```erDiagram
    mkt_landing_pages {
        ulid id PK
        ulid company_id FK
        ulid campaign_id FK
        string name
        string slug
        string custom_domain
        string status
        boolean remove_from_sitemap
        string ab_winner_variant_id
        integer ab_split_percent
        json seo_meta
        timestamps timestamps
    }

    mkt_lp_variants {
        ulid id PK
        ulid landing_page_id FK
        string label
        json blocks
        integer visits
        integer conversions
        decimal conversion_rate
        timestamps timestamps
    }

    mkt_lp_submissions {
        ulid id PK
        ulid landing_page_id FK
        ulid variant_id FK
        ulid contact_id FK
        json field_values
        string source_utm
        timestamp submitted_at
    }

    mkt_landing_pages ||--o{ mkt_lp_variants : "has"
    mkt_landing_pages ||--o{ mkt_lp_submissions : "collects"
```

| Table | Purpose |
|---|---|
| `mkt_landing_pages` | Page metadata, domain, A/B configuration |
| `mkt_lp_variants` | Content variants (A and B) with conversion stats |
| `mkt_lp_submissions` | Form submission records per variant |

## Permissions

```
marketing.landing-pages.view-any
marketing.landing-pages.create
marketing.landing-pages.publish
marketing.landing-pages.manage-ab-tests
marketing.landing-pages.delete
```

## Filament

**Resource class:** `LandingPageResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `LandingPageBuilderPage` (full-screen block editor), `LandingPageAnalyticsPage` (per-page and per-variant stats)
**Widgets:** none
**Nav group:** Content

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Unbounce | Landing page builder and A/B testing |
| Instapage | Page builder and conversion analytics |
| Leadpages | Template-based landing pages |
| Swipe Pages | Mobile-focused landing pages |
| ClickFunnels | Funnel-step pages |

## Related

- [[campaigns]] — pages belong to campaigns
- [[lead-capture]] — form submissions route through lead capture
- [[a-b-testing]] — A/B test variants and winner selection
- [[analytics]] — traffic source and conversion funnel reporting
- [[../crm/INDEX]] — form submissions create or update CRM contacts
