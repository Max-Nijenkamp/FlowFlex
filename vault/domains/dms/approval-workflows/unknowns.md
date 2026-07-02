---
domain: dms
module: approval-workflows
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Approval Workflows — Unknowns

## UNVERIFIED

> [!warning] UNVERIFIED
> **`dms.approvals.view-any` is referenced but not defined.** The access contract in [[architecture#Access contract]] / [[security#Access Contract]] gates on `dms.approvals.view-any`, but the source `## Permissions` list only names `dms.approvals.manage-workflows`, `dms.approvals.submit`, `dms.approvals.act`. Resolve by either adding `view-any` to the permission seeder or re-gating the resources on one of the three listed permissions.

## Assumed Items

- Approver may not be the submitter *(assumed)* — stated as an assumption in the source; enforced in `act()`.
- `comment` required on **request-changes** as well as reject *(assumed)* — source explicitly requires it only on reject; DTO rule assumed `required_if` for both.
- No column encryption on approval comments / audit rows *(assumed)*.
- No public/portal surface; all actions are internal `/dms` Filament resources *(assumed)*.
- No module-specific queued jobs; notification delivery deferred to `core.notifications` *(assumed)*.

## Open Questions

- **Lock fallback shape** — when `dms.versions` is inactive, where does the "flag otherwise" lock live? Source says a flag but does not name a column/table. Currently no lock column is owned by `dms.approvals`.
- **Parallel completion semantics** — does every approver in the `steps` set need to act exactly once, or is a quorum acceptable? Source says "all must approve".
- **Re-submit after request-changes** — does resubmit restart at `current_step = 0` for both sequential and parallel? Assumed yes (chain restarts).
- **Cross-domain event** — should an `ApprovalCompleted` / `DocumentApproved` event be fired so `dms.library` (or audit/search) can react to publication? Currently none (`fires-events: []`).
- **Workflow assignment** — is a workflow chosen per-submission, or bound to a folder/document-type? Source has the submitter pick `workflow_id`; folder-level defaults are not specified.
