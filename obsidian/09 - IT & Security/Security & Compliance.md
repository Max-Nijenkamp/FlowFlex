---
tags: [flowflex, domain/it, security, gdpr, iso27001, soc2, phase/6]
domain: IT & Security
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-07
---

# Security & Compliance

Readiness tooling for GDPR, ISO 27001, and SOC 2. Evidence collection and audit packaging in one place — map controls, attach evidence, track progress, and generate audit-ready exports.

**Who uses it:** IT team, compliance officer, legal team
**Filament Panel:** `it`
**Depends on:** [[Audit Log & Activity Trail]], [[Data Privacy]], [[Access & Permissions Audit]]
**Phase:** 6
**Build complexity:** High — 4 resources, 2 pages, 4 tables

---

## Features

- **Compliance framework library** — pre-built frameworks for ISO 27001 (Annex A), SOC 2 Type II, and GDPR; each with controls pre-populated from the standard
- **Custom frameworks** — add a bespoke framework (e.g. internal security policy) with custom controls
- **Control tracking** — for each control, record evidence, assessment status (compliant/non_compliant/in_progress/not_applicable), and last review date
- **Evidence attachment** — attach files (policies, screenshots, test results) to each control as compliance evidence; stored to S3 via FileStorageService
- **`ComplianceControlFailed` event** — fires when a control is saved as `non_compliant`; notifies compliance officer
- **Assessment runs** — conduct formal compliance assessments against a framework; record overall score, status, and findings JSON
- **Assessment history** — track compliance score over time; chart improvement per assessment cycle
- **Gap analysis view** — filter controls by status; export list of non-compliant and in-progress controls as a gap register
- **Breach reporting workflow** — structured security incident form linked to control evidence; generates a draft GDPR breach notification if personal data is involved
- **Audit export package** — export all evidence, control statuses, and assessments for a framework as a ZIP archive for external auditors
- **Review due alerts** — notify control owner when `last_reviewed_at` + review cycle exceeds today; driven by scheduled check
- **Integration with Audit Log** — link control evidence to entries in the Spatie activity log for a tamper-evident audit trail

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `compliance_frameworks`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "ISO 27001:2022" |
| `version` | string nullable | |
| `description` | text nullable | |
| `is_active` | boolean default true | |
| `is_built_in` | boolean default false | pre-populated framework |

### `compliance_controls`
| Column | Type | Notes |
|---|---|---|
| `compliance_framework_id` | ulid FK | → compliance_frameworks |
| `control_id` | string | e.g. "A.5.1" |
| `title` | string | |
| `description` | text | |
| `category` | string nullable | e.g. "Information Security Policies" |
| `risk_level` | enum | `low`, `medium`, `high`, `critical` |
| `sort_order` | integer default 0 | |

### `control_evidence`
| Column | Type | Notes |
|---|---|---|
| `compliance_control_id` | ulid FK | → compliance_controls |
| `tenant_id` | ulid FK | owner/assessor → tenants |
| `description` | text nullable | |
| `file_id` | ulid FK nullable | → files |
| `status` | enum | `compliant`, `non_compliant`, `in_progress`, `not_applicable` |
| `last_reviewed_at` | timestamp nullable | |
| `next_review_date` | date nullable | |
| `notes` | text nullable | |

### `compliance_assessments`
| Column | Type | Notes |
|---|---|---|
| `compliance_framework_id` | ulid FK | → compliance_frameworks |
| `status` | enum | `in_progress`, `completed` |
| `completed_at` | timestamp nullable | |
| `score` | integer nullable | % compliant |
| `findings` | json nullable | array of {control_id, status, notes} |
| `conducted_by` | ulid FK nullable | → tenants |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `ComplianceControlFailed` | `control_evidence_id`, `control_id` | Notification to compliance officer |

---

## Events Consumed

None — compliance is assessed manually on review cadence.

---

## Permissions

```
it.compliance-frameworks.view
it.compliance-frameworks.create
it.compliance-frameworks.edit
it.compliance-frameworks.delete
it.compliance-controls.view
it.compliance-controls.create
it.compliance-controls.edit
it.control-evidence.view
it.control-evidence.create
it.control-evidence.edit
it.compliance-assessments.view
it.compliance-assessments.create
it.compliance-assessments.export
```

---

## Related

- [[IT Overview]]
- [[Access & Permissions Audit]]
- [[Data Privacy]]
- [[Audit Log & Activity Trail]]
- [[Risk Register]]
