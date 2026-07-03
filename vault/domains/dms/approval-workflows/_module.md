---
domain: dms
module: approval-workflows
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Approval Workflows

Document approval chains before publication. Route a document through an ordered chain of reviewers, track approval status through a state machine, and only publish once fully approved. Layers on the [[../document-library/_module|Document Library]] — requests target documents, and the document is locked while it is in review.

## Module-key

`dms.approvals`

**Priority:** p2  
**Panel:** dms  
**Permission prefix:** `dms.approvals`  
**Tables:** `dms_approval_workflows`, `dms_approval_requests`, `dms_approval_actions`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../document-library/_module\|dms.library]] | Requests target documents; reads the document read model |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()`, approver roles resolution |
| Hard | [[../../core/notifications/_module\|core.notifications]] | Email + in-app notifications to approvers / submitter |
| Soft | [[../version-control/_module\|dms.versions]] | Document locked via the version lock mechanism while in approval (a plain flag otherwise) |

## Core Features

- **Approval workflow** — an ordered chain of approvers (roles or specific users), stored as a `steps` repeater.
- **Sequential vs parallel** — `sequential` walks step by step via a `current_step` pointer; `parallel` fans out to all approvers at once and completes only when **all** approve.
- **Approval request** — submit a document into a workflow; one open request per document (partial unique).
- **Approval status machine** — `pending → in_review → approved | rejected` (`spatie/laravel-model-states`); request-changes returns to `pending`.
- **Approver actions** — approve, reject (with a required reason), or request changes (back to submitter, re-submittable).
- **My-approvals queue** — `pendingFor(User)` surfaces what a user still has to act on.
- **Approval audit trail** — append-only record of who approved/rejected, when, and comments.
- **Document lock** — locked while in approval (version lock when `dms.versions` active; a flag otherwise), unlocked on completion.
- **Re-submit after rejection** — a new request; the prior history is kept.
- Approver may not be the submitter *(assumed)*.

## See features/

- [[features/workflow-builder|Workflow Builder]] — define the ordered chain, sequential/parallel, steps repeater.
- [[features/submit-for-approval|Submit for Approval]] — submit a document into a workflow, lock it, notify the first approver(s).
- [[features/approver-actions|Approver Actions]] — approve / reject-with-reason / request-changes, and the my-approvals queue.
- [[features/approval-audit-trail|Approval Audit Trail]] — append-only who/when/comments record.

## Build Manifest

```
database/migrations/xxxx_create_dms_approval_workflows_table.php
database/migrations/xxxx_create_dms_approval_requests_table.php
database/migrations/xxxx_create_dms_approval_actions_table.php
app/Models/DMS/{ApprovalWorkflow,ApprovalRequest,ApprovalAction}.php
app/States/DMS/ApprovalRequest/{ApprovalRequestState,Pending,InReview,Approved,Rejected}.php
app/Data/DMS/{CreateWorkflowData,SubmitForApprovalData,ApprovalActionData}.php
app/Contracts/DMS/ApprovalServiceInterface.php
app/Services/DMS/ApprovalService.php
app/Exceptions/DMS/OpenRequestExistsException.php
app/Filament/DMS/Resources/{ApprovalWorkflowResource,ApprovalRequestResource}.php
database/factories/DMS/{ApprovalWorkflowFactory,ApprovalRequestFactory}.php
tests/Feature/DMS/{ApprovalSequentialTest,ApprovalParallelTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see or act on company B's approval requests, workflows, or actions.
- [ ] Module gating: artifacts hidden when `dms.approvals` inactive.
- [ ] Sequential: only the current-step approver can act; the pointer advances in order.
- [ ] Parallel: completes only when all approve; one reject rejects the whole request.
- [ ] Submitter cannot approve their own request.
- [ ] Reject requires a comment; the changes flow unlocks + resubmit restarts the chain.
- [ ] A second open request on the same document is rejected (`OpenRequestExistsException`).
- [ ] Document locked during review; unlocked on completion.
- [ ] The next approver is notified each step.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | Document read model | [[../document-library/_module\|dms.library]] | Requests target documents; read-only |
| Commands | Lock / unlock document | [[../version-control/_module\|dms.versions]] (soft) | Lock lives in the versions table — a command to that service, never a direct write |
| Commands | Notify approver / submitter | [[../../core/notifications/_module\|core.notifications]] | Email + in-app to next approver each step, and to submitter on completion |
| Fires | *(none)* | — | No cross-domain events fired in v1 (source `fires-events: []`) |

**Data ownership:** `dms.approvals` writes only `dms_approval_workflows`, `dms_approval_requests`, `dms_approval_actions`. It **reads** documents from `dms.library` and **commands** the document lock through `dms.versions` (the lock column lives in the versions module's table) and notifications through `core.notifications` — it never writes another domain's tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../document-library/_module|Document Library]] · [[../version-control/_module|Version Control]]
- [[../../core/billing-engine/_module|core.billing]] · [[../../core/rbac/_module|core.rbac]] · [[../../core/notifications/_module|core.notifications]]
- [[../../../architecture/patterns/states]]
