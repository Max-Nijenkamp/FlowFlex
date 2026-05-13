---
type: module
domain: Document Management
panel: dms
phase: 4
status: complete
cssclasses: domain-dms
migration_range: 997000–997499
last_updated: 2026-05-12
---

# Version Control

Every document change tracked. Full history, diff view, restore to any version. Prevents accidental overwrites and supports collaborative editing with conflict resolution.

---

## How Versioning Works

Every save to a document creates a new version:
- **Minor version** (v1.1, v1.2): draft edits, auto-saved or manual save
- **Major version** (v2.0): explicit milestone (e.g., "Sent for external review")
- **Locked version**: after signing or final approval — immutable, no further edits

Versions stored with: editor name, timestamp, change summary (optional), diff from prior version.

---

## Diff View

Side-by-side comparison of any two versions:
- Green: added text
- Red: deleted text
- Highlight: changed text
- Shows metadata changes (title, category, owner)

---

## Restore

Restore document to any prior version:
- Creates a new version (restoring doesn't overwrite history)
- Requires comment explaining why restoring

---

## Collaborative Editing

Multiple users can edit simultaneously (CRDT-based conflict resolution):
- Presence indicators (see who is editing)
- Changes merged in real-time
- Conflict resolution: if two users edit same sentence, both changes preserved with author annotation, then one is chosen

---

## Retention Policies

Configurable document retention:
- Documents auto-purged after X years (e.g., 7 years for financial docs per regulation)
- Legal hold: flag documents subject to litigation — prevents deletion
- GDPR right to erasure: pseudonymise or delete personal data while preserving document integrity

---

## Data Model

### `dms_document_versions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| document_id | ulid | FK |
| version_number | varchar(20) | "1.3", "2.0" |
| major | boolean | |
| locked | boolean | |
| created_by | ulid | FK |
| summary | varchar(500) | nullable |
| storage_key | varchar(500) | S3/GCS path |
| file_size | bigint | bytes |
| checksum | varchar(64) | SHA-256 |

---

## Migration

```
997000_create_dms_document_versions_table
997001_create_dms_retention_policies_table
997002_create_dms_legal_holds_table
```

---

## Related

- [[MOC_DMS]]
- [[document-templates]]
- [[document-workflows]]
- [[contract-repository]]
