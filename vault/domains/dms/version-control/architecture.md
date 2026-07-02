---
domain: dms
module: version-control
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

All surfaces hang off the `dms.library` `DocumentViewerPage` — version control adds a relation manager and header actions rather than its own top-level page.

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| Version history relation manager | Documents | on `DocumentViewerPage` (custom page) | Lists all versions; per-row download + restore actions. |
| Upload-new-version action | Documents | action on `DocumentViewerPage` | Modal for file + change note; blocked when locked by another. |
| Lock / unlock action + lock badge | Documents | action + badge on `DocumentViewerPage` | Badge shows current lock holder; force-unlock gated. |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.versions.view-any')
        && BillingService::hasModule('dms.versions');
}
```

Custom pages / relation managers state this explicitly. Folder access (via `dms.library`'s `accessibleFoldersFor`) is a **second gate** on history and download.

> [!warning] UNVERIFIED
> The access contract references `dms.versions.view-any`, but the source Permissions list only defines `upload`, `restore`, and `force-unlock`. See [[unknowns]].

## Events

None fired or consumed. Version control defines no cross-domain events in v1; see [[../../../architecture/event-bus]] for the platform contract.

## Jobs & Scheduling

- `ExpireStaleLocksCommand` — scheduled (frequency *(assumed)* every 15 min) to clear locks past the 4h *(assumed)* window.
- Text extraction is not owned here: `uploadVersion` re-triggers `dms.library`'s `ExtractDocumentTextJob` via `DocumentService` so the current version's text stays searchable.

## Search & Realtime

No dedicated index. The searchable `extracted_text` lives on `dms_documents` (owned by `dms.library`) and reflects the current version only. No realtime; lock state is read on page load *(assumed)*.
