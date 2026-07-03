---
domain: dms
module: wiki
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Wiki — Architecture

## Services & Actions

`WikiService` is a multi-method service (no interface needed unless a second implementation appears *(assumed)*).

| Class | Type | Responsibility |
|---|---|---|
| `WikiService::save(CreateWikiPageData\|UpdateWikiPageData): WikiPageData` | service method | Purify body, resolve slug, **snapshot the previous body to `dms_wiki_page_versions` on update**, cycle-check `parent_page_id`, reindex in Meilisearch. |
| `WikiService::restoreVersion(string $versionId): WikiPageData` | service method | Restore a snapshot's body onto the page; itself creates a new version snapshot *(assumed)*. |
| `WikiService::accessiblePagesFor(User): Builder` | service method | **The single access-scope API.** Restricted pages are invisible in the tree, search, AND direct URL. Every list / tree / search / viewer path composes on it. |
| `WikiService::tree(): array` | service method | Build the nested nav tree (composed on `accessiblePagesFor`). |

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `WikiPageResource` | Wiki | #1 CRUD resource | Tree-ordered list, Tiptap editor form, version-history relation manager, access-config form section. |
| `WikiViewerPage` | Wiki | #2-style custom page | Rendered page + auto-TOC sidebar + nested nav; internal page-id links resolve here. |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.wiki.view-any')
        && BillingService::hasModule('dms.wiki');
}
```

Custom pages state this explicitly. The per-page access list is a **second gate** on top of the permission — resolved by `accessiblePagesFor`.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Page save (`WikiService::save`) | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] — stale editor gets a conflict instead of silently overwriting; the pre-save snapshot to `dms_wiki_page_versions` preserves the losing body for manual merge |
| Restore version (`WikiService::restoreVersion`) | Optimistic | Same version check; restore itself snapshots first, so a raced restore never destroys history |
| Page access-list edit | Optimistic | Settings-form save per [[../../../architecture/patterns/optimistic-locking]] |
| Version snapshots | n-a | Append-only rows, never updated |
| Tree / search / viewer reads | n-a | Read-only, composed on `accessiblePagesFor` |

Wiki pages do NOT use the DMS document-lock (checkout/checkin) tier — that tier is scoped to `dms.version-control` binary documents; wiki concurrency is handled by versioned saves. Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Events

None fired or consumed. Wiki defines no cross-domain events in v1; see [[../../../architecture/event-bus]] for the platform contract.

## Jobs & Scheduling

None in v1. Meilisearch reindex happens synchronously on save via Scout *(assumed)*; a queued reindex is a possible follow-up ([[unknowns]]).

## Search & Realtime

Meilisearch index over `title` + stripped `body`. **Results post-filtered by page access** — the accessible-page set from `accessiblePagesFor` intersects Meilisearch hits, so a restricted page never surfaces even on a direct term hit. No realtime. Rate-limited per company/user on the search endpoint ([[security]]).
