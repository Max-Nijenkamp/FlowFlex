---
domain: dms
module: document-library
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Library — Architecture

## Services & Actions

Interface→Service binding (`DmsServiceProvider`): `DocumentServiceInterface` → `DocumentService`.

| Class | Type | Responsibility |
|---|---|---|
| `DocumentService::upload(UploadDocumentData): DocumentData` | service method | Store media via `CompanyPathGenerator` (`dms/` prefix); dispatch `ExtractDocumentTextJob`. |
| `DocumentService::move` / `::copy` | service method | Re-check target folder access before writing. |
| `DocumentService::accessibleFoldersFor(User): Builder` | service method | **The single access-scope API.** Every list / search / viewer path composes on this; inheritance down the tree is resolved here. |
| `ExtractDocumentTextJob` | queued job (`default`) | PDF text extraction (`pdftotext` *(assumed)*), updates `extracted_text`, reindexes in Meilisearch. Other types name-only v1. |

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `DocumentLibraryPage` | Documents | #11 tree custom page | Folder-tree sidebar + document grid; drag-drop upload. |
| `DocumentViewerPage` | Documents | #2-style custom page | PDF/image preview via temp signed URL; metadata; version relation (soft-dep). |
| `FolderResource` | Documents | #1 CRUD resource | Folder create/edit + access config. |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.library.view-any')
        && BillingService::hasModule('dms.library');
}
```

Custom pages state this explicitly. Folder-access list is a **second gate** on top of the permission.

## Events

None fired or consumed. DMS defines no cross-domain events in v1; see [[../../../architecture/event-bus]] for the platform contract and [[unknowns]] for a possible future `DocumentUploaded`.

## Jobs & Scheduling

`ExtractDocumentTextJob` — dispatched on upload / new version; `default` queue; PDF-only extraction v1.

## Search & Realtime

Meilisearch index over `name`, `description`, `extracted_text`. **Results post-filtered by folder access** (`folder_id` filterable attribute + accessible-set filter from `accessibleFoldersFor`). No realtime. Rate-limited per company/user on the search endpoint ([[security]]).
