---
domain: marketing
module: content-cms
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Content CMS

Blog and content management for the company's public marketing presence. Author posts, categorise, schedule, publish, and serve a searchable public blog.

- **module-key:** `marketing.cms` · **panel:** marketing · **priority:** p3
- **fires-events:** none · **consumes-events:** none
- **tables:** `mkt_posts`, `mkt_post_categories`

## What it does

- Blog post: title, slug, body (Tiptap, purified), excerpt, featured image, author, category, tags, SEO fields.
- Status: `draft → scheduled → published` (simple enum + scheduler *(assumed no spatie states — linear)*).
- Scheduled publishing (`PublishScheduledPostsCommand` flips at `published_at`, once).
- Categories + tags; author profiles (from user record *(assumed)*).
- Public blog (Vue + Inertia) at `/blog/{company-slug}` *(assumed company-scoped)*.
- Full-text search (Meilisearch, published only); related posts (same category); reading-time estimate.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Hard | [[../../core/file-storage/_module\|core.files]] | featured images |

No soft deps. Search backed by [[../../../architecture/search]] (Meilisearch via laravel/scout).

## Sibling notes

- [[architecture]] — publish command, related-posts service, public blog, search
- [[data-model]] — two tables + ERD
- [[api]] — `CreatePostData` DTO
- [[security]] — public visibility, purification, search throttle
- [[decisions]] · [[unknowns]]
- [[features/authoring]] · [[features/scheduling-publish]] · [[features/public-blog]]

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | author profile | core users | display name + bio (read-only) |
| Reads | media URL | [[../../core/file-storage/_module\|core.files]] | featured image (read-only) |
| Uses | Meilisearch index | [[../../../architecture/search]] | published posts only |

No cross-domain **domain events** fired or consumed ([[../../../architecture/event-bus]]).

**Data ownership:** writes **only** `mkt_posts`, `mkt_post_categories`. Author (user) + media are **read** from their owning modules; the Meilisearch index is a projection it owns. Never writes user/files tables ([[../../../security/data-ownership]]).

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

## Related

- [[../../../frontend/_index]] · [[../../../architecture/search]] · [[../../../architecture/packages]] (`awcodes/filament-tiptap-editor`, `spatie/laravel-sluggable`)
