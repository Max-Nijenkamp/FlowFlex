---
domain: marketing
module: content-cms
feature: public-blog
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Public Blog

Serve a searchable, company-scoped public blog of published posts.

## Behaviour

- Index `/blog/{company-slug}` lists published posts (paginated); Show renders a single post.
- Full-text search (Meilisearch) over published-only, company-scoped posts.
- Related posts (same category, recent, excludes self); reading time shown; SEO meta + OG rendered.

## UI

- **Kind**: public-vue
- **Page**: `/blog/{company-slug}` (Index) + `/blog/{company-slug}/{slug}` (Show) — Vue + Inertia (ui-strategy rows #12/#16).
- **Layout**: Index = post cards + search box + category filter; Show = article + related-posts rail.
- **Key interactions**: search; filter by category; open a post; navigate related.
- **States**: empty (no published posts → friendly empty) · loading (search/SSR) · error (unknown slug → 404) · selected (post open).
- **Gating**: public, unauthenticated; published-only; throttled search ([[../security]]).

## Data

- Owns / writes: none at read time (reads own `mkt_posts` + search index).
- Reads: published posts, author display, media (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Uses: Meilisearch via [[../../../architecture/search]].
- Reads: author from core users; media from [[../../../core/file-storage/_module|core.files]].
- Shared entity: tags.

## Test Checklist

### Unit
- [ ] Related-posts query selects same-category recent posts and excludes the current post

### Feature (Pest)
- [ ] Index lists only published, company-scoped posts (paginated); draft/scheduled excluded
- [ ] Search returns published-only results for the correct company; unknown slug → 404
- [ ] Tenant isolation: `/blog/{company-slug}` never surfaces another company's posts or search hits
- [ ] Public search route is throttled (429 past the limit)

## Unknowns

- RSS/sitemap for SEO. See [[../unknowns]].

## Related

- [[../_module|Content CMS]] · [[scheduling-publish]] · [[../architecture]]
