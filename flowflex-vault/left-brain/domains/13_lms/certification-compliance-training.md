---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: complete
migration_range: 480010–480011
last_updated: 2026-05-12
right_brain_log: "[[builder-log-lms-phase7]]"
---

# Certification & Compliance Training

Mandatory training management — assign required courses to roles, track completion deadlines, send escalating reminders, and generate audit-ready compliance reports.

**Panel:** `lms`  
**Phase:** 7  
**Migration range:** `740000–744999`

---

## Features

### Core (MVP)

- Compliance training assignments: assign mandatory courses to employees by role, department, or individual
- Completion deadlines: set due dates with configurable reminder schedule (7-day, 3-day, 1-day, overdue)
- Escalating notifications: learner → manager → HR if overdue
- Certification tracking: issued certificates with expiry dates and renewal triggers
- Compliance dashboard: % complete per course, per department, per employee
- Audit export: compliance completion report (PDF, CSV) suitable for regulators

### Advanced

- Recurring mandatory training: set annual/biannual renewal requirement automatically
- External certification import: log externally obtained certifications (e.g. GDPR, Health & Safety)
- Near-expiry alerts: notify 60/30/7 days before certification expires
- Regulatory frameworks: tag courses to specific frameworks (ISO 27001, GDPR, Health & Safety, SOX)
- Exemptions: record legitimate exemptions with approver sign-off

### AI-Powered

- Risk scoring: flag employees overdue on compliance training as compliance risk
- Predictive completion: forecast which employees are likely to miss deadline based on engagement

---

## Data Model

```erDiagram
    compliance_assignments {
        ulid id PK
        ulid company_id FK
        ulid course_id FK
        string target_type
        string target_value
        date due_date
        string recurrence
    }

    employee_certifications {
        ulid id PK
        ulid employee_id FK
        string certification_name
        string source
        date issued_at
        date expires_at
        string certificate_url
        ulid uploaded_by FK
    }

    compliance_assignments ||--o{ course_enrollments : "generates"
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `ComplianceDeadlineApproaching` | 7/3/1 days before due | Notifications (learner + manager) |
| `ComplianceTrainingOverdue` | Past due date | Notifications (HR escalation) |
| `CertificationEarned` | Course passed + cert issued | HR (update employee record) |
| `CertificationExpired` | Expiry date reached | Notifications + LMS (auto-enrol renewal) |

### Consumed

| Event | From | Action |
|---|---|---|
| `EmployeeHired` | HR | Auto-assign mandatory onboarding compliance courses |
| `JobTitleChanged` | HR | Re-evaluate compliance requirements for new role |

---

## Permissions

```
lms.compliance.view-any
lms.compliance.assign-training
lms.compliance.manage-certifications
lms.compliance.view-audit-reports
lms.compliance.export
```

---

## Related

- [[MOC_LMS]]
- [[course-builder-lms]] — compliance courses live here
- [[entity-employee]]
- [[MOC_HR]] — certifications stored on employee profile
- [[MOC_Legal]] — regulatory framework mapping
