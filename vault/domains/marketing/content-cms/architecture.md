---
domain: marketing
module: content-cms
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Content CMS — Architecture

Parent: [[_module]]

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `PublishScheduledPostsCommand` | scheduled | Flips `scheduled → published` at `published_at` with an idempotent `WHERE status=scheduled AND published_at <= now` guard. |
| `PostService::related` | `related(postId): Collection` | Same-category recent posts, excludes self. |

Reading time computed from body word count; slug via `spatie/laravel-sluggable`; search indexing via `laravel/scout` (published scope only).

## Status

`draft → scheduled → published` — simple enum + scheduler (no spatie states *(assumed)*). `published_at` doubles as schedule time and display date.

## Public blog + search

`GET /blog/{company-slug}` (Index) + `/blog/{company-slug}/{slug}` (Show) — Vue + Inertia (ui-strategy rows #12/#16). Meilisearch query returns published-only, company-scoped. Throttled.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `PostResource` | Content | #1 CRUD resource | Tiptap body, schedule/publish actions, SEO section |
| `PostCategoryResource` | Content | #1 CRUD resource | |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('marketing.cms.view-any')
        && BillingService::hasModule('marketing.cms');
}
```

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/search]]
