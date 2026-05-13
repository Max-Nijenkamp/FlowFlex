---
type: module
domain: Marketing
panel: marketing
module-key: marketing.seo
status: planned
color: "#4ADE80"
---

# SEO Tools

> Track keyword rankings, audit page health, and monitor competitor positions without a separate Ahrefs or SEMrush subscription.

**Panel:** `marketing`
**Module key:** `marketing.seo`

## What It Does

SEO Tools gives marketing teams visibility into organic search performance across three areas: keyword rank tracking (where does the site rank for target keywords over time), page-level SEO audit (what technical and on-page issues reduce ranking potential), and competitor tracking (how do competitor domains rank for the same keywords). Data surfaces inside FlowFlex so teams can act on SEO insights without switching tools.

## Features

### Core
- Keyword rank tracking: add target keywords and track position (1–100+) daily for any domain
- Search engine and geography selection: Google, Bing; country and city-level for local SEO
- Position history chart: rank trend over 30, 90, 180 days per keyword
- Page SEO audit: crawl all pages of the connected domain and report issues (missing title tags, duplicate meta, slow load, broken links, missing alt text, thin content)
- Audit issue severity: critical, warning, notice — with fix guidance per issue type
- Google Search Console integration: pull impressions, clicks, CTR, and average position from GSC

### Advanced
- Competitor rank tracking: add up to 5 competitor domains and compare positions for the same keyword set
- Keyword grouping: organise keywords by topic cluster or content pillar
- SERP feature tracking: track featured snippet, people-also-ask, image pack, local pack positions
- Keyword opportunity finder: keywords where the site ranks position 11–20 (low-hanging fruit for optimisation)
- Scheduled audits: weekly site crawl with change detection (new issues since last audit)
- Content gap analysis: keywords competitors rank for but the company does not

### AI-Powered
- Meta tag generator: produce optimised title tag and meta description from page content
- Content brief generator: suggested headings, word count, and semantically related terms for a target keyword

## Data Model

```erDiagram
    mkt_seo_projects {
        ulid id PK
        ulid company_id FK
        string domain
        string name
        string gsc_integration_token
        timestamps timestamps
    }

    mkt_seo_keywords {
        ulid id PK
        ulid project_id FK
        string keyword
        string country_code
        string search_engine
        string topic_cluster
        timestamps timestamps
    }

    mkt_seo_rankings {
        ulid id PK
        ulid keyword_id FK
        integer position
        string url
        date checked_on
    }

    mkt_seo_audit_issues {
        ulid id PK
        ulid project_id FK
        string page_url
        string issue_type
        string severity
        text description
        text fix_guidance
        boolean resolved
        date audited_on
    }

    mkt_seo_projects ||--o{ mkt_seo_keywords : "tracks"
    mkt_seo_keywords ||--o{ mkt_seo_rankings : "has"
    mkt_seo_projects ||--o{ mkt_seo_audit_issues : "surfaces"
```

| Table | Purpose |
|---|---|
| `mkt_seo_projects` | Domain-level project configuration |
| `mkt_seo_keywords` | Target keywords per project |
| `mkt_seo_rankings` | Daily rank snapshots per keyword |
| `mkt_seo_audit_issues` | Page-level audit findings |

## Permissions

```
marketing.seo.view-any
marketing.seo.manage-projects
marketing.seo.run-audit
marketing.seo.manage-keywords
marketing.seo.export
```

## Filament

**Resource class:** `SeoProjectResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `KeywordRankingPage` (rank trend charts), `SiteAuditPage` (issue list with severity filters)
**Widgets:** `SeoOverviewWidget` (top movers up/down, critical issues count)
**Nav group:** Analytics

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Ahrefs | Keyword tracking, site audit, competitor analysis |
| SEMrush | Rank tracking and content gap analysis |
| Moz Pro | Page authority and keyword difficulty data |
| Screaming Frog | On-page site crawl and issue detection |

## Related

- [[content-calendar]] — SEO keyword targets drive content planning
- [[analytics]] — organic traffic feeds into attribution reporting
- [[campaigns]] — SEO content campaigns tracked alongside paid
- [[landing-pages]] — audit landing page SEO health
