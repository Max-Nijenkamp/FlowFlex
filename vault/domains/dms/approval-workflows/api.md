---
domain: dms
module: approval-workflows
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Approval Workflows — API / DTOs

All input flows through `spatie/laravel-data` DTOs — never `$request->all()`.

## `CreateWorkflowData`

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `type` | string | required, `in:sequential,parallel` |
| `steps` | array | required, `min:1`; each entry has exactly one of `role_id` / `user_id`, and it must resolve |

## `SubmitForApprovalData`

| Field | Type | Rules |
|---|---|---|
| `document_id` | ulid | required; must be accessible to the submitter; no open request already exists |
| `workflow_id` | ulid | required; must resolve to a workflow in the company |

## `ApprovalActionData`

| Field | Type | Rules |
|---|---|---|
| `request_id` | ulid | required; the actor must be an approver assigned at the current step |
| `action` | string | required, `in:approved,rejected,changes` |
| `comment` | string | `required_if` action is `rejected` or `changes`; nullable on `approved` |

## Service Methods

| Method | Returns | Notes |
|---|---|---|
| `submit(SubmitForApprovalData)` | `ApprovalRequest` | Throws `OpenRequestExistsException` when the document already has an open request. |
| `act(ApprovalActionData)` | `ApprovalRequest` | Actor must be a current-step approver and ≠ submitter. Sequential advances the step; parallel completes when all approvers approve. |
| `pendingFor(User $user)` | `Collection` | The "my approvals" queue. |

## Exceptions

- `OpenRequestExistsException` — thrown by `submit()` when a document already has an open (`pending`/`in_review`) request.

## Public / Portal Endpoints

None. Approval Workflows is an internal `/dms` surface. All actions run through authenticated Filament resources gated by permission + module. *(assumed — no portal surface named in source)*
