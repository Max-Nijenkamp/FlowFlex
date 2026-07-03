---
domain: dms
module: document-library
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Documents

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DocumentLibraryPage` | #11 tree custom page | [[../../../architecture/patterns/page-blueprints#Org Chart / Tree]] — folder-tree sidebar + document grid | drag-drop upload; breadcrumb + search toolbar |
| `DocumentViewerPage` | #2-style custom page | no dedicated viewer blueprint — record-detail preview rendered as a custom page *(assumed)*; passes [[../../../architecture/patterns/custom-page-checklist]] | PDF/image preview via temp signed URL; metadata rail; version relation (soft-dep) |
| `FolderResource` | #1 CRUD resource | tweaks: inline-relation-repeater (folder-access rows) | folder create/edit + access config |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('dms.library.view-any') && BillingService::hasModule('dms.library')`
per [[../../../architecture/filament-patterns]] #1. `DocumentLibraryPage` and `DocumentViewerPage` are custom pages
and MUST state it explicitly — Filament does not auto-gate custom pages. Beyond the permission, the folder-access
list (`accessibleFoldersFor`) is a **second gate**: a restricted folder is invisible in the tree, grid, search, and
on the direct viewer URL for non-permitted users ([[security#Folder Access Inheritance]]).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Folder + document metadata CRUD, move/copy, favourite toggle | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Folder access rules (`dms_folder_access`) | Optimistic | `updated_at` stale-check on the parent folder; save re-resolves `accessibleFoldersFor` ([[../../../architecture/patterns/optimistic-locking]]) |
| Upload + text extraction | n/a | Append-only: upload inserts a new `dms_documents` row; `ExtractDocumentTextJob` updates only `extracted_text` on its own row (single writer, no concurrent-edit surface) |
| Checkout lock for versioned edits | Document locks | Owned by [[../version-control/_module\|dms.versions]] (`dms_document_locks`) — the library never holds the lock row; see [[../version-control/architecture#Concurrency]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Events

None fired or consumed. DMS defines no cross-domain events in v1; see [[../../../architecture/event-bus]] for the platform contract and [[unknowns]] for a possible future `DocumentUploaded`.

## Jobs & Scheduling

`ExtractDocumentTextJob` — dispatched on upload / new version; `default` queue; PDF-only extraction v1.

## Search & Realtime

Meilisearch index over `name`, `description`, `extracted_text`. **Results post-filtered by folder access** (`folder_id` filterable attribute + accessible-set filter from `accessibleFoldersFor`). No realtime. Rate-limited per company/user on the search endpoint ([[security]]).
