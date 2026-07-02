---
domain: dms
module: version-control
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Version Control — Decisions

## ADR: Restore creates a NEW version, never mutates history

- **Context:** Restoring an old version could either overwrite the current record or append a new one.
- **Decision:** `VersionService::restore` copies the target historical media as a fresh version (new `version_number`, `is_current = true`); no version row is deleted or edited.
- **Consequences:** History is immutable and fully auditable — every state the document has ever been in is reachable. Storage grows on restore, accepted as the cost of an audit-safe trail.

## ADR: `is_current` partial-unique instead of a pointer column on the document

- **Context:** "Which version is live" must be unambiguous and queryable.
- **Decision:** Each version carries `is_current`; a partial-unique index enforces exactly one current version per document. `uploadVersion` / `restore` flip it inside the transaction.
- **Consequences:** No denormalised `current_version_id` on `dms_documents` to drift out of sync; the constraint is enforced at the database. Version control never writes `dms_documents` ([[../../../security/data-ownership]]).

## ADR: Pessimistic lock with auto-expiry for concurrent edits *(assumed)*

- **Context:** Two users uploading a new version concurrently would create conflicting history.
- **Decision:** A user locks a document (`dms_document_locks`, `document_id` unique) before editing; a non-owner upload throws `DocumentLockedException`. Locks auto-expire after 4h *(assumed)* via `ExpireStaleLocksCommand`; `dms.versions.force-unlock` overrides.
- **Consequences:** Simple, no client heartbeat needed; the trade-off is a stale lock blocks edits until the sweep runs (mitigated by the auto-expiry window).

## ADR: Metadata updates flow through `dms.library`'s `DocumentService` *(assumed)*

- **Context:** A new version changes the document's size, mime, and extracted text — all columns on `dms_documents`, owned by `dms.library`.
- **Decision:** `VersionService` calls `DocumentService` to update metadata and re-dispatch `ExtractDocumentTextJob`; it never writes `dms_documents` directly.
- **Consequences:** Preserves the cross-domain write ban ([[../../../security/data-ownership]]); the current version's text stays searchable through the library's existing pipeline.

## ADR: Metadata-only version comparison for v1 *(assumed)*

- **Decision:** Version comparison shows size/date diff only; full content diff is out of scope for v1.
- **Consequences:** No diff engine dependency; content diff noted in [[unknowns]] as a follow-up.
