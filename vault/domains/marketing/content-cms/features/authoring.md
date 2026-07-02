---
domain: marketing
module: content-cms
feature: authoring
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Authoring

Write and organise blog posts: body, excerpt, image, category, tags, SEO.

## Behaviour

- Compose in Tiptap (purified); set excerpt, featured image, category, tags.
- Fill SEO meta (title, description, OG image). Reading time computed on save.
- Manage categories separately.

## UI

- **Kind**: simple-resource
- **Page**: `PostResource` (`/marketing/content`) — Content nav group; `PostCategoryResource` alongside.
- **Layout**: table (title, status, category, published_at) + form (Tiptap body, SEO section, media picker, tag input).
- **Key interactions**: write body; pick category/tags; set featured + OG image; save draft.
- **States**: empty (no posts → CTA) · loading (media upload) · error (title/body required) · selected (editing).
- **Gating**: `marketing.cms.create` / `.update`.

## Data

- Owns / writes: `mkt_posts`, `mkt_post_categories` (own module).
- Reads: media (featured image), author (current user) — read-only.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: media from [[../../../core/file-storage/_module|core.files]]; author from core users.
- Feeds: posts consumed by [[scheduling-publish]] + [[public-blog]].
- Shared entity: tags (`spatie/laravel-tags`, polymorphic).

## Unknowns

- Author bio source; multi-author bylines. See [[../unknowns]].

## Related

- [[../_module|Content CMS]] · [[scheduling-publish]] · [[public-blog]]
