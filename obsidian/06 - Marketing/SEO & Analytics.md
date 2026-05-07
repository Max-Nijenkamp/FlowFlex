---
tags: [flowflex, domain/marketing, seo, analytics, phase/5]
domain: Marketing
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-07
---

# SEO & Analytics

Track keyword rankings, audit technical SEO, and pull GA4 performance data into FlowFlex. One place to see organic growth, traffic trends, and site health.

**Who uses it:** Marketing team, SEO specialists, content managers
**Filament Panel:** `marketing`
**Depends on:** [[CMS & Website Builder]], GA4 API, Google Search Console API
**Phase:** 5
**Build complexity:** Medium — 4 resources, 2 pages, 4 tables

---

## Features

- **Keyword rank tracking** — monitor Google position for target keywords over time; daily snapshot via API; position delta indicators
- **Rank history charts** — line chart of position over time per keyword; compare multiple keywords on one chart
- **Technical SEO audit** — crawl company website for common SEO issues: missing meta, broken links, duplicate titles, slow pages, missing alt text; scored 0–100
- **Issue prioritisation** — audit issues categorised as critical/warning/suggestion; fix-it guidance per issue type
- **GA4 dashboard integration** — pull sessions, users, bounce rate, avg session duration from GA4 property; daily snapshots stored locally so data is available even when API is down
- **Traffic trend widgets** — week-over-week and month-over-month traffic comparison charts in marketing panel dashboard
- **Redirect management** — create and manage 301/302 redirects directly from FlowFlex; hit counter per rule; detect redirect loops
- **Google Search Console data** — import click, impression, CTR, and position data from GSC (deferred — placeholders present)
- **Competitor tracking placeholder** — track up to 5 competitor domains' keyword overlap; requires third-party API key (e.g. SEMrush)
- **Audit history** — compare audit scores over time to track SEO improvement; list of resolved vs new issues per audit run
- **Backlink monitoring placeholder** — inbound link count and referring domain trend (requires third-party API key)
- **Export audit report** — PDF export of latest audit with recommendations for client or leadership reporting

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `seo_audits`
| Column | Type | Notes |
|---|---|---|
| `url` | string | root URL audited |
| `score` | integer | 0–100 |
| `pages_crawled` | integer | |
| `issues` | json | array of {type, url, severity, description} |
| `recommendations` | json | array of {priority, action} |
| `audited_at` | timestamp | |
| `duration_seconds` | integer nullable | |

### `keyword_rankings`
| Column | Type | Notes |
|---|---|---|
| `keyword` | string | |
| `url` | string nullable | target page URL |
| `position` | integer nullable | null = not in top 100 |
| `previous_position` | integer nullable | prior snapshot |
| `search_volume` | integer nullable | monthly search volume |
| `recorded_at` | date | |
| `source` | enum | `manual`, `api` |

### `ga4_snapshots`
| Column | Type | Notes |
|---|---|---|
| `property_id` | string | GA4 property ID |
| `sessions` | integer | |
| `users` | integer | |
| `new_users` | integer | |
| `bounce_rate` | decimal(5,2) | % |
| `avg_session_duration` | decimal(8,2) | seconds |
| `pageviews` | integer | |
| `recorded_at` | timestamp | |
| `period_start` | date | |
| `period_end` | date | |

### `redirect_rules`
| Column | Type | Notes |
|---|---|---|
| `from_path` | string | e.g. "/old-page" |
| `to_path` | string | e.g. "/new-page" |
| `type` | enum | `301`, `302` |
| `is_active` | boolean default true | |
| `hit_count` | integer default 0 | |
| `last_hit_at` | timestamp nullable | |
| `notes` | string nullable | |

---

## Events Fired

None — SEO & Analytics is a read/pull module.

---

## Events Consumed

None — reads from GA4 API, Google Search Console API, and internal crawl engine.

---

## Permissions

```
marketing.seo-audits.view
marketing.seo-audits.create
marketing.keyword-rankings.view
marketing.keyword-rankings.create
marketing.keyword-rankings.edit
marketing.keyword-rankings.delete
marketing.ga4-snapshots.view
marketing.redirect-rules.view
marketing.redirect-rules.create
marketing.redirect-rules.edit
marketing.redirect-rules.delete
```

---

## Related

- [[Marketing Overview]]
- [[CMS & Website Builder]]
- [[Ad Campaign Management]]
