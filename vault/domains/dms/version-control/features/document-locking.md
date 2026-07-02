---
domain: dms
module: version-control
feature: document-locking
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Document Locking

Lock a document while editing to prevent concurrent version conflicts; unlock when done; auto-expire abandoned locks.

## Behaviour

1. `LockDocumentAction` creates a `dms_document_locks` row for the current user (`document_id` unique — at most one lock per document). If already locked by **another** user, it is rejected unless the caller holds `dms.versions.force-unlock`.
2. While locked by user A, an upload of a new version by any other user throws `DocumentLockedException`; the lock owner uploads normally.
3. `UnlockDocumentAction` releases a lock — own lock, or any lock with `dms.versions.force-unlock`.
4. `ExpireStaleLocksCommand` (scheduled) deletes locks older than the auto-expiry window (4h *(assumed)*), so an abandoned edit cannot block a document indefinitely.

## UI

- **Kind**: action  <!-- lock/unlock action + lock badge on the DocumentViewerPage -->
- **Page**: "Document Viewer" (`/dms/library` viewer) — lock / unlock header action + lock badge.
- **Layout**: a "Locked by {user}" badge in the viewer header; the action toggles lock/unlock; force-unlock exposed only to permitted users.
- **Key interactions**: click lock → badge appears, upload enabled for you; another user sees the badge + a disabled upload; admin clicks force-unlock → confirm → lock cleared.
- **States**: empty (unlocked → "Lock to edit" CTA) · loading (spinner while (un)locking) · error (toast + retry) · selected (locked → badge with holder name, force-unlock shown if permitted).
- **Gating**: lock / unlock own lock — any versions user; overriding another user's lock requires `dms.versions.force-unlock`.

## Data

- Owns / writes: `dms_document_locks` (this module).
- Reads/Commands: `dms.library` `DocumentService` for folder-access scope on the document.
- Cross-domain writes: none — locks are wholly owned here, no other domain's tables touched ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing (no cross-domain event on lock/unlock v1 — [[../unknowns]]).
- Shared entity: `dms_documents` (`dms.library`).

## Unknowns

- 4h auto-expiry window + `ExpireStaleLocksCommand` schedule frequency are *(assumed)* — not in source ([[../unknowns]]).
- Whether force-unlock notifies the displaced lock holder — open ([[../unknowns]]).

## Related

- [[../_module|Version Control]] · [[upload-version]] · [[version-history]] · [[restore-version]]
- [[../../document-library/_module|Document Library]]
