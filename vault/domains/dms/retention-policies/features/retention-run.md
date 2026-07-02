---
domain: dms
module: retention-policies
feature: retention-run
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Retention Run

The daily background job that evaluates every active policy, archives/deletes expired documents, notifies owners before deletion, and logs every action. This is `ProcessRetentionCommand`.

## Behaviour

1. `ProcessRetentionCommand` runs **daily at 03:00** on the `default` queue (`foundation.queues`).
2. Calls `RetentionService::evaluate()`. For each active policy, find matching documents (folder subtree / tag) past `retention_days` measured from `clock_from`.
3. **Skip any document with an active legal hold** (`released_at IS NULL`) — hold wins over policy for both archive and delete.
4. **Archive action** → command `dms.library` `DocumentService::archive` (sets `is_archived = true`, moves to read-only archive folder); log `archived`.
5. **Delete action** → 7 days before expiry *(assumed)*, send owner a pre-deletion notice via [[../../../../core/notifications/_module|core.notifications]]; log `notified`. On expiry, command `DocumentService::softDelete`; log `soft-deleted`.
6. **Grace pass** → documents soft-deleted past the 30-day grace *(assumed)* are hard-deleted; media purged via [[../../../../core/file-storage/_module|core.files]]; log `hard-deleted`.
7. **Idempotent** — a **log-row guard** per `(document_id, action)` + date guards means a same-day re-run performs no duplicate action. Chunked per-document with `try/catch` so one failure logs and the run continues.

## UI

- **Kind**: background. No page. Triggered by the scheduler: `ProcessRetentionCommand` at 03:00 daily. Results are observable only via the [[retention-audit-log|Retention Audit Log]].

## Data

- Owns / writes: `dms_retention_log` (this module) — one row per action.
- Reads: `dms_retention_policies`, `dms_legal_holds` (own); `dms_documents` + folders/tags (owned by [[../../document-library/_module|dms.library]]).
- Cross-domain writes: **none direct.** Archive/soft-delete are **commands to `dms.library`'s `DocumentService`**; media purge is a command to `core.files`; notices go through `core.notifications`. Retention never writes `dms_documents` itself ([[../../../../security/data-ownership|data-ownership]]).

## Relations

- Consumes: policies + holds defined by [[retention-policy]] / [[legal-hold]].
- Commands: `dms.library` (archive / soft-delete), `core.files` (media purge), `core.notifications` (pre-deletion notice).
- Coordinates: [[../../../../core/data-privacy/_module|core.privacy]] erasure (soft) — erasure overrides retention for person-files, but legal holds still win.
- Feeds: writes the [[retention-audit-log|Retention Audit Log]].

## Unknowns

- Grace window (30 days) and pre-deletion notice lead (7 days) both *(assumed)*.
- Whether the run should be triggered by a `core.privacy` `ErasureRequested` event rather than only the daily schedule — open ([[../unknowns]]).

## Related

- [[../_module|Retention Policies]] · [[retention-policy]] · [[legal-hold]] · [[retention-audit-log]]
- [[../../document-library/_module|Document Library]] · [[../../../../foundation/queue-workers/_module|foundation.queues]] · [[../../../../core/notifications/_module|core.notifications]]
