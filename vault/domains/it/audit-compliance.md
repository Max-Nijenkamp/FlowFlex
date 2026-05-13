---
type: module
domain: IT & Security
panel: it
module-key: it.audit
status: planned
color: "#4ADE80"
---

# Audit & Compliance

> Run IT compliance checklists, collect control evidence, and maintain an audit-ready trail of IT assets, changes, and access across common frameworks.

**Panel:** `it`
**Module key:** `it.audit`

## What It Does

Audit & Compliance helps IT teams prepare for and pass security audits (ISO 27001, SOC 2, Cyber Essentials, NIS2) by maintaining structured control checklists, gathering evidence against each control, and providing a timeline of IT events (asset changes, access grants, incidents, change approvals) as an audit trail. Rather than scrambling to collect evidence at audit time, controls are maintained on a continuous basis so evidence is always current. Read access to this module can be granted to external auditors via a secure link.

## Features

### Core
- Compliance frameworks: pre-loaded control sets for ISO 27001, SOC 2 Type II, Cyber Essentials, NIS2, and GDPR (IT controls)
- Custom frameworks: build a custom control list for internal audits or bespoke requirements
- Control status: not started, in progress, compliant, non-compliant, not applicable
- Evidence collection: attach documents, screenshots, or links as evidence against each control; each evidence item timestamped and attributed to uploader
- Control owners: assign each control to a responsible owner who receives review reminders
- Audit readiness score: percentage of controls with current evidence across a selected framework

### Advanced
- Evidence expiry: set an expiry period on evidence (e.g., penetration test report valid for 12 months); alert owner before expiry
- Automated evidence: pull evidence automatically from other FlowFlex modules (access review completion rate from [[access-management]], patch cadence from [[vulnerability-management]], change record count from [[change-management]])
- Auditor access: create a read-only auditor view with a time-limited secure link for external reviewers
- Gap analysis: identify controls with no evidence or expired evidence; export gap list for remediation planning
- Audit trail report: time-stamped log of all IT events (access grants/revocations, asset changes, incidents, change approvals) formatted for auditor review
- Control linking: map multiple controls across frameworks that share the same evidence (ISO 27001 A.9 maps to SOC 2 CC6.1)

### AI-Powered
- Control guidance: for each control, suggest the evidence that auditors most commonly request
- Audit readiness forecast: given current evidence completion rate and evidence expiry dates, predict audit readiness in 30/60/90 days

## Data Model

```erDiagram
    it_compliance_frameworks {
        ulid id PK
        ulid company_id FK
        string name
        string version
        boolean is_system_framework
        timestamps timestamps
    }

    it_controls {
        ulid id PK
        ulid framework_id FK
        string control_ref
        string title
        text description
        ulid owner_id FK
        string status
        timestamps timestamps
    }

    it_control_evidence {
        ulid id PK
        ulid control_id FK
        string title
        string evidence_type
        string file_url
        date valid_until
        ulid uploaded_by FK
        timestamp uploaded_at
    }

    it_compliance_frameworks ||--o{ it_controls : "contains"
    it_controls ||--o{ it_control_evidence : "evidenced by"
```

| Table | Purpose |
|---|---|
| `it_compliance_frameworks` | Framework definitions (ISO 27001, SOC 2, custom) |
| `it_controls` | Individual control items with owner and status |
| `it_control_evidence` | Evidence artefacts attached to each control |

## Permissions

```
it.audit.view-any
it.audit.manage-controls
it.audit.upload-evidence
it.audit.grant-auditor-access
it.audit.manage-frameworks
```

## Filament

**Resource class:** `ComplianceFrameworkResource`, `ControlResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `AuditReadinessDashboardPage` (control status heatmap), `AuditTrailPage` (event timeline for auditor export)
**Widgets:** `AuditReadinessScoreWidget`, `ControlsNeedingAttentionWidget`
**Nav group:** Compliance

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Vanta | Automated compliance evidence collection |
| Drata | SOC 2 and ISO 27001 compliance automation |
| Tugboat Logic | Compliance framework and evidence management |
| OneTrust (IT GRC) | Control management and audit trail |

## Related

- [[access-management]] — access review records as audit evidence
- [[vulnerability-management]] — vulnerability remediation evidence
- [[change-management]] — change records as audit trail
- [[asset-management]] — asset inventory as audit evidence
- [[incident-management]] — incident records in audit trail
