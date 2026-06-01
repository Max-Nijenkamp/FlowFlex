---
type: module
domain: Marketing
panel: marketing
module-key: marketing.cms
status: planned
color: "#4ADE80"
---

# Content CMS

Blog and content management for the public marketing site. Author posts, categorise, schedule, and publish.

## Core Features

- Blog post: title, slug, body (rich text), excerpt, featured image, author, category, tags
- Status: draft → scheduled → published
- Scheduled publishing
- Categories and tags
- SEO fields: meta title, description, OG image
- Author profiles
- Public blog rendering (Vue + Inertia)
- Full-text search (Meilisearch)
- Related posts
- Reading time estimate

## Data Model

| Table | Key Columns |
|---|---|
| `mkt_posts` | company_id, title, slug, body, excerpt, featured_image, author_id, category_id, status, published_at, meta_title, meta_description |
| `mkt_post_categories` | company_id, name, slug |

## Filament

**Nav group:** Content

- `PostResource` — create, edit (Tiptap), schedule, publish
- `PostCategoryResource` — manage categories

## Public Frontend

- `/blog`, `/blog/{slug}` (Vue + Inertia)

## Related

- [[frontend/_index]]
- `awcodes/filament-tiptap-editor`, `spatie/laravel-sluggable`
