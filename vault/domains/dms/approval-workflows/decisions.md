---
domain: dms
module: approval-workflows
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Approval Workflows — Decisions

## ADR: State machine via `spatie/laravel-model-states`

- **Context:** An approval request moves through a small, well-defined lifecycle with guarded transitions and side effects (lock/unlock, notifications).
- **Decision:** Model the request status as `spatie/laravel-model-states` — `Pending`, `InReview`, `Approved`, `Rejected` under `app/States/DMS/ApprovalRequest/`. Transitions carry the side effects (lock document, notify next approver, notify submitter on completion).
- **Consequences:** Illegal transitions are impossible; side effects live in transition classes, not scattered across the service. See [[architecture#State Machine]].

## ADR: Sequential vs parallel workflows

- **Context:** Some approvals are strictly ordered (manager → director); others need every approver to sign off but in any order.
- **Decision:** A `type` column on the workflow: `sequential` walks `steps` one at a time via `current_step`; `parallel` fans out to all approvers and completes only when **all** approve. One reject rejects either type.
- **Consequences:** A single `act()` path handles both, branching on `type`. Parallel requests need completion-detection across all approvers rather than a pointer.

## ADR: One open request per document (partial unique)

- **Decision:** A partial unique constraint enforces a single open (`pending`/`in_review`) request per document; `submit()` throws `OpenRequestExistsException` on violation.
- **Consequences:** No two concurrent approval chains for the same document. Re-submitting after a rejection creates a **new** request (history kept), which is allowed because the prior one is no longer open.

## ADR: Document locked while in approval (soft-dep on dms.versions)

- **Context:** A document under review must not be edited out from under its approvers.
- **Decision:** Lock the document while the request is in review. When [[../version-control/_module|dms.versions]] is active, use its version lock mechanism (a **command** to that service — the lock column lives in the versions table). Otherwise fall back to a plain flag.
- **Consequences:** `dms.approvals` never writes the versions table directly ([[../../../security/data-ownership]]); locking degrades gracefully to a flag when versioning is off.

## ADR: Approver may not be the submitter *(assumed)*

- **Context:** Self-approval defeats the purpose of a review chain.
- **Decision:** `act()` rejects an action where the actor is the request's `submitted_by`.
- **Consequences:** Enforced at the service layer (not just a permission). Marked *(assumed)* — the source states it as an assumption; revisit if single-person companies need self-approval. See [[unknowns]].

## ADR: Comment required on reject

- **Decision:** `ApprovalActionData.comment` is `required_if` the action is `rejected` (and `changes`); the reason is surfaced to the submitter.
- **Consequences:** Every rejection has an explanatory trail in `dms_approval_actions`.
