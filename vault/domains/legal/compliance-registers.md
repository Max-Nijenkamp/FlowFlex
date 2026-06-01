---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.compliance
status: planned
color: "#4ADE80"
---

# Compliance Registers

Track regulatory obligations, compliance tasks, and audit readiness. Registers for GDPR, ISO, industry-specific regulations.

## Core Features

- Compliance framework: GDPR, ISO 27001, SOC 2, industry-specific
- Requirement/control register: list of controls per framework with status
- Control status: compliant / partial / non-compliant / not-applicable
- Evidence attachment per control (documents proving compliance)
- Compliance tasks: recurring obligations (e.g. annual review, quarterly audit)
- Task assignment and due dates
- Audit readiness dashboard: % compliance per framework
- Gap report: non-compliant controls

## Data Model

| Table | Key Columns |
|---|---|
| `legal_frameworks` | company_id, name, description |
| `legal_controls` | framework_id, company_id, reference, requirement, status, owner_id, evidence_note |
| `legal_compliance_tasks` | company_id, control_id, title, due_date, frequency, status, assignee_id |

## Filament

**Nav group:** Compliance

- `FrameworkResource` — manage compliance frameworks
- `ControlResource` — control register with status + evidence
- `ComplianceDashboardPage` (custom page) — readiness per framework

## Related

- [[domains/legal/policy-library]]
- [[domains/core/data-privacy]]
