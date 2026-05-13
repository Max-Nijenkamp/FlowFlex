---
type: module
domain: Operations
panel: operations
module-key: operations.quality
status: planned
color: "#4ADE80"
---

# Quality Control

> Run inspection checklists on incoming goods and production output, raise non-conformance reports, track corrective actions, and score supplier quality.

**Panel:** `operations`
**Module key:** `operations.quality`

## What It Does

Quality Control provides an ISO 9001-aligned quality workflow without the overhead of a standalone QMS tool. Inspectors run checklists against incoming stock receipts or production batches, record pass/fail results per criterion, and raise a non-conformance report (NCR) when failures are found. Each NCR triggers a corrective and preventive action (CAPA) workflow to address root cause. Supplier quality scores are computed from the history of NCRs and GRN inspection results, feeding into supplier assessments in [[supplier-management]].

## Features

### Core
- Inspection checklists: configurable checklists per product category or supplier (appearance, dimensions, test results, documentation)
- Inspection triggers: auto-create inspection task on goods receipt or production completion
- Pass/fail recording: inspector records results per criterion with notes and photo evidence
- Hold status: goods placed on quality hold until inspection passes; cannot be picked or sold
- Non-conformance report (NCR): raised when inspection fails — records defect type, affected quantity, severity (critical/major/minor)
- NCR workflow: open → contained → root cause identified → CAPA raised → CAPA completed → closed

### Advanced
- CAPA management: each corrective and preventive action has owner, due date, action description, and evidence of completion
- Defect categories: configurable taxonomy (dimensional, cosmetic, functional, documentation, labelling)
- AQL sampling: configurable Acceptable Quality Level sampling plans (AQL 1.0, 2.5, 4.0) — system calculates sample size from batch size
- Supplier quality score: computed from % of GRNs passing inspection, NCR count, CAPA completion rate
- Quality audit schedule: schedule internal audits per department or supplier; record findings linked to CAPA
- Quality dashboard: open NCRs, overdue CAPAs, supplier quality league table

### AI-Powered
- Defect pattern detection: highlight recurring defect types across suppliers or products to focus CAPA effort
- Root cause suggestion: prompt likely root causes based on defect type and historical patterns

## Data Model

```erDiagram
    ops_inspection_checklists {
        ulid id PK
        ulid company_id FK
        string name
        string applies_to
        json criteria
        string aql_level
        timestamps timestamps
    }

    ops_inspections {
        ulid id PK
        ulid company_id FK
        ulid checklist_id FK
        ulid reference_id FK
        string reference_type
        ulid inspector_id FK
        string result
        string status
        integer sample_size
        integer defects_found
        timestamp inspected_at
    }

    ops_ncrs {
        ulid id PK
        ulid company_id FK
        string ncr_number
        ulid inspection_id FK
        ulid supplier_id FK
        string defect_type
        string severity
        integer qty_affected
        text description
        string status
        timestamps timestamps
    }

    ops_capas {
        ulid id PK
        ulid ncr_id FK
        string type
        text action_description
        ulid owner_id FK
        date due_date
        string status
        text evidence
        timestamp completed_at
        timestamps timestamps
    }

    ops_inspection_checklists ||--o{ ops_inspections : "used in"
    ops_inspections ||--o{ ops_ncrs : "generates"
    ops_ncrs ||--o{ ops_capas : "triggers"
```

| Table | Purpose |
|---|---|
| `ops_inspection_checklists` | Reusable checklists with criteria definitions |
| `ops_inspections` | Per-receipt or per-batch inspection records |
| `ops_ncrs` | Non-conformance reports with defect detail |
| `ops_capas` | Corrective and preventive actions |

## Permissions

```
operations.quality.view-any
operations.quality.inspect
operations.quality.raise-ncr
operations.quality.manage-capas
operations.quality.manage-checklists
```

## Filament

**Resource class:** `InspectionResource`, `NcrResource`, `CapaResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `InspectionWorkflowPage` (inspector checklist completion interface), `QualityDashboardPage`
**Widgets:** `OpenNcrsWidget`, `OverdueCApasWidget`, `SupplierQualityLeagueWidget`
**Nav group:** Warehouse

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Qualio | QMS with NCR and CAPA management |
| MasterControl | Document and quality event management |
| ETQ Reliance | Enterprise QMS workflows |
| Intelex | Supplier quality and audit management |

## Related

- [[purchase-orders]] — goods receipts trigger incoming inspection
- [[warehousing]] — quality hold prevents bin putaway until pass
- [[supplier-management]] — supplier quality scores fed from NCR history
- [[production-planning]] — production output inspections linked here
