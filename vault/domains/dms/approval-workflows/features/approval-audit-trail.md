---
domain: dms
module: approval-workflows
feature: approval-audit-trail
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Approval Audit Trail

An append-only record of every approval action — who approved or rejected, when, and with what comment — attached to each request.

## Behaviour

1. Every `ApprovalService::act()` call writes a `dms_approval_actions` row: `approver_id`, `action` (`approved`/`rejected`/`changes`), `comment`, `acted_at`.
2. Rows are **append-only** — never updated or deleted; they survive rejection + re-submit (the new request keeps its own trail, the old request's trail is retained).
3. The trail is the audit source for who acted at each step; state transitions are additionally captured by the activity log.
4. Comments are required on reject (and request-changes *(assumed)*), so every non-approval carries a reason.

## UI

- **Kind**: simple-resource (read-only relation)
- **Page**: an **audit-trail relation** on `ApprovalRequestResource` (Approvals nav group, `/dms/approval-requests`).
- **Layout**: read-only table under the request — approver, action (badge), comment, acted-at, in chronological order.
- **Key interactions**: view only; no create/edit/delete (append-only, written by the service).
- **States**: empty (no actions yet → "no approval activity") · loading (table skeleton) · error (n/a) · selected (n/a).
- **Gating**: visible with `dms.approvals.view-any` *(UNVERIFIED — see below)*; inherits the request resource's access.

## Data

- Owns / writes: `dms_approval_actions` (this module) — written only by `ApprovalService::act()`, never edited via the UI.
- Reads: the parent `dms_approval_requests` row; user names for display.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: action rows produced by [[approver-actions|Approver Actions]].
- Feeds: nothing downstream in v1.
- Shared entity: users (platform), referenced as `approver_id`.

## Test Checklist

### Unit
- [ ] Action-row builder maps `action` (`approved`/`rejected`/`changes`) to the correct display badge and retains the comment.

### Feature (Pest)
- [ ] Each `ApprovalService::act()` call appends exactly one `dms_approval_actions` row; rows are never updated or deleted.
- [ ] After a reject + re-submit, the new request keeps its own trail and the original request's trail is retained.

### Livewire
- [ ] The audit-trail relation renders actions chronologically and read-only (no create/edit/delete actions exposed).

## Unknowns

> [!warning] UNVERIFIED
> The trail's gating references `dms.approvals.view-any`, which is not in the source permission list (`manage-workflows`, `submit`, `act`). See [[../unknowns]].

- Whether request-changes actions require a comment (assumed yes) — [[../unknowns]].

## Related

- [[../_module|Approval Workflows]] · [[approver-actions]] · [[submit-for-approval]]
