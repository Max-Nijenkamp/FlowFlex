---
type: module
domain: Document Management
panel: dms
module-key: dms.workflows
status: planned
color: "#4ADE80"
---

# Document Workflows

> Document workflow automation — draft → review → approve → publish with configurable stages, assignees, and deadline enforcement.

**Panel:** `dms`
**Module key:** `dms.workflows`

---

## What It Does

Document Workflows automates the structured lifecycle of formal documents such as policies, contracts, and quality management documents. Administrators define workflow templates specifying the stages a document must pass through — for example draft, legal review, compliance review, executive approval, and publication — with assignees, SLA timers, and escalation rules for each stage. When a document is submitted to a workflow, the system routes it through each stage in sequence, sending notifications and tracking completion. The final approved version is published to the document library automatically.

---

## Features

### Core
- Workflow template creation: define stages with name, assignee (role or specific user), and maximum days allowed
- Document submission: attach a document from the library to a workflow template to start a run
- Stage completion: assignee reviews the document, adds notes, and marks the stage complete or returned
- Stage return: return the document to the drafter with revision notes; drafter resubmits when ready
- Notification: assignee notified on stage activation; drafter notified on stage completion or return
- Workflow history: full log of every stage transition, decision, and note for audit

### Advanced
- Parallel stages: define stages that can run simultaneously (e.g. legal and finance review at the same time)
- Escalation: auto-escalate to the stage owner's manager if the SLA is breached
- Conditional routing: route to different next stages based on the outcome of the current stage decision
- Bulk workflows: apply the same workflow to a batch of similar documents (e.g. annual policy review)
- Delegation: stage assignees can delegate their stage to a colleague

### AI-Powered
- Stage recommendation: AI suggests which review stages are appropriate based on the document category
- Bottleneck detection: identify which workflow stages consistently cause the longest delays
- Completion prediction: estimate the expected publication date based on average stage durations

---

## Data Model

```erDiagram
    workflow_templates {
        ulid id PK
        ulid company_id FK
        string name
        string document_category
        json stages
        timestamps created_at_updated_at
    }

    document_workflow_runs {
        ulid id PK
        ulid document_id FK
        ulid template_id FK
        ulid company_id FK
        string current_stage
        string status
        timestamps created_at_updated_at
    }

    workflow_stage_actions {
        ulid id PK
        ulid run_id FK
        string stage_name
        ulid actor_id FK
        string decision
        text notes
        timestamp actioned_at
    }

    workflow_templates ||--o{ document_workflow_runs : "governs"
    document_workflow_runs ||--o{ workflow_stage_actions : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `workflow_templates` | Workflow definitions | `id`, `company_id`, `name`, `document_category`, `stages` |
| `document_workflow_runs` | Active workflow instances | `id`, `document_id`, `template_id`, `current_stage`, `status` |
| `workflow_stage_actions` | Stage decisions | `id`, `run_id`, `stage_name`, `actor_id`, `decision`, `actioned_at` |

---

## Permissions

```
dms.workflows.view-any
dms.workflows.manage-templates
dms.workflows.submit-document
dms.workflows.act-on-stage
dms.workflows.view-audit-log
```

---

## Filament

- **Resource:** `App\Filament\Dms\Resources\WorkflowTemplateResource`
- **Pages:** `ListWorkflowTemplates`, `CreateWorkflowTemplate`, `EditWorkflowTemplate`
- **Custom pages:** `ActiveWorkflowRunsPage`, `WorkflowAuditLogPage`
- **Widgets:** `ActiveWorkflowsWidget`, `OverdueStagesWidget`
- **Nav group:** Workflows

---

## Displaces

| Feature | FlowFlex | SharePoint | Confluence | DocuSign CLM |
|---|---|---|---|---|
| Configurable stage workflows | Yes | Yes | No | Yes |
| Parallel stages | Yes | No | No | Yes |
| Escalation rules | Yes | Yes | No | Yes |
| AI stage recommendation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[document-library]] — documents submitted from the library to a workflow
- [[document-collaboration]] — collaboration features used within workflow stages
- [[document-retention]] — published documents subject to retention policies
