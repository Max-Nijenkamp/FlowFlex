---
tags: [flowflex, domain/lms, compliance, certification, phase/7]
domain: Learning & Development
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-08
---

# Certification & Compliance Training

Mandatory training, industry certifications, and regulatory compliance — tracked automatically. Get ahead of renewal deadlines before they lapse. Replaces spreadsheets and calendar reminders with an automated compliance engine.

**Who uses it:** L&D managers, HR, compliance officers, all employees (as learners)
**Filament Panel:** `lms`
**Depends on:** Core, [[Course Builder & LMS]], [[HR & People]]
**Phase:** 7

---

## Features

### Compliance Training Management

- Assign mandatory training by: role, department, location, employment type
- Set recurrence: one-time, annual, bi-annual, custom interval
- Due date auto-set on hire + on each recurrence
- Grace period: configurable days after due date before "overdue" status
- Manager approval required: some certifications need manager sign-off after completion

### Certification Tracking

- Store external certifications (NEBOSH, ISO auditor, First Aid, industry-specific)
- Upload supporting document (PDF certificate)
- Expiry date tracked with automated renewal reminders (90/30/7 days before)
- Issued by: external body or internal FlowFlex course
- Certification status per employee: valid / expiring / expired / pending

### Compliance Dashboard

- Real-time compliance rate per team/department/company
- Traffic light RAG status: green (>90% complete), amber (75–90%), red (<75%)
- Overdue list: who is overdue, by how long, assigned manager
- Upcoming expirations this month view
- Export compliance report (PDF, CSV) for auditors

### Automated Reminders

- Learner: "Your Food Safety certification expires in 30 days. Complete renewal now."
- Manager: "3 team members are overdue on GDPR training — due tomorrow"
- HR: weekly digest of team-wide compliance status
- Escalation: if still overdue after grace period → HR director notified

### Regulatory Frameworks

- Pre-built compliance sets: GDPR, ISO 27001, SOC 2, Health & Safety (UK/EU), Food Safety
- EU/NL specific: ARBO-wet, BHV (Bedrijfshulpverlening), NEN standards
- Custom framework builder: create your own compliance requirement set
- Audit report: one-click PDF showing all completions with dates for a given framework

### Skills & Compliance Integration

- Completed compliance training auto-updates Skills Matrix entries
- Certifications shown on employee profile
- Succession Planning reads certification status (can't promote if compliance incomplete)

---

## Database Tables (3)

### `lms_compliance_requirements`
| Column | Type | Notes |
|---|---|---|
| `course_id` | ulid FK | |
| `role` | string nullable | required for this role |
| `department_id` | ulid FK nullable | |
| `recurrence` | enum | `once`, `annual`, `biannual`, `custom` |
| `recurrence_days` | integer nullable | for custom |
| `grace_period_days` | integer default 14 | |
| `framework_tag` | string nullable | GDPR, ISO27001, etc. |

### `lms_employee_certifications`
| Column | Type | Notes |
|---|---|---|
| `employee_id` | ulid FK | |
| `course_id` | ulid FK nullable | null if external cert |
| `title` | string | cert name |
| `issued_by` | string nullable | external body |
| `issued_at` | date | |
| `expires_at` | date nullable | |
| `document_file_id` | ulid FK nullable | uploaded cert PDF |
| `status` | enum | `valid`, `expiring`, `expired`, `pending` |
| `manager_approved_at` | timestamp nullable | |

### `lms_compliance_snapshots`
| Column | Type | Notes |
|---|---|---|
| `snapshot_date` | date | |
| `department_id` | ulid FK nullable | |
| `required_count` | integer | |
| `completed_count` | integer | |
| `overdue_count` | integer | |
| `rate_pct` | decimal | |

---

## Permissions

```
lms.compliance.view
lms.compliance.assign
lms.compliance.manage-requirements
lms.compliance.export-audit-report
lms.compliance.manage-certifications
```

---

## Competitor Comparison

| Feature | FlowFlex | TalentLMS | Learnupon | SAP SuccessFactors |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ | ❌ | ❌ |
| Auto-recurrence tracking | ✅ | ✅ | ✅ | ✅ |
| Regulatory framework presets | ✅ | ❌ | partial | ✅ |
| NL/EU ARBO-wet support | ✅ | ❌ | ❌ | partial |
| Succession integration | ✅ | ❌ | ❌ | ✅ |
| One-click auditor export | ✅ | partial | ✅ | ✅ |

---

## Related

- [[LMS Overview]]
- [[Course Builder & LMS]]
- [[Skills Matrix & Gap Analysis]]
- [[Succession Planning]]
- [[Data Privacy]]
