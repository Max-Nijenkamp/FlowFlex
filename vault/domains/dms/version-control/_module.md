---
domain: dms
module: version-control
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Version Control

Version history on documents. Upload a new version, view the full history, compare metadata, restore a previous version, and lock a document while editing to prevent concurrent version conflicts. Layers on top of `dms.library` — every version belongs to a document.

## Module-key

`dms.versions`

**Priority:** p2  
**Panel:** dms  
**Permission prefix:** `dms.versions`  
**Tables:** `dms_document_versions`, `dms_document_locks`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../document-library/_module\|dms.library]] | Versions belong to documents; metadata read/updated via `DocumentService` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()`, folder-access reuse |

## Core Features

- **Multiple versions per document** — the current version is the latest by default; history is never deleted.
- **Upload new version** — replaces the current file, keeps the old as history. Records version number, file, uploader, upload date, and change note.
- **Version history** — list all versions with metadata; download any historical version (folder-access gated).
- **Restore** — make an old version current again by **creating a NEW version** (does not delete history).
- **Version comparison** — size / date diff only; full content diff is out of scope for v1 *(assumed)*.
- **Document locking** — lock a document while editing to prevent concurrent version conflicts; locks auto-expire after 4h *(assumed)*. `dms.versions.force-unlock` can override another user's lock.
- **Upload contract** — version files reuse the `dms.library` upload whitelist, max size, and `companies/{id}/dms/` path via `CompanyPathGenerator`.

## See features/

- [[features/upload-version|Upload Version]] — new-version action on the viewer; flips `is_current`, updates document metadata.
- [[features/version-history|Version History]] — the history relation manager / list on the viewer.
- [[features/restore-version|Restore Version]] — restore an old version by creating a new one.
- [[features/document-locking|Document Locking]] — lock / unlock / force-unlock + stale-lock expiry.

## Build Manifest

```
database/migrations/xxxx_create_dms_document_versions_table.php
database/migrations/xxxx_create_dms_document_locks_table.php
app/Models/DMS/{DocumentVersion,DocumentLock}.php
app/Data/DMS/{UploadVersionData,RestoreVersionData,VersionData}.php
app/Services/DMS/VersionService.php
app/Exceptions/DMS/DocumentLockedException.php
app/Actions/DMS/{LockDocumentAction,UnlockDocumentAction}.php
app/Console/Commands/DMS/ExpireStaleLocksCommand.php
database/factories/DMS/DocumentVersionFactory.php
tests/Feature/DMS/{VersionControlTest,DocumentLockTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot view, download, or restore company B's document versions.
- [ ] Module gating: artifacts hidden when `dms.versions` inactive.
- [ ] Folder-access gate applies to version history and historical-version download.
- [ ] Version numbers sequential; exactly one `is_current` per document.
- [ ] Upload on a document locked by another user rejected; lock owner OK.
- [ ] Restore creates a new version, history intact.
- [ ] Stale locks expire (`ExpireStaleLocksCommand`).
- [ ] Document metadata (size / mime / extracted text) updated on new version.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Commands | `DocumentService` (folder access, metadata update) | dms.library | Reads accessible docs + folder-access scope; updates document size/mime and re-triggers text extraction via the owning service — never writes `dms_documents` directly |
| Reads/Commands | `core.files` Media Library + `CompanyPathGenerator` | core.files | Version bytes stored under `companies/{id}/dms/` via the file-storage service |
| Fires | *(none)* | — | No cross-domain events; version control is an internal DMS surface *(assumed)* |

**Data ownership:** `dms.versions` writes only `dms_document_versions` and `dms_document_locks`. Document metadata is updated through `dms.library`'s `DocumentService` (its owning API) and version bytes are stored through the `core.files` service, never by writing another domain's tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../document-library/_module|Document Library]] · [[../approval-workflows/_module|Approval Workflows]] · [[../retention-policies/_module|Retention Policies]]
- [[../../core/billing-engine/_module|core.billing]] · [[../../core/rbac/_module|core.rbac]] · [[../../core/file-storage/_module|core.files]]
