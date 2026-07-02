---
domain: hr
module: hr-analytics
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# HR Analytics — Unknowns

No `*(assumed)*` markers or `## Open Questions` section were present in the source spec.

## Unverified

- UNVERIFIED — permission naming: `## Permissions` lists `hr.analytics.view`, but the access contract checks `hr.analytics.view-any`. Reconcile which permission is authoritative before build.
- UNVERIFIED — source tables `hr_employees`, `hr_leave_requests`, `hr_payroll_runs` must exist and match these names; they belong to sibling modules that were stripped and are themselves `planned`.
- UNVERIFIED — no build-status yet on turnover math, tenant isolation, module gating, salary-suppression, or N+1-free queries; all remain intended per the Test Checklist, none passing.
- UNVERIFIED — named throttle for CSV/PNG export not yet specified (medium security finding open).

Blueprint per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing in this module is built.
