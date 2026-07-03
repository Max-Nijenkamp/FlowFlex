---
domain: hr
module: org-chart
feature: org-tree-visualization
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Org Tree Visualization

## Purpose

Render the manager hierarchy as an interactive, auto-generated tree on `OrgChartPage`.

## Intended Behavior

- Auto-generated from `hr_employees.manager_id` — no separate data entry.
- Expand/collapse nodes; click a node to view the employee profile.
- Avatar cards with vertical connector lines and direct-report badges, wrapped in a Section showing headcount.
- Handles multi-root companies (multiple top-level managers render side by side).
- Search highlights the matching employee in the tree.

## UI

- **Kind**: custom-page
- **Page**: "Org Chart" (`/hr/org-chart`)
- **Layout**: Full-width interactive tree of employee nodes (photo, name, title) built from `hr_employees.manager_id`; expand/collapse subtrees; click a node opens the employee profile. Multi-root companies render all top-level managers side by side.
- **Key interactions**: Expand/collapse nodes, click node → open employee profile, search to highlight a node.
- **States**: empty = "No employees to chart yet"; loading = tree skeleton / spinner while the single hierarchy query resolves; error = "Could not build chart"; selected = clicked node highlighted with a profile link.
- **Gating**: visible with `hr.org.view` *(assumed permission prefix `hr.org`)*.

## Data

- Owns / writes: none (view-only module)
- Reads: `hr_employees` (self-referential `manager_id` for hierarchy, plus `name`, `title`, `photo`) + `hr_departments`, both owned by [[../../employee-profiles/_module|hr.profiles]], read via `EmployeeService` / `OrgChartService`.
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]).

## Relations

- Consumes: `EmployeeHired` / `EmployeeOffboarded` from `hr.profiles` → tree reflects new/removed nodes (no local write; read-through).
  > [!warning] UNVERIFIED
  > May simply re-query live rows on each render rather than react to these events. No confirmed event subscription. *(assumed)*
- Feeds: none
- Shared entity: reads `hr_employees`, `hr_departments` (owned by hr.profiles).

## Test Checklist

### Unit
- [ ] `OrgChartService::tree` assembles nodes by `manager_id` and renders all roots for a multi-root company
- [ ] Tree is built from a single query + in-memory assembly (no recursive N+1)

### Feature (Pest)
- [ ] Tree build issues one hierarchy query (N+1 / query-count assertion)
- [ ] Multi-root company renders every top-level manager
- [ ] Tenant isolation: the tree contains only the acting company's employees

### Livewire
- [ ] `OrgChartPage` `canAccess()` denies without `hr.org.view` or when `hr.org` inactive
- [ ] Search highlights the matching node; click navigates to the employee profile

## Related

- Tables (read): `hr_employees`, `hr_departments`
- Permissions: `hr.org.view`
- [[../_module]]
