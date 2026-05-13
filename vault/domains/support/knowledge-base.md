---
type: module
domain: Support & Help Desk
panel: support
module-key: support.knowledge-base
status: planned
color: "#4ADE80"
---

# Knowledge Base

> Public-facing help centre with Tiptap article editor, category tree, article versioning, Meilisearch full-text search, and per-article CSAT feedback — the self-service layer of the support domain.

**Panel:** `/support`
**Module key:** `support.knowledge-base`

## What It Does

Knowledge Base provides a public, SEO-optimised help centre that lets customers find answers without opening a support ticket. Articles are organised in a hierarchical category tree and authored with a rich Tiptap editor supporting headings, images, code blocks, callouts, and embedded videos. Every article tracks a version history so changes can be reviewed or rolled back. Customers can rate articles with thumbs up/down and leave optional comments, giving the support team a deflection signal. Meilisearch powers instant full-text search across all published articles. Companies with multiple brands or languages can create separate help centre collections, each with its own domain slug and locale.

## Features

### Core
- Article editor powered by Tiptap v2: headings, bold, italic, tables, code blocks, images, embedded video iframes, callout blocks, ordered and unordered lists
- Category tree with unlimited depth — drag-and-drop reorder in Filament, position stored as integer
- Article states: draft / published / archived
- SEO metadata per article: meta title, meta description, canonical URL, OG image
- Article versioning: each save creates a version snapshot. Agents can diff versions and restore any previous version.
- Related articles: manually curate up to 5 related article links shown at the bottom of each article
- Multiple help centre collections per company (one per brand or locale) with custom slug prefix

### Advanced
- Meilisearch full-text indexing of all published articles using `Laravel\Scout` + `meilisearch` driver. Index updated on article save via queued job.
- Article feedback: thumbs up / thumbs down button at article footer. Optional free-text comment. Stored in `kb_article_feedback`. Helpfulness ratio shown in Filament article list.
- Article views counter: incremented on each public page view via a queued `IncrementArticleViews` job (avoids N+1 on read path).
- Article suggestions in ticket reply composer — when agent opens `TicketDetailPage`, AI searches knowledge base for articles relevant to the ticket subject and surfaces top 3 as insert links.
- CSV/JSON export of all articles per collection for migration or backup.

### AI-Powered
- AI article draft generator: provide a topic and bullet points, Claude generates a structured article draft in the Filament editor
- Gap detection: AI analyses ticket tags and unanswered questions over the past 30 days and suggests missing knowledge base article topics
- Auto-summarise: Claude generates a one-paragraph summary of long articles for use as the meta description and search snippet

## Data Model

```erDiagram
    kb_collections {
        ulid id PK
        ulid company_id FK
        string title
        string slug
        string locale
        string custom_domain
        boolean is_active
        timestamps created_at/updated_at
    }

    kb_categories {
        ulid id PK
        ulid company_id FK
        ulid collection_id FK
        ulid parent_id FK
        string title
        string slug
        integer position
        timestamps created_at/updated_at
    }

    kb_articles {
        ulid id PK
        ulid company_id FK
        ulid category_id FK
        ulid author_id FK
        string title
        string slug
        text body
        string status
        integer views_count
        integer helpful_count
        integer not_helpful_count
        string meta_title
        string meta_description
        timestamp published_at
        timestamps created_at/updated_at
        timestamp deleted_at
    }

    kb_article_versions {
        ulid id PK
        ulid article_id FK
        ulid saved_by FK
        text body
        string title
        timestamps created_at/updated_at
    }

    kb_article_feedback {
        ulid id PK
        ulid article_id FK
        boolean is_helpful
        text comment
        string visitor_fingerprint
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | draft / published / archived — only published articles appear on public help centre |
| `locale` | BCP-47 locale code on collection (e.g. `en`, `nl`, `fr`) — drives hreflang tags |
| `body` | Stored as Tiptap JSON (not HTML) — rendered to HTML at display time via Tiptap renderer |
| `custom_domain` | Optional custom domain per collection (e.g. `help.acme.com`) — requires DNS CNAME to FlowFlex |
| `parent_id` | Self-referencing FK on `kb_categories` — null = root category |
| `visitor_fingerprint` | SHA-256 hash of IP + user-agent — prevents duplicate feedback from same visitor |

## Permissions

```
support.knowledge-base.view
support.knowledge-base.create
support.knowledge-base.edit
support.knowledge-base.delete
support.knowledge-base.publish
```

## Filament

- **Resource:** `KbArticleResource` (manage articles — list, create, edit with Tiptap block editor), `KbCategoryResource` (manage category tree), `KbCollectionResource` (manage help centre collections)
- **Pages:** `ListKbArticles`, `CreateKbArticle`, `EditKbArticle` (Tiptap editor as custom form component), `ListKbCategories`, `ListKbCollections`
- **Custom pages:** `KbArticleVersionsPage` — shows version history for a given article with diff view and restore action
- **Widgets:** `KbDeflectionWidget` — shows ticket deflection rate (tickets opened vs help centre searches) on the Support panel dashboard
- **Nav group:** Knowledge (support panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Zendesk Guide | Help centre, article authoring, categories |
| Intercom Articles | Knowledge base, article search |
| Helpscout Docs | Public documentation site |
| Notion (public wikis) | Shared knowledge base for customers |
| Confluence | Internal/external documentation |

## Related

- [[support-tickets]]
- [[live-chat-widget]]
- [[support-analytics]]
- [[domains/ai/INDEX]]

## Implementation Notes

- **Public routing:** Help centre rendered at `/help/{collection_slug}/{category_slug}/{article_slug}` via Vue 3 + Inertia — NOT a Filament page. These are public routes defined in `routes/web.php` under the `HelpCentreController`. Custom domain support requires routing middleware that checks the `Host` header against `kb_collections.custom_domain`.
- **Meilisearch indexing:** `KbArticle` model implements `Laravel\Scout\Searchable`. The Scout index name is scoped per company: `kb_articles_{company_id}`. Only `status = published` articles are indexed (`shouldBeSearchable()` method). Triggered by `saved` and `deleted` events via queued `Scout::sync` jobs.
- **Tiptap storage:** Article body is stored as Tiptap JSON (not raw HTML) for portability. A `TiptapRenderer` service class converts JSON to HTML for public display and plain text for search indexing.
- **Version snapshots:** A `KbArticleObserver::saving()` hook creates a `KbArticleVersion` record before every update when the body or title has changed. Versions are pruned to the last 50 per article via a scheduled weekly job.
- **Multi-locale:** Separate collections per locale — no inline translation system. Each collection is a completely independent set of articles and categories. Future: machine translation AI action on articles.
