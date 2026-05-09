---
type: module
domain: Legal & Compliance
panel: legal
cssclasses: domain-legal
phase: 4
status: planned
migration_range: 555000–559999
last_updated: 2026-05-09
---

# Policy Management

Centralised policy library with version control, mandatory employee sign-off tracking, and automated re-acknowledgement workflows when policies are updated.

**Panel:** `legal`  
**Phase:** 4  
**Migration range:** `555000–559999`

---

## Features

### Core (MVP)

- Policy library: upload or write policies (rich text editor)
- Categories: HR, IT Security, Finance, Legal, Health & Safety, Operations
- Version control: publish new version → old version archived with diff
- Mandatory sign-off: assign policy to employees/roles/departments for acknowledgement
- Sign-off tracking: who has/hasn't acknowledged, with timestamps
- Re-acknowledgement: when policy is updated, require sign-off from all affected employees again
- Policy search: full-text search across all active policies

### Advanced

- Policy review schedule: set mandatory annual review date per policy
- Approval workflow: legal draft → department head review → CEO sign-off → publish
- Policy effectiveness survey: post-acknowledgement quiz to verify understanding
- External policy distribution: share policies with suppliers/partners (without FlowFlex account)
- Policy heatmap: visualise which departments have outstanding sign-offs

### AI-Powered

- Policy gap analysis: compare current policies against regulatory framework requirements
- Plain-language summary: AI generates employee-friendly summary of complex policy

---

## Data Model

```erDiagram
    policies {
        ulid id PK
        ulid company_id FK
        string title
        string category
        string status
        integer version_number
        text content
        ulid published_by FK
        date review_due_date
        timestamp published_at
        softDeletes deleted_at
    }

    policy_acknowledgements {
        ulid id PK
        ulid policy_id FK
        integer policy_version FK
        ulid employee_id FK
        timestamp acknowledged_at
        string ip_address
    }

    policies ||--o{ policy_acknowledgements : "requires"
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `PolicyPublished` | New policy or version published | Notifications (affected employees: sign-off required) |
| `PolicySignOffDue` | Reminder schedule | Notifications (employee + manager if overdue) |
| `PolicySignOffOverdue` | Past deadline | Notifications (HR escalation) |
| `PolicyReviewDue` | Annual review date approaching | Notifications (policy owner) |

### Consumed

| Event | From | Action |
|---|---|---|
| `EmployeeHired` | HR | Auto-assign mandatory policies for acknowledgement |

---

## Permissions

```
legal.policies.view-any
legal.policies.view
legal.policies.create
legal.policies.update
legal.policies.publish
legal.policies.manage-assignments
legal.policies.view-compliance-report
```

---

## Related

- [[MOC_Legal]]
- [[MOC_HR]] — policy sign-off tracked in employee compliance record
- [[entity-employee]]
