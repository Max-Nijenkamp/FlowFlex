---
type: module
domain: Document Management
domain-key: dms
panel: dms
module-key: dms.approvals
status: planned
priority: p2
depends-on: [dms.library, core.billing, core.rbac, core.notifications]
soft-depends: [dms.versions]
fires-events: []
consumes-events: []
patterns: [states]
tables: [dms_approval_workflows, dms_approval_requests, dms_approval_actions]
permission-prefix: dms.approvals
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Approval Workflows

Document approval chains before publication. Route a document through reviewers, track approval status, and only publish when fully approved.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/dms/document-library\|dms.library]] | requests target documents |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, approver notifications |
| Soft | [[domains/dms/version-control\|dms.versions]] | document locked via lock mechanism while in approval |

---

## Core Features

- Approval workflow: ordered chain of approvers (roles or specific users)
- Approval request: submit a document into a workflow
- Approval status machine: `pending → in_review → approved | rejected` (spatie/laravel-model-states)
- Sequential (step by step) or parallel (all at once; all must approve)
- Approver actions: approve, reject (with reason), request changes (back to submitter, re-submittable)
- Email + in-app notification to next approver
- Approval audit trail: who approved/rejected, when, comments
- Document locked while in approval (version lock when dms.versions active; flag otherwise)
- Re-submit after rejection (new request, history kept)
- Approver may not be the submitter *(assumed)*

---

## Data Model

### dms_approval_workflows

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| type | string | sequential / parallel |
| steps | jsonb | ordered [{role_id?/user_id?}] |
| deleted_at | timestamp nullable | |

### dms_approval_requests

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), document_id FK, workflow_id FK | ulid | one open request per document (partial unique) |
| status | string default `pending` | state machine |
| submitted_by | ulid FK users | |
| current_step | int default 0 | sequential pointer |
| completed_at | timestamp nullable | |

### dms_approval_actions — id, request_id FK, company_id, approver_id FK, action (approved/rejected/changes), comment nullable (required on reject), acted_at

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `pending` | `in_review` | submit | document locked; first approver(s) notified |
| `in_review` | `approved` | last required approval | unlock; submitter notified |
| `in_review` | `rejected` | any reject | unlock; reason to submitter |
| `in_review` | `pending` (changes) | request-changes | unlock for edit; resubmit restarts chain |

Audited.

---

## DTOs

### CreateWorkflowData — name, type (in:sequential,parallel), steps[] min:1 (each exactly one of role_id/user_id, resolvable)
### SubmitForApprovalData — document_id (accessible, no open request), workflow_id
### ApprovalActionData — request_id (assigned to actor at current step), action (in set), comment (required_if rejected/changes)

## Services & Actions

Interface→Service: `ApprovalServiceInterface` → `ApprovalService`.

- `submit(SubmitForApprovalData)` — throws `OpenRequestExistsException`
- `act(ApprovalActionData)` — validates actor is a current-step approver, ≠ submitter; sequential advances step; parallel completes when all acted approve
- `pendingFor(User $user): Collection` — "my approvals"

---

## Filament

**Nav group:** Approvals

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ApprovalWorkflowResource` | #1 CRUD resource | steps repeater |
| `ApprovalRequestResource` | #1 CRUD resource | "My approvals" tab; approve/reject/changes actions; audit trail relation |

---

## Permissions

`dms.approvals.manage-workflows` · `dms.approvals.submit` · `dms.approvals.act`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Sequential: only current-step approver can act; advances in order
- [ ] Parallel: completes only when all approve; one reject rejects
- [ ] Submitter cannot approve own request
- [ ] Reject requires comment; changes flow unlocks + resubmit restarts
- [ ] Second open request on same document rejected
- [ ] Document locked during review; unlocked on completion
- [ ] Next approver notified each step

---

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

---

## Related

- [[domains/dms/document-library]]
- [[architecture/patterns/states]]
