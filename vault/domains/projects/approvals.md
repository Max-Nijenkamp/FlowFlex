---
type: module
domain: Projects & Work
panel: projects
module-key: projects.approvals
status: planned
color: "#4ADE80"
---

# Approvals

> Approval workflows for project deliverables — requester submits, approvers review, outcome recorded — with multi-step approval chains and audit trail.

**Panel:** `projects`
**Module key:** `projects.approvals`

## What It Does

The Approvals module provides a lightweight, configurable approval workflow that any project deliverable can be routed through. A requester submits an approval request with a title, description, and attachments. The workflow routes the request to one or more approvers in sequence or parallel. Each approver can approve or reject with a comment. The final outcome (approved or rejected) is recorded and the requester notified. Approvals integrate with Documents (approve a document for release), Milestones (mark a milestone as formally approved by a stakeholder), and any other context where a sign-off is needed.

## Features

### Core
- Approval request: title, description, linked item (document, milestone, or freeform), file attachments, submitter
- Approval workflow templates: define named sequences of approvers (e.g. "Two-level budget approval" = Line Manager → Finance Director)
- Sequential approval: approvers notified one at a time — next approver receives request only after the previous approves
- Parallel approval: all approvers notified simultaneously — outcome determined when all have responded (or first rejection)
- Request status: `pending` → `approved` / `rejected` / `withdrawn`

### Advanced
- Delegated approval: approver can delegate to a substitute (e.g. while on leave) — delegation recorded in audit trail
- Approval deadline: set a response deadline per approver — reminder notifications fired at T-24h; auto-escalate to manager if not responded
- Conditional routing: route to different approver chains based on request attributes (e.g. value above €10,000 routes to CFO)
- Bulk approval: reviewer sees a list of pending requests and can bulk approve low-risk items with one click
- Full audit trail: every approval, rejection, comment, and delegation logged with timestamp and actor — immutable

### AI-Powered
- Risk classification: AI analyses the request description and attached documents to classify the approval as routine / elevated / high-risk — suggested routing updated accordingly
- Approval time prediction: based on historical approval times per approver, AI estimates when the request will be resolved — shown to requester as "Expected by" date

## Data Model

```erDiagram
    approval_requests {
        ulid id PK
        ulid company_id FK
        string title
        text description
        string approvable_type
        ulid approvable_id FK
        ulid submitted_by FK
        ulid workflow_template_id FK
        string status
        timestamp deadline
        timestamps created_at/updated_at
    }

    approval_steps {
        ulid id PK
        ulid request_id FK
        ulid approver_id FK
        integer step_order
        string mode
        string status
        text comment
        timestamp responded_at
        ulid delegated_to FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `mode` | sequential / parallel |
| `status` (step) | pending / approved / rejected / delegated |
| `approvable_type` / `approvable_id` | Polymorphic — document, milestone, or freeform |

## Permissions

- `projects.approvals.submit`
- `projects.approvals.review`
- `projects.approvals.delegate`
- `projects.approvals.view-all`
- `projects.approvals.manage-templates`

## Filament

- **Resource:** `ApprovalRequestResource`, `ApprovalWorkflowTemplateResource`
- **Pages:** `ListApprovalRequests`, `ViewApprovalRequest` (with step timeline and comment history)
- **Custom pages:** None
- **Widgets:** `PendingApprovalsWidget` — count of approvals awaiting current user's action on dashboard
- **Nav group:** Work (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| DocuSign | Document approval and sign-off |
| Jira Workflow | Issue approval and sign-off |
| Monday.com Approvals | Work item approval workflows |
| Nintex | Business process approval automation |

## Related

- [[tasks]]
- [[documents]]
- [[milestones]]
- [[wikis]]
