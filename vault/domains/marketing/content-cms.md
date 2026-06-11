---
type: module
domain: Marketing
domain-key: marketing
panel: marketing
module-key: marketing.cms
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.files]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [search]
tables: [mkt_posts, mkt_post_categories]
permission-prefix: marketing.cms
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Content CMS

Blog and content management for the company's public marketing presence. Author posts, categorise, schedule, and publish.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, featured images |

---

## Core Features

- Blog post: title, slug, body (rich text, purified), excerpt, featured image, author, category, tags
- Status: `draft → scheduled → published` (simple enum + scheduler *(assumed: no spatie states — linear)*)
- Scheduled publishing (command flips at `published_at`)
- Categories and tags
- SEO fields: meta title, description, OG image
- Author profiles (user name + bio *(assumed: from user record)*)
- Public blog rendering (Vue + Inertia) at `/blog/{company-slug}` *(assumed: company-scoped public blog)*
- Full-text search (Meilisearch, published only)
- Related posts (same category, recent)
- Reading time estimate (computed)

---

## Data Model

### mkt_posts

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| slug | string | sluggable, unique per company |
| body | text | purified |
| excerpt | text nullable | |
| featured_image | string nullable | media path |
| author_id | ulid FK users | |
| category_id | ulid nullable FK | |
| status | string default `draft` | draft / scheduled / published |
| published_at | timestamp nullable | schedule + display date |
| meta_title / meta_description / og_image | string nullable | |
| deleted_at | timestamp nullable | |

### mkt_post_categories — id, company_id (indexed), name, slug (unique per company)

---

## DTOs

### CreatePostData — title (required, max:255), body (required, purified), excerpt?, category_id?, tags[], published_at? (future → scheduled), SEO fields

## Services & Actions

- `PublishScheduledPostsCommand` — flips scheduled → published at time (idempotent WHERE guard)
- `PostService::related(string $postId): Collection`

---

## Filament

**Nav group:** Content

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `PostResource` | #1 CRUD resource | Tiptap, schedule/publish actions, SEO section |
| `PostCategoryResource` | #1 CRUD resource | |

Public blog: Vue + Inertia (`/blog`, `/blog/{slug}`) — ui-strategy row #12/16.


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('marketing.cms.view-any') && BillingService::hasModule('marketing.cms')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a throttle/rate limiter on the public blog and search routes to protect Meilisearch-backed queries from abuse.

---

## Permissions

`marketing.cms.view-any` · `marketing.cms.create` · `marketing.cms.update` · `marketing.cms.publish`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Draft/scheduled invisible publicly; publish command flips on time, once
- [ ] Body purified; SEO meta rendered
- [ ] Public search returns published-only for the right company
- [ ] Related posts same category, excludes self
- [ ] Reading time computed

---

## Build Manifest

```
database/migrations/xxxx_create_mkt_post_categories_table.php
database/migrations/xxxx_create_mkt_posts_table.php
app/Models/Marketing/{Post,PostCategory}.php
app/Data/Marketing/CreatePostData.php
app/Services/Marketing/PostService.php
app/Console/Commands/Marketing/PublishScheduledPostsCommand.php
app/Http/Controllers/BlogController.php + resources/js/Pages/Marketing/Blog/{Index,Show}.vue
app/Filament/Marketing/Resources/{PostResource,PostCategoryResource}.php
database/factories/Marketing/{PostFactory,PostCategoryFactory}.php
tests/Feature/Marketing/BlogTest.php
```

---

## Related

- [[frontend/_index]]
- [[architecture/search]]
- [[architecture/packages]] (`awcodes/filament-tiptap-editor`, `spatie/laravel-sluggable`)
