---
type: module
domain: Support & Help Desk
domain-key: support
panel: support
module-key: support.kb
status: planned
priority: p2
depends-on: [core.billing, core.rbac]
soft-depends: [support.tickets]
fires-events: []
consumes-events: []
patterns: [search]
tables: [sup_kb_articles, sup_kb_categories]
permission-prefix: support.kb
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Knowledge Base

Self-service article library. Internal agents reference articles; customers browse a public help centre. Reduces ticket volume by deflecting common questions.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/support/tickets\|support.tickets]] | article suggestions in reply composer; standalone otherwise |

---

## Core Features

- Article record: title, slug, body (rich text), category, status (draft/published), author
- Categories and sub-categories for organisation
- Rich text editing via `awcodes/filament-tiptap-editor` — purified before storage ([[architecture/security]])
- Slugs via `spatie/laravel-sluggable`
- Public help centre: published articles browsable at a public URL (Vue + Inertia) — company resolved from help-centre slug *(assumed: `/help/{company-slug}`)*
- Article search via Meilisearch (public search limited to published)
- Article feedback: "Was this helpful?" thumbs up/down with counts (rate-limited per visitor)
- View count tracking per article
- Suggest relevant articles to agents while replying to a ticket (search on ticket subject)
- Article versioning: previous bodies kept (jsonb history *(assumed: last 20 revisions)*)

---

## Data Model

### sup_kb_articles

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| slug | string | sluggable, unique `(company_id, slug)` |
| body | text | purified rich text |
| revisions | jsonb | [{body, author_id, saved_at}] capped 20 |
| category_id | ulid FK | |
| status | string default `draft` | draft / published |
| author_id | ulid FK users | |
| view_count / helpful_count / not_helpful_count | int default 0 | |
| published_at | timestamp nullable | |
| deleted_at | timestamp nullable | |

### sup_kb_categories — id, company_id (indexed), name, slug, parent_category_id nullable, order, deleted_at

---

## DTOs

### CreateArticleData — title (required, max:255), body (required, purified), category_id (exists), status (in:draft,published)
### FeedbackData (public) — article_id, helpful (bool) — rate-limited, no auth

## Services & Actions

- `KbService::publish(string $articleId)` / `unpublish(...)`
- `KbService::suggestFor(string $text): Collection` — Meilisearch query, published only (agent composer)
- `RecordArticleViewAction` / `RecordFeedbackAction` — public, rate-limited

---

## Filament

**Nav group:** Knowledge Base

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `KbArticleResource` | #1 CRUD resource | Tiptap editor, publish action, feedback counts |
| `KbCategoryResource` | #1 CRUD resource | tree order |

Public help centre: Vue + Inertia (`/help/{company}`, `/help/{company}/{category}/{slug}`) — ui-strategy row #16.

---

## Permissions

`support.kb.view-any` · `support.kb.create` · `support.kb.update` · `support.kb.publish` · `support.kb.manage-categories`

---

## Search & Realtime

Meilisearch: title, body (stripped), category — `is_published` filterable attribute; public queries always filter published + company ([[architecture/search]] tenant-safe pattern). No realtime.

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Public help centre serves published-only, right company
- [ ] Draft invisible publicly + in public search
- [ ] Body purified (XSS fixture)
- [ ] Revisions appended on edit, capped
- [ ] Feedback rate-limited; counts increment
- [ ] Agent suggestions return relevant published articles

---

## Build Manifest

```
database/migrations/xxxx_create_sup_kb_categories_table.php
database/migrations/xxxx_create_sup_kb_articles_table.php
app/Models/Support/{KbArticle,KbCategory}.php
app/Data/Support/{CreateArticleData,FeedbackData}.php
app/Services/Support/KbService.php
app/Actions/Support/{RecordArticleViewAction,RecordFeedbackAction}.php
app/Http/Controllers/HelpCentreController.php + resources/js/Pages/Help/{Index,Category,Article}.vue
app/Filament/Support/Resources/{KbArticleResource,KbCategoryResource}.php
database/factories/Support/{KbArticleFactory,KbCategoryFactory}.php
tests/Feature/Support/{KbArticleTest,HelpCentreTest}.php
```

---

## Related

- [[domains/support/tickets]]
- [[architecture/search]]
- [[frontend/_index]]
