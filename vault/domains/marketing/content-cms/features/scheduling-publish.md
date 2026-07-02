---
domain: marketing
module: content-cms
feature: scheduling-publish
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Scheduling & Publish

Publish now or schedule for later; the scheduler flips posts live exactly once.

## Behaviour

- Publish action: `draft → published` immediately, or set future `published_at` → `scheduled`.
- `PublishScheduledPostsCommand` flips `scheduled → published` at time with an idempotent WHERE guard (fires once).
- Publishing (re)indexes the post into Meilisearch (published scope).

## UI

- **Kind**: simple-resource
- **Page**: publish/schedule actions on `PostResource` rows + edit form; status badge tracks state.
- **Layout**: row action "Publish" / "Schedule"; date-time picker for `published_at`.
- **Key interactions**: click Publish (immediate) or set date → Schedule; unpublish returns to draft.
- **States**: empty (n/a) · loading (index write) · error (nothing to publish) · selected (status badge).
- **Gating**: `marketing.cms.publish`.

## Data

- Owns / writes: `mkt_posts.status/published_at` + Meilisearch projection (own module).
- Reads: post rows (own).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Feeds: published posts consumed by [[public-blog]].
- Shared entity: none written.

## Unknowns

- Sitemap/RSS regeneration on publish — unspecced. See [[../unknowns]].

## Related

- [[../_module|Content CMS]] · [[public-blog]] · [[../architecture]]
