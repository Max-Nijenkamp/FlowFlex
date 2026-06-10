---
type: module
domain: Document Management
domain-key: dms
panel: dms
module-key: dms.versions
status: planned
priority: p2
depends-on: [dms.library, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: []
tables: [dms_document_versions, dms_document_locks]
permission-prefix: dms.versions
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Version Control

Version history on documents. Upload a new version, view history, compare metadata, and restore a previous version.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/dms/document-library\|dms.library]] | versions belong to documents |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Each document has multiple versions; current version is the latest by default
- Upload new version: replaces current, keeps old as history
- Version record: version number, file, uploaded by, upload date, change note
- View version history: list all versions with metadata
- Download any historical version (folder-access gated)
- Restore: make an old version current again (creates a NEW version, doesn't delete history)
- Version comparison: size/date diff (full content diff out of scope for v1)
- Lock document while being edited (prevent concurrent version conflicts); auto-expire locks after 4h *(assumed)*

---

## Data Model

### dms_document_versions

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), document_id FK | ulid | |
| version_number | int | unique `(document_id, version_number)`, sequential |
| media_id | ulid FK media | |
| uploaded_by | ulid FK users | |
| change_note | string nullable | |
| is_current | boolean | exactly one current per document (partial unique) |
| created_at | timestamp | |

### dms_document_locks — id, document_id FK unique, company_id, locked_by FK users, locked_at

---

## DTOs

### UploadVersionData — document_id (accessible, unlocked-or-own-lock), file (security rules), change_note?
### RestoreVersionData — version_id

## Services & Actions

- `VersionService::uploadVersion(UploadVersionData $data): VersionData` — transaction: next number, flips is_current, updates document size/mime/extracted text job; throws `DocumentLockedException`
- `VersionService::restore(RestoreVersionData $data): VersionData` — copies old media as new version
- `LockDocumentAction` / `UnlockDocumentAction` (own lock or `dms.versions.force-unlock`)
- `ExpireStaleLocksCommand` — scheduled

---

## Filament

**Nav group:** Documents

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| Version history relation manager | on DocumentViewerPage | download/restore actions |
| Upload-new-version + lock actions | on document view | lock badge |

---

## Permissions

`dms.versions.upload` · `dms.versions.restore` · `dms.versions.force-unlock`

---

## Test Checklist

- [ ] Tenant isolation + module gating + folder-access gate on history/download
- [ ] Version numbers sequential; exactly one is_current
- [ ] Upload on locked document by other user rejected; lock owner OK
- [ ] Restore creates new version, history intact
- [ ] Stale locks expire
- [ ] Document metadata updated on new version

---

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

---

## Related

- [[domains/dms/document-library]]
