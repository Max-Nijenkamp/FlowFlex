---
domain: dms
module: approval-workflows
feature: workflow-builder
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Workflow Builder

Define a reusable approval chain: an ordered list of approvers (roles or specific users), run either sequentially or in parallel.

## Behaviour

1. Create a workflow with a `name` and a `type` of `sequential` or `parallel`.
2. Add ordered `steps` (a repeater) — each step names exactly one of `role_id` or `user_id`, and it must resolve within the company.
3. Validate `CreateWorkflowData`: `type in:sequential,parallel`; `steps min:1`; each step has exactly one approver reference.
4. `sequential` runs steps one at a time (submission walks them via `current_step`); `parallel` fans out to all approvers at once, completing only when all approve.
5. Workflows are soft-deletable; existing open requests keep their reference *(assumed)*.

## UI

- **Kind**: simple-resource
- **Page**: `ApprovalWorkflowResource` under the "Approvals" nav group (`/dms/approval-workflows`).
- **Layout**: table (name, type, step count) + create/edit form with a **steps repeater** (reorderable rows; each row = role-or-user select) and a `type` toggle.
- **Key interactions**: add/reorder/remove steps; switching to `parallel` de-emphasises step ordering (all approve). Delete = soft delete.
- **States**: empty (no workflows → "create your first approval workflow" CTA) · loading (table skeleton) · error (validation toast on an unresolved role/user) · selected (n/a).
- **Gating**: `dms.approvals.manage-workflows`.

## Data

- Owns / writes: `dms_approval_workflows` (this module).
- Reads: roles from [[../../core/rbac/_module|core.rbac]]; users for user-steps.
- Cross-domain writes: none — only its own table ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing directly — [[submit-for-approval|Submit for Approval]] references a workflow when creating a request.
- Shared entity: roles/users (owned by `core.rbac` / platform).

## Unknowns

- Whether a soft-deleted workflow with open requests behaves gracefully *(assumed)* — [[../unknowns]].
- Whether workflows can be bound to a folder/document-type as a default — open ([[../unknowns]]).

## Related

- [[../_module|Approval Workflows]] · [[submit-for-approval]] · [[approver-actions]]
