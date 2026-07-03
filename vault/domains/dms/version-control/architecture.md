---
domain: dms
module: version-control
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Version Control — Architecture

## Services & Actions

`VersionService` is a multi-method service; lock/unlock are single-purpose `lorisleiva/laravel-actions`.

| Class | Type | Responsibility |
|---|---|---|
| `VersionService::uploadVersion(UploadVersionData): VersionData` | service method | Transaction: compute next `version_number`, store media via `CompanyPathGenerator` (`dms/` prefix), flip `is_current`, update document size/mime + re-dispatch text extraction through `dms.library`'s `DocumentService`. Throws `DocumentLockedException` if the document is locked by another user. |
| `VersionService::restore(RestoreVersionData): VersionData` | service method | Copies the target historical media as a **new** version (fresh `version_number`, `is_current = true`); history is preserved, nothing deleted. |
| `LockDocumentAction` | action | Create a `dms_document_locks` row for the current user; rejects if already locked by another (unless caller holds `dms.versions.force-unlock`). |
| `UnlockDocumentAction` | action | Release a lock — own lock, or any lock with `dms.versions.force-unlock`. |
| `ExpireStaleLocksCommand` | scheduled command | Deletes locks older than the auto-expiry window (4h *(assumed)*). Scheduled in the console kernel. |

Cross-domain rule: `VersionService` never writes `dms_documents`; it calls `DocumentService` to update metadata and re-trigger `ExtractDocumentTextJob` ([[../../../security/data-ownership]]).

## Filament Artifacts

**Nav group:** Documents

All surfaces hang off the `dms.library` `DocumentViewerPage` — version control adds a relation manager and header actions rather than its own top-level page.

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| Version-history relation manager | on `DocumentViewerPage` (#2-style library custom page) | tweak: relation-manager-timeline (read-only versions list) | per-row download + restore actions |
| Upload-new-version action | custom-header-action on `DocumentViewerPage` | tweak: custom-header-actions (needs `dms.versions.upload`) | modal for file + change note; blocked when locked by another (`DocumentLockedException`) |
| Lock / unlock action + lock badge | custom-header-action + badge on `DocumentViewerPage` | tweak: custom-header-actions (force-unlock needs `dms.versions.force-unlock`) | badge shows the current lock holder |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('dms.versions.view-any') && BillingService::hasModule('dms.versions')`
per [[../../../architecture/filament-patterns]] #1. These artifacts render on `dms.library`'s `DocumentViewerPage`
(a custom page), so each MUST state the gate explicitly — Filament does not auto-gate custom pages or their
actions. Folder access (via `dms.library`'s `accessibleFoldersFor`) is a **second gate** on history and download
([[security#Locking as a Concurrency Control]]).

> [!warning] UNVERIFIED
> The access contract references `dms.versions.view-any`, but the source Permissions list only defines `upload`, `restore`, and `force-unlock`. See [[unknowns]].

## Concurrency

This module owns the DMS **document-locks** tier — the third concurrency tier in the platform standard.

| Write path | Tier | Mechanism |
|---|---|---|
| Upload new version (`uploadVersion`) | Pessimistic + Document locks | `DB::transaction()` + `lockForUpdate()` computes the next `version_number` and flips the single `is_current` row atomically ([[../../../architecture/patterns/states]]); additionally a checkout lock (`dms_document_locks`) makes a non-holder's concurrent version fail with `DocumentLockedException` |
| Restore version (`restore`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` — creates a fresh current version and flips `is_current` atomically; history untouched |
| Document checkout / checkin (`LockDocumentAction` / `UnlockDocumentAction`) | Document locks | Explicit checkout row in `dms_document_locks` (`document_id` unique); create rejects if held by another (unless `dms.versions.force-unlock`); `ExpireStaleLocksCommand` clears stale locks — the DMS-only document-locks tier |
| Version-history read / download | n/a | Read-only over `dms_document_versions` — no write surface |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Events

None fired or consumed. Version control defines no cross-domain events in v1; see [[../../../architecture/event-bus]] for the platform contract.

## Jobs & Scheduling

- `ExpireStaleLocksCommand` — scheduled (frequency *(assumed)* every 15 min) to clear locks past the 4h *(assumed)* window.
- Text extraction is not owned here: `uploadVersion` re-triggers `dms.library`'s `ExtractDocumentTextJob` via `DocumentService` so the current version's text stays searchable.

## Search & Realtime

No dedicated index. The searchable `extracted_text` lives on `dms_documents` (owned by `dms.library`) and reflects the current version only. No realtime; lock state is read on page load *(assumed)*.
