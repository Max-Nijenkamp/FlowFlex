---
domain: dms
module: approval-workflows
feature: submit-for-approval
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Submit for Approval

Submit a document into an approval workflow: create the request, lock the document, and notify the first approver(s).

## Behaviour

1. Validate `SubmitForApprovalData`: `document_id` accessible to the submitter and with **no open request**; `workflow_id` resolves in the company.
2. `ApprovalService::submit()` creates the `dms_approval_requests` row (`status = pending`, `submitted_by`, `current_step = 0`).
3. Transition `pending → in_review` (state machine) — side effects fire on this transition.
4. **Lock the document**: command [[../version-control/_module|dms.versions]] to lock via its version-lock mechanism when active; otherwise set a flag. The lock lives in the versions table — a command, not a direct write ([[../../../../security/data-ownership]]).
5. **Notify the first approver(s)** via [[../../core/notifications/_module|core.notifications]] — the current step's approver (sequential) or all approvers (parallel).
6. A second submit on the same document throws `OpenRequestExistsException`.

## UI

- **Kind**: simple-resource
- **Page**: a "Submit for approval" row/create action within `ApprovalRequestResource` (Approvals nav group, `/dms/approval-requests`). *(May also be triggered from the document viewer — [[../../document-library/features/document-viewer|Document Viewer]] — but the owning artifact is the request resource.)*
- **Layout**: modal form — pick document + workflow.
- **Key interactions**: submit → request created, document locks, confirmation toast; duplicate open request → inline error from `OpenRequestExistsException`.
- **States**: empty (n/a) · loading (submit spinner) · error (open-request-exists / inaccessible document → toast) · selected (n/a).
- **Gating**: `dms.approvals.submit` + document access.

## Data

- Owns / writes: `dms_approval_requests` (this module).
- Reads: the document read model from [[../../document-library/_module|dms.library]].
- Cross-domain writes: none — locks the document by **commanding** `dms.versions`, notifies by **commanding** `core.notifications`; never writes those tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: [[approver-actions|Approver Actions]] act on the request created here; the lock command targets `dms.versions`.
- Shared entity: the document (owned by `dms.library`); the lock column (owned by `dms.versions`).

## Test Checklist

### Unit
- [ ] `SubmitForApprovalData` validation: `document_id` accessible + no open request; `workflow_id` resolves in the company.

### Feature (Pest)
- [ ] `submit()` creates a `pending` request, transitions `pending → in_review`, locks the document, and notifies the first approver(s).
- [ ] A second submit on the same document throws `OpenRequestExistsException` (partial-unique enforced under a concurrent double-submit via row lock).
- [ ] A submitter in company A cannot submit a company B document (tenant isolation).

### Livewire
- [ ] Submit modal picks document + workflow; success toast on submit.
- [ ] Duplicate open request surfaces the `OpenRequestExistsException` error inline; action denied without `dms.approvals.submit`.

## Unknowns

- Lock fallback ("flag otherwise") — which column/table when `dms.versions` is off? Open ([[../unknowns]]).
- Whether submitting also fires a cross-domain event — none in v1 ([[../unknowns]]).

## Related

- [[../_module|Approval Workflows]] · [[workflow-builder]] · [[approver-actions]] · [[approval-audit-trail]]
