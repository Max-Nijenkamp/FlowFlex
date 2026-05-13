---
type: module
domain: Customer Success
panel: cs
module-key: cs.kb-analytics
status: planned
color: "#4ADE80"
---

# Knowledge Base Analytics (CS View)

> See what your customers search for, what they can't find, and which articles fail them — so CS can close content gaps before they become support tickets.

**Panel:** `/cs`
**Module key:** `cs.kb-analytics`

## What It Does

CS managers and content teams need to know whether the knowledge base is actually helping customers self-serve — or quietly failing them. This module gives the CS panel a read-only view into knowledge base search and article engagement data, filtered to the company's customer segments. CS managers can see which searches return zero results (the most actionable content gap signal), which articles are most and least helpful per customer tier, and can create content gap tasks for the Support or Documentation team to act on. All article and search data is read from the Support domain.

## Features

### Core
- Search query log: a table of all knowledge base search queries submitted by the company's customers, with columns for query text, result count, whether the customer clicked a result, and timestamp — filterable by date range and customer segment
- Failed searches view: filtered to searches where `results_count = 0` — sorted by frequency so the highest-volume gaps appear first; this is the primary actionable view for CS teams
- Article engagement: for any article in the knowledge base, show how many times it was viewed by customers of a given segment, average helpfulness rating, and number of times it was viewed without a support ticket being opened within 48 hours (self-service success rate)
- Customer segment filtering: filter all views by customer tier, industry, or plan level — so a CS manager responsible for enterprise accounts can focus on enterprise search gaps specifically

### Advanced
- Content gap tasks: from the Failed Searches view, CS managers can create a content gap task directly — pre-populated with the search query, frequency, and a "Suggested article title" field — assigned to a Support or Documentation team member for content creation
- Article effectiveness by segment: a ranked table of articles filtered to a specific customer segment showing helpfulness score distribution — identify articles that are helpful for SMBs but rated poorly by enterprise customers (different needs)
- Search-to-ticket correlation: for each failed search, show how many customers who searched and found nothing subsequently opened a support ticket — quantifies the cost of the content gap in support volume
- Weekly content gap digest: automated weekly email to the configured CS lead summarising the top 5 new failed search terms, top 3 articles with declining ratings, and any new content gap tasks opened or resolved

### AI-Powered
- Suggested article topics: given the top 20 failed search queries from the past 30 days, AI generates suggested article titles and outlines — each suggestion is a content gap task template that CS can send to the Support/Documentation team with one click
- Cluster analysis: AI clusters semantically similar failed search queries into theme groups (e.g. 15 queries about "export to CSV", "download data", "get my data" are clustered under "Data Export") — so CS sees 5 meaningful gaps instead of 200 raw queries
- Article gap prioritisation: AI scores each content gap by estimated impact (frequency × ticket correlation × customer tier weight) and surfaces a prioritised "Top 5 Gaps to Fix This Week" recommendation

## Data Model

The core article and search data lives in the Support domain. This module adds one table for content gap tasks:

```erDiagram
    kb_article_searches {
        ulid id PK
        ulid company_id FK
        string query
        integer results_count
        ulid clicked_article_id FK "nullable"
        ulid searcher_contact_id FK "nullable"
        timestamps created_at/updated_at
    }

    cs_content_gap_tasks {
        ulid id PK
        ulid company_id FK
        string search_query
        integer search_frequency
        string suggested_title "nullable"
        text suggested_outline "nullable"
        ulid assigned_to FK
        enum status
        ulid created_by FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `kb_article_searches` | This table is defined and owned by the Support domain — CS module reads it via the Support domain's repository interface; CS domain must not write to it |
| `kb_article_searches.searcher_contact_id` | Populated when the searcher is an identified (logged-in) contact; null for anonymous searches |
| `cs_content_gap_tasks.status` | enum: `open` / `in_progress` / `completed` / `wont_fix` |
| `cs_content_gap_tasks` | This table IS owned by the CS domain — it represents CS-initiated tasks, not support tickets |

## Permissions

```
cs.kb-analytics.view-searches
cs.kb-analytics.view-failed-searches
cs.kb-analytics.view-article-engagement
cs.kb-analytics.create-gap-tasks
cs.kb-analytics.manage-gap-tasks
```

## Filament

- **Custom widget:** `KbAnalyticsWidget` on the CS dashboard — shows three KPIs: (1) Failed searches this week (count with week-on-week delta), (2) Self-service success rate (% of searches that ended without a ticket), (3) Top 3 failed queries; clicking any metric navigates to the full Content Gap page
- **Custom page:** `ContentGapPage` — the primary view for this module; three tabs:
  - **Failed Searches** — table of zero-result queries with columns: query, frequency, ticket correlation count, "Create Gap Task" action button
  - **Article Engagement** — table of articles with: view count, helpfulness score, self-service success rate, trend vs prior period; filterable by customer segment
  - **Gap Tasks** — kanban-style board of `cs_content_gap_tasks` grouped by status (open / in progress / completed)
- **No standard CRUD resource** — all views are read-only analytics or task management on the Content Gap page
- **Nav group:** Analytics (cs panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Zendesk Content Cues | Failed search detection and article gap recommendations |
| Intercom Articles analytics | Article performance and search effectiveness per customer segment |
| Guru analytics | Knowledge base engagement and content gap identification |

## Related

- [[health-scores]]
- [[support-tickets]]
- [[playbooks]]
- [[../support/INDEX]]

## Implementation Notes

### Cross-Domain Data Reading
`kb_article_searches` lives in the Support domain's migration range. The CS module reads it via a `SupportDomain::KbSearchRepositoryInterface` — the concrete implementation is registered by the Support domain's ServiceProvider. This pattern keeps the domain boundary clean and allows the Support domain to refactor its internal schema without breaking the CS module, as long as the interface contract is maintained.

### Anonymity and Privacy
`kb_article_searches.searcher_contact_id` is populated only when the knowledge base is accessed by an authenticated (identified) contact. Anonymous searches (e.g. from the public knowledge base) are recorded without a contact ID. When filtering by customer segment, only identified-contact searches can be segment-filtered — anonymous searches appear in the "All" view but not in segment-specific views.

GDPR note: search query text is considered activity data. Implement a data retention policy (default: 180 days rolling deletion via a scheduled job) configurable in the Privacy module.

### AI Clustering
The semantic clustering of failed search queries uses the Embeddings API (OpenAI `text-embedding-3-small`) to generate embeddings for each unique failed query, then applies k-means clustering (k=10 default, configurable) using a PHP implementation or a Python microservice call. Clusters are recomputed weekly via a scheduled job and cached in Redis. The cluster labels (e.g. "Data Export") are generated by GPT-4o given the 5 most representative queries in each cluster.
