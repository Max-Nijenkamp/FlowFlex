---
domain: legal
module: compliance-registers
feature: audit-readiness-dashboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Audit Readiness Dashboard

At-a-glance % compliance per framework and a gap list of non-compliant controls.

## Behaviour

- Readiness % per framework = compliant / (total − not-applicable) ([[../architecture]]).
- Gap list = non-compliant controls, grouped by framework, ownerable.
- Drives audit preparation.

## UI

- **Kind**: custom-page (dashboard)
- **Page**: `ComplianceDashboardPage` (`/legal/compliance/dashboard`).
- **Layout**: readiness gauge/bar per framework; gap list panel (non-compliant controls with owner + due tasks); trend if available.
- **Key interactions**: switch framework; click gap → control; assign owner; drill to control.
- **States**: empty ("No frameworks configured") · loading (gauge skeletons) · error (toast + retry) · selected (framework focused, its gaps listed).
- **Gating**: `legal.compliance.view-any`.

## Data

- Owns / writes: none (read/aggregate over own `legal_frameworks`, `legal_controls`, `legal_compliance_tasks`).
- Reads: own tables via `ComplianceService::readiness`.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: audit-prep view; export pack is a candidate opportunity.
- Shared entity: none.

## Unknowns

- Auditor export pack (PDF) not yet specified — [[../unknowns]] + [[../_opportunities]].

## Related

- [[../_module|Compliance Registers]] · [[./control-management]] · [[./framework-registers]]
