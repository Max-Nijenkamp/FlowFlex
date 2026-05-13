---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: complete
migration_range: 480009
last_updated: 2026-05-12
right_brain_log: "[[builder-log-lms-phase7]]"
---

# External Training Requests

Manage employee requests for external courses, conferences, and certifications — including budget approval, attendance confirmation, and certificate upload on return.

**Panel:** `lms`  
**Phase:** 7  
**Migration range:** `730000–734999`

---

## Features

### Core (MVP)

- Request submission: employee submits external training request with cost, provider, dates, justification
- Approval workflow: line manager → HR / L&D → Finance (if above budget threshold)
- Training budget per employee: annual allocation, spend to date
- Attendance confirmation: mark attended after event
- Certificate upload: store completion certificate against employee record
- Training history: full log of all external training per employee

### Advanced

- Department training budget: aggregate view of spend vs budget per department
- Vendor management: preferred training providers list, discount agreements
- ROI tracking: pre/post skill assessments to measure impact
- Group bookings: one approval for multiple employees at same training

### AI-Powered

- Auto-suggest external training based on skills gaps from [[skills-matrix]]

---

## Data Model

```erDiagram
    external_training_requests {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        string provider_name
        string course_name
        decimal cost
        date training_date
        string status
        text justification
        string approval_status
        ulid approved_by FK
        string certificate_url
        timestamp approved_at
        timestamp attended_at
    }
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `ExternalTrainingApproved` | Approval granted | Notifications (employee), Finance (log expense) |
| `ExternalTrainingCompleted` | Certificate uploaded | HR (update profile), LMS (log development activity) |

### Consumed

| Event | From | Action |
|---|---|---|
| `SkillGapIdentified` | LMS | Suggest relevant external training providers |

---

## Permissions

```
lms.external-training.request
lms.external-training.approve
lms.external-training.manage-budgets
lms.external-training.view-reports
```

---

## Related

- [[MOC_LMS]]
- [[skills-matrix]] — gaps drive training requests
- [[entity-employee]]
- [[MOC_Finance]] — training budget is a cost centre
