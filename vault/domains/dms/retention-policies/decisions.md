---
domain: dms
module: retention-policies
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Retention Policies — Decisions

## ADR: Archive/delete via `dms.library` service, never direct `dms_documents` writes

- **Context:** `RetentionService::evaluate` executes archive (`is_archived = true`) and delete (soft/hard) on matching documents. Those documents live in `dms_documents`, owned by [[../document-library/_module|dms.library]]. The source phrasing ("RetentionService executes archive/delete on matching documents") could imply retention writes `dms_documents` directly.
- **Decision:** Frame every document mutation as a **command to `dms.library`'s `DocumentService`** (archive / softDelete methods). Retention only ever writes its own three tables. Per [[../../../security/data-ownership|data-ownership]], writing another domain's tables is a security + integrity violation.
- **Consequences:** Retention depends on `dms.library` exposing archive/soft-delete methods on `DocumentService`. Whether those methods exist as specified is UNVERIFIED — flagged in [[unknowns]]. Media purge on hard-delete routes through [[../../core/file-storage/_module|core.files]].

## ADR: Legal hold always wins over policy — blocks archive AND delete

- **Decision:** An active legal hold (`released_at IS NULL`) exempts a document from **all** retention actions, including archive, not just deletion. `evaluate` skips held documents before applying any action.
- **Consequences:** Compliance-driven holds cannot be silently circumvented by an archive policy. One active hold per document enforced at the action layer.

## ADR: Soft-delete then hard-delete after 30-day grace *(assumed)*

- **Context:** Deletion needs a recovery window before bytes are irreversibly purged.
- **Decision:** `delete` policies soft-delete first; a grace pass hard-deletes + purges media via `core.files` after 30 days *(assumed)*.
- **Consequences:** A trash/restore window exists. Whether it shares a bin with library soft-deletes is open ([[unknowns]]).

## ADR: Daily run idempotent via log-row guard

- **Decision:** `ProcessRetentionCommand` guards each `(document_id, action)` against an existing `dms_retention_log` row + date guards, so a same-day re-run is a no-op. Chunked, per-document `try/catch`.
- **Consequences:** Safe to re-run after a partial failure without double-archiving or double-notifying. The log doubles as compliance proof and idempotency ledger.

## ADR: Pre-deletion notice to owner 7 days before *(assumed)*

- **Decision:** Before a `delete` action executes, notify the document owner 7 days ahead via [[../../core/notifications/_module|core.notifications]], logged as `notified`. Sent once (log-guarded).
- **Consequences:** Owners get a chance to place a legal hold or move the document before deletion.
