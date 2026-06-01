---
type: module
domain: HR & People
panel: hr
module-key: hr.org
status: planned
color: "#4ADE80"
---

# Org Chart

Visual interactive org chart driven by the manager hierarchy on employee profiles. Supports tree-select for reassigning managers and department head assignment.

---

## Core Features

- Auto-generated from `hr_employees.manager_id` hierarchy — no separate data entry
- Interactive tree: expand/collapse nodes, click to view employee profile
- Filterable by department
- Search: highlight employee in tree on search
- Download as PNG/PDF
- Manager reassignment via tree-select field (`codewithdennis/filament-select-tree`)
- Handles multi-root (companies with multiple top-level managers)

---

## Data Model

No additional tables — reads from `hr_employees` (manager_id FK) and `hr_departments`.

---

## Filament

**Nav group:** Employees

- `OrgChartPage` (custom Filament page) — rendered with a Vue component embedded in the Blade view via Alpine.js or Livewire
- Department filter in page header

---

## Related

- [[domains/hr/employee-profiles]]
- [[architecture/patterns/custom-pages]]
- [[architecture/packages]] (`codewithdennis/filament-select-tree`)
