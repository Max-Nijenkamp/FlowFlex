---
type: module
domain: HR & People Management
panel: hr
phase: 3
status: planned
cssclasses: domain-hr
migration_range: 107500–107999
last_updated: 2026-05-09
---

# Org Chart & Workforce Planning

Live organisational chart drawn from employee data. Workforce planning: headcount by department, span of control, vacancy tracking, and succession planning.

---

## Org Chart

Auto-generated from employee manager relationships:
- Interactive tree view: zoom, click to expand, search by name/role
- Switch view: department / function / location / legal entity
- Show vacant positions (dashed boxes)
- Download as PDF or share link (access-controlled)

Updates in real-time as HR data changes — no manual Visio/Lucidchart maintenance.

---

## Workforce Analytics

Metrics visible on org chart:

| Metric | Definition |
|---|---|
| Headcount | Total active FTEs by department |
| Span of control | Direct reports per manager |
| Manager / IC ratio | Managers as % of total |
| Average tenure | By department |
| Vacancy rate | Open roles / (filled + open) |

Benchmark: typical span of control 6–10 direct reports. Flag managers with >12 or <3.

---

## Workforce Planning

Scenario-based planning for org design:
- "What if we restructure these 3 teams into one?"
- Drag-and-drop employees between departments
- See impact on span of control and headcount cost
- Save as scenario (doesn't change live data until approved)

---

## Succession Planning

For key roles:
- Identify successors: ready now / ready in 1 year / development needed
- Bench strength score per department
- Identify single points of failure (no successor for critical role)

---

## Position Management

Define positions (headcount slots) separately from employees:
- Approved headcount by department
- Filled positions (linked to employee)
- Vacant positions (linked to open job requisition in recruitment)

Position budget links to FP&A headcount plan.

---

## Data Model

### `hr_positions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| title | varchar(200) | |
| department | varchar(100) | |
| level | varchar(50) | |
| manager_position_id | ulid | nullable self-FK |
| employee_id | ulid | nullable FK — null = vacant |
| status | enum | filled/vacant/eliminated |

### `hr_succession_plans`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| position_id | ulid | FK |
| successor_id | ulid | FK employee |
| readiness | enum | ready_now/one_year/development |

---

## Migration

```
107500_create_hr_positions_table
107501_create_hr_succession_plans_table
```

---

## Related

- [[MOC_HR]]
- [[compensation-benefits]]
- [[performance-reviews-360]]
- [[MOC_FPA]] — headcount planning
