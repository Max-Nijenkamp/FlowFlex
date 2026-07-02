---
domain: support
module: knowledge-base
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Knowledge Base — Architecture

## Services & Actions

- `KbService::publish(string $articleId)` / `unpublish(...)` — flips status + `published_at`
- `KbService::suggestFor(string $text): Collection` — Meilisearch query, published only (agent composer, soft-dep entry point)
- `RecordArticleViewAction::run(...)` — public, rate-limited, increments `view_count`
- `RecordFeedbackAction::run(FeedbackData)` — public, rate-limited, increments helpful/not-helpful
- Revisions: on edit, prior body appended to `revisions` jsonb, capped at 20 *(assumed)*

Slugs via `spatie/laravel-sluggable`; rich text purified via `ezyang/htmlpurifier` before storage.

---

## Filament Artifacts

**Nav group:** Knowledge Base

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `KbArticleResource` | #1 CRUD resource | Tiptap editor, publish action, feedback counts |
| `KbCategoryResource` | #1 CRUD resource | tree order, sub-categories |

Public help centre: Vue + Inertia (`/help/{company}`, `/help/{company}/{category}/{slug}`) — ui-strategy row #16, `HelpCentreController` + `resources/js/Pages/Help/*`.

**Access contract:** panel artifacts gate on `canAccess() = Auth::user()->can('support.kb.view-any') && BillingService::hasModule('support.kb')` per [[../../../architecture/filament-patterns]] #1. Public help centre runs under a guest guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Search & Realtime

Meilisearch (Scout): `title`, `body` (stripped), `category` — `is_published` filterable attribute; public queries always filter published + company ([[../../../architecture/search]] tenant-safe pattern). No realtime.

See [[./security]] for the public rate-limit + tenant-scoping contract.
