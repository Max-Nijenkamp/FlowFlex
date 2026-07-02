---
domain: it
module: access-provisioning
feature: provisioning-on-hire
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Provisioning on Hire

When HR hires an employee, IT gets a ready-made provisioning checklist: pending access grants derived from
the role template, plus a notification to act on them.

- Consumes `EmployeeHired` from [[../../../hr/employee-profiles/_module|hr.profiles]] via `ProvisionOnHireListener` (queued + `WithCompanyContext`).
- Matches an `it_access_templates` row by the employee's job role name → creates one **pending** grant per template system in `it_access_grants` + fires an IT notification.
- **No matching template = no grants created and no error** (safe no-op).

## UI

- **Kind**: background — event listener, no screen. Results surface in the Pending tab of [[access-grants]] and as an IT notification.
- **Page**: none.
- **Trigger**: `EmployeeHired` → `ProvisionOnHireListener` → pending grants from matching template + IT notification.

## Data

- Owns / writes: `it_access_grants` only (creates pending grants).
- Reads: `it_access_templates` (role match), `it_systems`; employee/job-role reference carried on the event.
- Cross-domain writes: none — reacts to an HR event and writes only IT tables, never hr.profiles' tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `EmployeeHired` from [[../../../hr/employee-profiles/_module|hr.employee-profiles]] → creates pending grants from the matching role template + notifies IT.
- Feeds: pending grants into [[access-grants]] (Pending tab).
- Shared entity: employee + job role owned by hr.profiles (read via event only).

## Unknowns

- Template match by job role name — `*(assumed: template name matching)*`; rename / multi-role behaviour unspecified.
- Single-approval workflow — `*(assumed)*`.

## Related

- [[../_module|Access Provisioning]] · [[access-templates]] · [[access-grants]] · [[deprovisioning-on-offboard]] · [[../../../../architecture/event-bus]]
