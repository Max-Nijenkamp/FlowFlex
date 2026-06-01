---
type: module
domain: Document Management
panel: dms
module-key: dms.versions
status: planned
color: "#4ADE80"
---

# Version Control

Version history on documents. Upload a new version, view history, compare metadata, and restore a previous version.

## Core Features

- Each document has multiple versions; current version is the latest by default
- Upload new version: replaces current, keeps old as history
- Version record: version number, file, uploaded by, upload date, change note
- View version history: list all versions with metadata
- Download any historical version
- Restore: make an old version current again (creates a new version, doesn't delete)
- Version comparison: show size/date diff (full content diff out of scope for v1)
- Lock document while being edited (prevent concurrent version conflicts)

## Data Model

| Table | Key Columns |
|---|---|
| `dms_document_versions` | company_id, document_id, version_number, media_id, uploaded_by, change_note, is_current |
| `dms_document_locks` | document_id, company_id, locked_by, locked_at |

## Filament

**Nav group:** Documents

- Version history shown as a relation manager on the document view page
- Upload new version + restore actions on the document view page

## Related

- [[domains/dms/document-library]]
