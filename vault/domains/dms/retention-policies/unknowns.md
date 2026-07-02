---
domain: dms
module: retention-policies
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Retention Policies — Unknowns

## Assumed Items

- Soft-delete → hard-delete + media purge after a **30-day grace** *(assumed)* — grace window not stated in source.
- **Pre-deletion notice 7 days before** deletion *(assumed)* — timing not stated in source.
- **Statutory floors warn at save** (policy cannot delete below a statutory retention class from [[../../../architecture/data-lifecycle|data-lifecycle]]) *(assumed: warning at save)* — enforcement mechanism (block vs warn) unconfirmed.
- `RetentionPolicyResource` shows a **preview affected-count** *(assumed)*.

## UNVERIFIED — flagged tensions

> [!warning] UNVERIFIED — permission mismatch
> The source access contract references `dms.retention.view-any`, but the permission list is only `dms.retention.manage-policies` / `manage-holds` / `view-log`. `view-any` is undefined. Assumed each artifact gates on its own manage/view permission.

> [!warning] UNVERIFIED — data-ownership tension
> The source says "RetentionService executes archive/delete on matching documents." Documents are owned by [[../document-library/_module|dms.library]] (`dms_documents`). Per [[../../../security/data-ownership|data-ownership]], retention must **not** write `dms_documents` directly — it must command `dms.library`'s `DocumentService` (archive / softDelete) or react via events. This module models those as **commands to the library service**. Whether `DocumentService` actually exposes `archive` / `softDelete` methods is unconfirmed against the library spec.

## Open Questions

- Should retention **consume** a `core.privacy` `ErasureRequested` event to trigger deletion (event-driven), rather than coordinating via a read call? Source `consumes-events: []` — currently none.
- Does the retention soft-delete share a **trash/restore bin** with library soft-deletes, or are they separate?
- Statutory-floor enforcement — hard **block** at policy save vs **warning** only?
- Archive folder semantics — is the read-only archive folder per-company auto-created, and who owns it (library vs retention)?
- Legal hold on an already-archived document — does releasing the hold re-expose it to the deletion policy immediately?
