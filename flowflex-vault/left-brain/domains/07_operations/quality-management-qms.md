---
type: module
domain: Operations & Supply Chain
panel: operations
phase: 3
status: planned
cssclasses: domain-operations
migration_range: 458000–458499
last_updated: 2026-05-09
---

# Quality Management (QMS)

ISO 9001-aligned quality management. Non-conformance tracking, corrective actions (CAPA), audits, document control for SOPs, and supplier quality ratings.

---

## Non-Conformance Reports (NCR)

When quality defect detected (goods received, production, customer complaint):
1. NCR raised: description, where found, quantity affected, severity
2. Containment action: quarantine affected stock, stop shipment
3. Root cause analysis: 5-Why or Fishbone diagram
4. Corrective action assigned (CAPA)
5. Effectiveness verification after action completed
6. NCR closed

---

## CAPA (Corrective & Preventive Action)

Tracks actions to fix quality problems and prevent recurrence:
- Corrective: fix this specific problem
- Preventive: change process to prevent similar problems

Each CAPA: owner, due date, status, evidence of completion.

---

## Audit Management

Internal and external quality audits:
- Audit plan: scope, auditor(s), date
- Checklist-based (ISO 9001 standard clauses or custom)
- Findings recorded: major/minor non-conformance or observation
- Finding → CAPA raised automatically
- Audit report generated

Supports: ISO 9001, ISO 14001, ISO 45001, IATF 16949 (automotive).

---

## Document Control

QMS procedures and work instructions:
- Controlled documents with version numbers
- Review cycle (e.g., review every 2 years)
- Distribution: staff must acknowledge reading new version
- Obsolete versions archived (not deleted)

Links to [[document-templates]] and [[document-workflows]] in DMS.

---

## Supplier Quality

Supplier quality score:
- % of GRNs with no defects
- NCRs raised against supplier
- Audit results
- Overall score → preferred / conditional / disqualified

Supplier quality data feeds [[supplier-catalog]] in Procurement.

---

## Data Model

### `ops_ncrs`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| ncr_number | varchar(50) | |
| source | enum | goods_in/production/customer/audit |
| severity | enum | critical/major/minor |
| description | text | |
| status | enum | open/contained/capa_raised/closed |
| supplier_id | ulid | nullable FK |

### `ops_capas`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| ncr_id | ulid | nullable FK |
| type | enum | corrective/preventive |
| action_description | text | |
| owner_id | ulid | FK |
| due_date | date | |
| status | enum | open/in_progress/completed/verified |

---

## Migration

```
458000_create_ops_ncrs_table
458001_create_ops_capas_table
458002_create_ops_audits_table
458003_create_ops_audit_findings_table
```

---

## Related

- [[MOC_Operations]]
- [[supplier-qualification-onboarding]]
- [[warehouse-management]]
- [[MOC_DMS]] — document control
