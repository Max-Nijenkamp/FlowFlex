---
type: module
domain: Document Management
panel: dms
module-key: dms.approvals
status: planned
color: "#4ADE80"
---

# Approval Workflows

Document approval chains before publication. Route a document through reviewers, track approval status, and only publish when fully approved.

## Core Features

- Approval workflow: ordered chain of approvers
- Approval request: submit a document into a workflow
- Approval status machine: `pending → in_review → approved | rejected` (spatie/laravel-model-states)
- Sequential or parallel approval (all must approve, or any one)
- Approver actions: approve, reject (with reason), request changes
- Email + in-app notification to next approver
- Approval audit trail: who approved/rejected, when, comments
- Document locked while in approval
- Re-submit after rejection

## Data Model

| Table | Key Columns |
|---|---|
| `dms_approval_workflows` | company_id, name, type (sequential/parallel), steps (json: ordered approver roles/users) |
| `dms_approval_requests` | company_id, document_id, workflow_id, status, submitted_by, current_step, completed_at |
| `dms_approval_actions` | request_id, company_id, approver_id, action (approved/rejected/changes), comment, acted_at |

## Filament

**Nav group:** Approvals

- `ApprovalWorkflowResource` — define workflows and approver chains
- `ApprovalRequestResource` — list pending approvals assigned to current user; approve/reject actions
- "My approvals" filter for current user's pending items

## Cross-Domain

- Notifications via Core Notifications + email

## Related

- [[domains/dms/document-library]]
- [[architecture/patterns/states]]
