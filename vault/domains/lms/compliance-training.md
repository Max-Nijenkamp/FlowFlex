---
type: module
domain: Learning & Development
panel: lms
module-key: lms.compliance
status: planned
color: "#4ADE80"
---

# Compliance Training

> Mandatory training assignment, completion deadline tracking, escalating reminders, and audit-ready compliance reporting.

**Panel:** `lms`
**Module key:** `lms.compliance`

---

## What It Does

Compliance Training turns the LMS into a mandatory training management system. Administrators assign required courses to employees by role, department, or individually, and set completion deadlines. The system sends escalating reminder notifications to learners, then managers, then HR if training is not completed on time. Compliance dashboards show percentage completion by course, department, and individual, and the module produces audit-ready exports suitable for regulators and certifying bodies.

---

## Features

### Core
- Compliance assignment: assign mandatory courses to employees by role, department, or individual
- Deadline configuration: set due dates per assignment with configurable lead time
- Escalating reminders: automated notifications at 7 days, 3 days, and 1 day before due, then overdue escalation to manager and HR
- Compliance dashboard: completion percentage per course, department, and employee in real time
- Audit export: compliance completion report in PDF and CSV formats

### Advanced
- Recurring mandatory training: configure annual or biannual renewal automatically
- External certification import: log third-party credentials (e.g. Health & Safety, GDPR awareness) against employee records
- Near-expiry certification alerts: notify 60/30/7 days before an external certification expires
- Regulatory framework tagging: tag courses to compliance frameworks (ISO 27001, GDPR, SOX, Health & Safety)
- Exemptions: record legitimate exemptions (e.g. medical leave) with approver sign-off and documented justification

### AI-Powered
- Compliance risk scoring: rank employees by risk level based on overdue training and role criticality
- Predictive completion: flag employees likely to miss a deadline based on their engagement pattern
- Framework gap detection: cross-reference current course assignments against a chosen regulatory framework

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
        json framework_tags
        timestamps created_at_updated_at
    }

    compliance_exemptions {
        ulid id PK
        ulid assignment_id FK
        ulid employee_id FK
        text reason
        ulid approved_by FK
        date approved_at
        timestamps created_at_updated_at
    }

    external_certifications {
        ulid id PK
        ulid employee_id FK
        ulid company_id FK
        string certification_name
        string issuing_body
        date issued_at
        date expires_at
        string evidence_url
        ulid logged_by FK
        timestamps created_at_updated_at
    }

    compliance_assignments ||--o{ compliance_exemptions : "can have"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `compliance_assignments` | Mandatory training rules | `id`, `company_id`, `course_id`, `target_type`, `due_date`, `recurrence` |
| `compliance_exemptions` | Exemption records | `id`, `assignment_id`, `employee_id`, `reason`, `approved_by` |
| `external_certifications` | Third-party credentials | `id`, `employee_id`, `certification_name`, `expires_at`, `evidence_url` |

---

## Permissions

```
lms.compliance.view-any
lms.compliance.assign-training
lms.compliance.manage-exemptions
lms.compliance.view-audit-reports
lms.compliance.export
```

---

## Filament

- **Resource:** `App\Filament\Lms\Resources\ComplianceAssignmentResource`
- **Pages:** `ListComplianceAssignments`, `CreateComplianceAssignment`, `EditComplianceAssignment`
- **Custom pages:** `ComplianceDashboardPage`, `AuditExportPage`
- **Widgets:** `ComplianceCompletionWidget`, `OverdueTrainingWidget`, `ExpiringCertificationsWidget`
- **Nav group:** Compliance

---

## Displaces

| Feature | FlowFlex | Cornerstone | Docebo | TalentLMS |
|---|---|---|---|---|
| Role-based mandatory assignment | Yes | Yes | Yes | Yes |
| Escalating reminders | Yes | Yes | Partial | No |
| Audit-ready export | Yes | Yes | Yes | Partial |
| Regulatory framework tagging | Yes | Yes | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[courses]] — compliance courses are built in the course builder
- [[certifications]] — certifications issued on compliance completion
- [[skills]] — compliance training can update relevant skill ratings
- [[analytics]] — compliance KPIs and trend reporting
