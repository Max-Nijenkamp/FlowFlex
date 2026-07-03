---
domain: dms
module: approval-workflows
feature: approver-actions
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Approver Actions

Let an approver approve, reject-with-reason, or request-changes on a request assigned to them — surfaced through a "My approvals" queue.

## Behaviour

1. `pendingFor(User)` builds the **my-approvals** queue: requests where the user is a current-step approver.
2. Validate `ApprovalActionData`: actor is an approver at the current step; `action in:approved,rejected,changes`; `comment required_if` rejected/changes.
3. `ApprovalService::act()` records a `dms_approval_actions` row and enforces **actor ≠ submitter** *(assumed)*.
4. State transitions (via the [[../architecture#State Machine|state machine]]):
   - **approve** — sequential advances `current_step`; parallel completes when all approvers approve → `in_review → approved`, unlock document, notify submitter.
   - **reject** — `in_review → rejected` (any single reject), unlock document, reason sent to submitter.
   - **request-changes** — `in_review → pending`, unlock document for edit; resubmit restarts the chain.
5. Re-submit after a rejection is a **new** request (history kept); request-changes reuses the same request.

## UI

- **Kind**: simple-resource
- **Page**: `ApprovalRequestResource` with a **"My approvals" tab** (Approvals nav group, `/dms/approval-requests`).
- **Layout**: table of pending requests (document, workflow, step, submitter, submitted-at) with **approve / reject / request-changes row actions**; reject + changes open a modal requiring a comment. *(Kept as a simple-resource per source — a bespoke queue could be a custom-page, but the source specifies the #1 CRUD resource with the approval actions as row/table actions.)*
- **Key interactions**: click approve → optimistic advance; reject/changes → comment modal → transition + notification; wrong-step / self-approval → blocked with a toast.
- **States**: empty (no pending approvals → "you're all caught up" CTA) · loading (table skeleton) · error (validation / not-your-step / missing comment → toast) · selected (row highlighted, action modal open).
- **Gating**: `dms.approvals.act`.

## Data

- Owns / writes: `dms_approval_actions` and `dms_approval_requests` (status/`current_step`/`completed_at`) — this module.
- Reads: the document read model from [[../../document-library/_module|dms.library]].
- Cross-domain writes: none — unlocks the document by **commanding** [[../../version-control/_module|dms.versions]], notifies by **commanding** [[../../core/notifications/_module|core.notifications]] ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: [[approval-audit-trail|Approval Audit Trail]] reads the action rows written here; completion commands unlock (`dms.versions`) + notify (`core.notifications`).
- Shared entity: the document (`dms.library`); the lock column (`dms.versions`).

## Test Checklist

### Unit
- [ ] `ApprovalActionData` validation: `action in:approved,rejected,changes`; `comment required_if` action is rejected/changes.
- [ ] `pendingFor(User)` returns only requests where the user is a current-step (sequential) or set-member (parallel) approver.

### Feature (Pest)
- [ ] Sequential: only the current-step approver can act; an approve advances `current_step` in order; a non-current-step approver is blocked.
- [ ] Parallel: request completes (`in_review → approved`) only when all approvers approve; a single reject rejects the whole request.
- [ ] Submitter cannot approve their own request (actor ≠ submitter); reject unlocks the document + notifies submitter; request-changes returns to `pending` and resubmit restarts the chain.
- [ ] Concurrent double-act on the same step is serialised by the row lock (no double advance).

### Livewire
- [ ] Approve row action advances the request; reject/request-changes open a comment modal requiring a reason.
- [ ] Wrong-step or self-approval attempt is blocked with a toast; actions denied without `dms.approvals.act`.

## Unknowns

- Actor ≠ submitter is *(assumed)* — [[../unknowns]].
- Whether request-changes requires a comment (assumed `required_if`) — [[../unknowns]].
- Parallel completion: all-approve vs quorum — open ([[../unknowns]]).

## Related

- [[../_module|Approval Workflows]] · [[submit-for-approval]] · [[approval-audit-trail]] · [[workflow-builder]]
