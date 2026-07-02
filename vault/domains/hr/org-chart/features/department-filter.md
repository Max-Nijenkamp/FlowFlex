---
domain: hr
module: org-chart
feature: department-filter
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Department Filter

## Purpose

Let users prune the org tree to a single department.

## Intended Behavior

- Department filter in the page header.
- Passes the selected `departmentId` to `OrgChartService::tree($departmentId)`; the tree re-renders scoped to that department.
- No filter selected → full company tree.

## UI

- **Kind**: custom-page
- **Page**: filter control on the Org Chart page (`/hr/org-chart`) — part of `OrgChartPage`, not a standalone page.
- **Layout**: A department select/dropdown above the tree; selecting a department prunes the tree to that department's subtree.
- **Key interactions**: Pick a department to scope the tree; clear to return to the full company tree.
- **States**: empty = no departments → filter hidden/disabled; loading = options loading; error = n/a; selected = active department chip shown, tree pruned to that department.
- **Gating**: visible with `hr.org.view` *(assumed permission prefix `hr.org`)*.

## Data

- Owns / writes: none (view-only module)
- Reads: `hr_employees` (self-referential `manager_id` for hierarchy) + `hr_departments`, both owned by [[../../employee-profiles/_module|hr.profiles]], read via `EmployeeService` / `OrgChartService`.
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none
- Feeds: none
- Shared entity: reads `hr_departments` (owned by hr.profiles).

## Related

- Tables (read): `hr_departments`, `hr_employees`
- Permissions: `hr.org.view`
- [[../_module]]
