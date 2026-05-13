---
type: module
domain: Document Management
panel: dms
phase: 4
status: complete
cssclasses: domain-dms
migration_range: 995500–995999
last_updated: 2026-05-12
---

# Document Workflows

Route documents through review, approval, and sign-off processes. Replace email chains with structured, auditable workflows with deadlines and auto-escalation.

---

## Workflow Types

| Workflow | Example |
|---|---|
| Sequential approval | CEO signs only after CFO approves |
| Parallel approval | All department heads approve simultaneously |
| Review + comment | Legal reviews draft, comments, returns for revision |
| Sign-off chain | HR offer letter: HR → hiring manager → employee signs |
| Acknowledgement | Policy distributed → each employee must acknowledge |

---

## Workflow Builder

Visual workflow designer:
- Steps: Review / Approve / Sign / Acknowledge / Notify
- Assignees: specific person, role, or dynamic (e.g., "direct manager of requester")
- Deadlines per step (e.g., 48 hours)
- Auto-escalation: if step missed → escalate to manager
- Branch logic: if legal review finds issues → route to revision step

---

## Document Statuses

```
Draft → In Review → Revision Requested → Approved → Sent for Signature → Signed → Filed
                  → Rejected
```

Each status transition logged with actor, timestamp, and comments.

---

## Notifications

- Assignee notified via email/in-app when step reaches them
- Requester notified at each status change
- Reminder sent 24h before deadline
- Escalation email to manager on deadline breach

---

## Audit Trail

Full immutable log per document:
- Who opened it (view tracking)
- Who edited what and when (diff per version)
- Who approved / rejected / commented
- IP address + device for signatures
- Exported as PDF audit certificate

---

## Data Model

### `dms_workflow_definitions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| steps | json | ordered step config |

### `dms_document_workflows`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| document_id | ulid | FK |
| definition_id | ulid | FK |
| current_step | int | |
| status | enum | active/completed/rejected/cancelled |
| started_at | timestamp | |
| completed_at | timestamp | nullable |

### `dms_workflow_steps`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| document_workflow_id | ulid | FK |
| step_number | int | |
| assignee_id | ulid | FK |
| action | enum | review/approve/sign/acknowledge |
| status | enum | pending/completed/rejected/escalated |
| due_at | timestamp | nullable |
| completed_at | timestamp | nullable |
| comment | text | nullable |

---

## Migration

```
995500_create_dms_workflow_definitions_table
995501_create_dms_document_workflows_table
995502_create_dms_workflow_steps_table
995503_create_dms_workflow_audit_log_table
```

---

## Related

- [[MOC_DMS]]
- [[document-templates]]
- [[e-signature]]
- [[contract-repository]]
