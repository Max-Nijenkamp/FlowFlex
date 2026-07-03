---
domain: marketing
module: content-cms
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Content

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `PostResource` | #1 CRUD resource | tweaks: state-badge-column, custom-header-actions (publish / schedule / unpublish) | Tiptap body (purified), SEO section, media picker; status is a simple enum (no spatie states *(assumed)*) |
| `PostCategoryResource` | #1 CRUD resource | — | categories management |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('marketing.cms.view-any') && BillingService::hasModule('marketing.cms')`
per [[../../../architecture/filament-patterns]] #1. This module has no custom Filament pages. The public blog Index/Show (`/blog/{company-slug}`) is Vue + Inertia (ui-strategy rows #12/#16) served to unauthenticated guests, published-only + company-scoped, with a throttled search endpoint ([[./security]]) — not a Filament artifact.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Post / Category CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Publish / schedule / unpublish (status flip via action) | Optimistic | Simple-enum flip on a single-author record; `updated_at` stale-check guards against a concurrent edit ([[../../../architecture/patterns/optimistic-locking]]) — not a spatie state transition, so no pessimistic lock |
| Scheduled publish (`PublishScheduledPostsCommand`) | n/a | Idempotent `WHERE status=scheduled AND published_at <= now` guard, single background writer, no concurrent editors — flips exactly once |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Related

- [[_module]] · [[data-model]] · [[../../../architecture/search]]
