---
domain: dms
module: wiki
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

## Events

None fired or consumed. Wiki defines no cross-domain events in v1; see [[../../../architecture/event-bus]] for the platform contract.

## Jobs & Scheduling

None in v1. Meilisearch reindex happens synchronously on save via Scout *(assumed)*; a queued reindex is a possible follow-up ([[unknowns]]).

## Search & Realtime

Meilisearch index over `title` + stripped `body`. **Results post-filtered by page access** — the accessible-page set from `accessiblePagesFor` intersects Meilisearch hits, so a restricted page never surfaces even on a direct term hit. No realtime. Rate-limited per company/user on the search endpoint ([[security]]).
