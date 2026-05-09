---
type: module
domain: Financial Planning & Analysis
panel: fpa
phase: 4
status: planned
cssclasses: domain-fpa
migration_range: 987000–987499
last_updated: 2026-05-09
---

# Headcount Planning

Manage planned headcount by department and role. Links to HR for actuals. The single source of truth for "how many people do we plan to have, when, and at what cost?"

---

## Why Headcount Planning Matters

Payroll is typically 50–70% of operating costs. Getting headcount timing right is the most impactful lever in financial planning. One hire at the wrong time can shift EBITDA by €50–100k.

---

## Headcount Plan

Finance/HR jointly maintain a headcount plan per fiscal year:
- Open positions: planned start date, department, role, level, location
- Existing employees: salary, benefits, employer NI
- Leavers: planned exit dates (known resignations, restructures)

**Loaded cost** per FTE auto-calculated:
- Base salary
- Employer NI/social contributions (country-specific rates)
- Benefits (pension contribution, health, equity)
- Equipment allowance
- Office / desk cost per head

---

## Hire Plan

For each planned hire:
| Field | Example |
|---|---|
| Role | Senior Backend Engineer |
| Department | Engineering |
| Expected start | 2026-09-01 |
| Salary range | €90,000–105,000 |
| Location | Amsterdam (→ NL NI rate) |
| Approved in budget? | Yes — FY2027 budget |

Hire plan feeds into rolling forecast automatically when confirmed.

---

## Actuals Sync

When employee hired in HR module → headcount plan item marked "filled":
- Actual salary vs plan variance tracked
- Delayed hire → underspend flag → update forecast

---

## Headcount Dashboard

- Budgeted FTEs vs actual FTEs by month and department
- Open roles: days vacant, cost of vacancy
- Payroll forecast vs budget
- Hiring velocity: offers extended, accepted, time-to-start

---

## Data Model

### `fpa_headcount_plans`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| cycle_id | ulid | FK budget cycle |
| department | varchar(100) | |
| role_title | varchar(200) | |
| level | varchar(50) | |
| planned_start_date | date | |
| planned_end_date | date | nullable |
| base_salary | decimal(14,2) | |
| currency | char(3) | |
| employee_id | ulid | nullable FK — filled when hired |
| status | enum | planned/recruiting/filled/cancelled |

---

## Migration

```
987000_create_fpa_headcount_plans_table
987001_create_fpa_headcount_cost_rates_table
```

---

## Related

- [[MOC_FPA]]
- [[annual-budget-builder]]
- [[rolling-forecasts]]
- [[scenario-modeling]]
- [[MOC_HR]] — actuals sync
