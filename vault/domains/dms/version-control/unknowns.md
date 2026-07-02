---
domain: dms
module: version-control
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Version Control — Unknowns

## UNVERIFIED

> [!warning] UNVERIFIED — `dms.versions.view-any` permission mismatch
> The access contract (in `architecture.md` and `security.md`) gates every artifact on
> `Auth::user()->can('dms.versions.view-any')`, but the source spec's **Permissions** section lists only
> `dms.versions.upload`, `dms.versions.restore`, and `dms.versions.force-unlock` — there is **no**
> `dms.versions.view-any`. Resolution needed: either (a) add `dms.versions.view-any` to the permission
> catalogue, or (b) gate viewing on `dms.library.view-any` (since versions live on the library's viewer).
> Build-blocking for the seeder + `canAccess()`.

## Assumed Items

- Lock auto-expiry window of **4h** *(assumed)* — not stated in source; drives `ExpireStaleLocksCommand`.
- `ExpireStaleLocksCommand` schedule frequency *(assumed)* — not specified (assumed every 15 min).
- Metadata comparison is size/date only; full content diff out of scope for v1 *(assumed)*.
- `change_note` treated as non-sensitive, unencrypted free text *(assumed)*.
- Metadata updates flow through `dms.library`'s `DocumentService` (cross-domain write ban) *(assumed)* — source says "updates document size/mime/extracted text job" without naming the API.
- Lock state read on page load, no realtime badge refresh *(assumed)*.

## Open Questions

- Should a `DocumentVersionUploaded` / `DocumentRestored` cross-domain event be fired (audit, approvals)? Currently none.
- Retention interaction: when [[../retention-policies/_module|retention]] archives/deletes a document, what happens to its version history and stored media?
- Restore permission scope — does `dms.versions.restore` imply `dms.versions.upload`, or are they independent?
- Should locking emit a notification to the current lock holder when force-unlocked?
- Storage cost of never-deleting versions + restore-creates-copy — is there a version-count or age cap?
