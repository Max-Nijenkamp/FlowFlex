---
domain: hr
module: org-chart
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Org Chart — DTOs & Services

No events fired or consumed. Intended interface; not yet built.

## DTOs

Output only:

- `OrgNodeData` — `employee_id`, `full_name`, `job_title`, `department_name`, `photo_url`, `children[]` (recursive). Built per [[../../../architecture/patterns/custom-pages|custom-page]] data flow; one query + in-memory assembly (no N+1 recursion).

## Services

- `OrgChartService::tree(?string $departmentId = null): array<OrgNodeData>` — returns the root nodes (multi-root capable). Single query, cycle-safe.

## Actions

- `ReassignManagerAction::run(string $employeeId, ?string $newManagerId): void` — delegates to `EmployeeService::update` in hr.profiles, which performs the cycle check.

## Related

- [[_module]]
- [[architecture]]
