---
domain: hr
module: org-chart
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Org Chart — Unknowns

Assumptions and unverified items carried from the spec. Resolve via ADR before/during build.

- **PNG/PDF export rendering** *(assumed)* — export is assumed to be client-side render-to-image; the rendering mechanism (browser canvas vs. server-side PDF) is not confirmed.
- **UNVERIFIED** — `EmployeeService::update` is assumed to own the manager-cycle check; depends on hr.profiles being rebuilt with that behavior.
- **UNVERIFIED** — `OrgNodeData` field set (`employee_id`, `full_name`, `job_title`, `department_name`, `photo_url`) matches the rebuilt hr.profiles schema.
- **UNVERIFIED** — the demo seeder hierarchy (2 departments, manager chains) needs re-seeding after the strip ([[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]).

## Related

- [[_module]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
