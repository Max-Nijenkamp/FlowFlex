---
domain: procurement
module: requisitions
feature: approval-flow
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Approval Flow

The requisition state machine and matrix-driven multi-level approval. `draft → submitted → approved | rejected → converted_to_po`.

## Behaviour

- On submit: `ApprovalMatrix::chainFor('requisition', total, category)` resolves the ordered levels; snapshotted into `proc_requisition_approvals`; level-1 approver notified.
- Each level approves or rejects (comment required on reject). Approver ≠ requester; current-level (or delegate) only.
- Final approval → `approved`, requester notified, fires `RequisitionApproved`.
- Any reject → `rejected` with reason; resubmit starts a fresh chain.

## UI

- **Kind**: custom-page (surfaces in the shared [[../../approvals/features/pending-approvals-queue|Pending Approvals queue]]) + inline actions on `RequisitionResource`.
- **Page**: approval acts happen from the Pending Approvals queue or the requisition infolist's approval timeline.
- **Layout**: approval timeline (level, approver, action, comment, timestamp) on the requisition; approve/reject buttons for the current-level user.
- **Key interactions**: approve → advance + notify next level (optimistic); reject → require comment, notify requester.
- **States**: empty (no chain yet = draft) · loading (timeline skeleton) · error (toast) · selected (current level highlighted).
- **Gating**: `procurement.requisitions.approve` **and** current-level/delegate check at the service layer.

## Data

- Owns / writes: `proc_requisitions.status/current_level`, `proc_requisition_approvals`.
- Reads: `ApprovalMatrix::chainFor` / `resolveApprover` from [[../../approvals/_module|approvals]].
- Cross-domain writes: none; only fires `RequisitionApproved` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: approval chain from `procurement.approvals`; notification delivery from `core.notifications`.
- Feeds: `RequisitionApproved` → spend analytics + finance budget-commitment listeners.

## Unknowns

- Category for `chainFor` — header vs per-line. `*(assumed: header)*`

## Related

- [[../_module|Requisitions]] · [[../../approvals/features/approval-matrix]] · [[convert-to-po]]
