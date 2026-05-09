---
tags: [flowflex, domain/hr, org-chart, workforce-planning, phase/8]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-08
---

# Org Chart & Workforce Planning

Visual org chart with live headcount data. Workforce planning that shows who you have, who you need, and where the gaps are — before they become problems.

**Who uses it:** HR directors, executives, department heads
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]], [[Recruitment & ATS]]
**Phase:** 8
**Build complexity:** Medium-High — 1 resource, 2 pages, 3 tables

---

## Features

### Visual Org Chart

- Interactive tree view — zoom, pan, search
- Shows: name, title, photo, department, direct reports count
- Colour-coded by department
- Highlight open positions (headcount approved but not filled)
- Export: PNG, PDF, SVG
- Auto-updates when employee records change
- Embed link for intranet / presentations

### Org Chart Views

- **Hierarchical** — traditional top-down tree
- **Flat list** — sortable table view of same data
- **Department view** — group by department with headcount totals
- **Matrix view** — employees with multiple reporting lines (dotted-line relationships)
- **Future state** — plan a future org structure without changing live data

### Headcount Planning

- Approved headcount vs current headcount per department
- Headcount forecast: add planned hires by quarter
- Budget impact: estimated cost of planned headcount (uses salary band data)
- Vacancy tracking: days position has been open

### Role & Position Management

- Define positions (distinct from employees — "2 open Senior Engineer positions")
- Position attributes: department, team, level, salary band
- Approved vs filled positions
- Succession planning links: "If this person leaves, who's next?"

### Workforce Analytics

- Headcount over time (chart: monthly for past 12 months)
- Attrition rate (rolling 12-month)
- Average tenure by department
- Gender ratio, age distribution (GDPR-compliant aggregate only)
- Department growth rate
- Manager span of control (average direct reports per manager)
- Open role age (average days to fill by department)

---

## Database Tables (3)

### `positions`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `department_id` | ulid FK | |
| `level` | string nullable | e.g. `Senior`, `Lead` |
| `salary_band_min` | integer nullable | |
| `salary_band_max` | integer nullable | |
| `reports_to_position_id` | ulid FK nullable | org hierarchy |
| `approved_count` | integer | approved slots |
| `filled_count` | integer | cached from employees |
| `status` | enum | `active`, `frozen`, `planned` |

### `employee_dotted_lines`
| Column | Type | Notes |
|---|---|---|
| `employee_id` | ulid FK | |
| `manager_id` | ulid FK | → employees |
| `relationship_type` | enum | `functional`, `project`, `mentoring` |

### `workforce_headcount_plans`
| Column | Type | Notes |
|---|---|---|
| `department_id` | ulid FK | |
| `period_quarter` | string | e.g. `2026-Q3` |
| `planned_hires` | integer | |
| `planned_departures` | integer | |
| `budget_allocated` | decimal nullable | |
| `notes` | text nullable | |

---

## Permissions

```
hr.org-chart.view
hr.org-chart.edit-structure
hr.workforce-planning.view
hr.workforce-planning.edit
hr.positions.view
hr.positions.manage
```

---

## Competitor Comparison

| Feature | FlowFlex | BambooHR | Rippling | Personio |
|---|---|---|---|---|
| Visual org chart | ✅ | ✅ | ✅ | ✅ |
| Headcount planning | ✅ | ❌ | ✅ | partial |
| Position-based org | ✅ | ❌ | ✅ | ❌ |
| Future state planning | ✅ | ❌ | partial | ❌ |
| Integrated with ATS vacancies | ✅ | partial | ✅ | ✅ |
| Workforce cost forecasting | ✅ | ❌ | ✅ | ❌ |

---

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Recruitment & ATS]]
- [[Performance & Reviews]]
