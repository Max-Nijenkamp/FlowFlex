---
domain: support
module: knowledge-base
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `KbArticleResource` | #1 CRUD resource | tweaks: state-badge-column (draft/published), custom-header-actions (publish / unpublish) | Tiptap editor (purified); view / helpful counts columns |
| `KbCategoryResource` | #1 CRUD resource | (base resource — tree order, sub-categories) | organises articles |

Public help centre: Vue + Inertia (`/help/{company}`, `/help/{company}/{category}/{slug}`) — ui-strategy row #16, `HelpCentreController` + `resources/js/Pages/Help/*`.

**Access contract (mandatory):** every panel artifact gates on
`canAccess() = Auth::user()->can('support.kb.view-any') && BillingService::hasModule('support.kb')`
per [[../../../architecture/filament-patterns]] #1. The public help centre is Vue+Inertia per [[../../../architecture/ui-strategy]] under a **guest guard** (not a panel session), every query filtered to `is_published = true` + `company_id`, with rate-limited feedback/view endpoints — not a Filament artifact.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Article CRUD + revision append (form) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification; prior body appended to `revisions` jsonb in the same save ([[../../../architecture/patterns/optimistic-locking]]) |
| Category CRUD | Optimistic | `updated_at` stale-check ([[../../../architecture/patterns/optimistic-locking]]) |
| Publish / unpublish (status flip) | Optimistic | `updated_at` stale-check → flips `status` + `published_at`; not a money/capacity mutation, no lock needed |
| View / feedback counters (public) | n/a | atomic `increment()` on the article counters — no read-modify-write race; rate-limited per visitor (see [[./security]]) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

Meilisearch (Scout): `title`, `body` (stripped), `category` — `is_published` filterable attribute; public queries always filter published + company ([[../../../architecture/search]] tenant-safe pattern). No realtime.

See [[./security]] for the public rate-limit + tenant-scoping contract.
