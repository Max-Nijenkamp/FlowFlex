---
type: module
domain: Professional Services Automation
panel: psa
cssclasses: domain-psa
phase: 7
status: complete
migration_range: 881000–883999
last_updated: 2026-05-12
---

# Resource Scheduling (PSA)

Cross-project resource demand planning. Assign the right people to the right engagements. Visualise forward capacity, identify conflicts, and match open roles to available staff.

---

## Core Concepts

### Resource vs Task Scheduling
The Projects domain handles task assignment within a project. PSA Resource Scheduling operates at the engagement/capacity level:
- "We need a Senior Front-End Developer for 3 days/week from June to September on Engagement X"
- Not: "Assign Alice to task #142"

### Resource Demand
Each active or confirmed engagement has a **resource demand plan**:
- Role required (e.g., UX Designer, Backend Engineer, Project Manager)
- Hours per week required
- Start and end date
- Must-have skills (from employee skills registry)
- Preferred specific person (optional)

### Allocation
Allocations link a specific employee to a demand slot:
- Employee → Engagement → Hours/week → Date range
- Can have partial allocation: employee at 50% on Engagement A, 50% on Engagement B
- Over-allocation warning: sum of allocations > contracted hours for that week

---

## Scheduling Views

### Gantt / Timeline View
Horizontal timeline, one row per employee:
- Coloured bars per engagement allocation
- White gaps = available capacity
- Red bars = over-allocated periods
- Bench indicator: grey bar when no allocation

Filter by: team, role, skill, engagement

### Role Demand Heatmap
Shows aggregate demand by role per week for next 12 weeks:
- Required: hours of that role needed
- Available: hours of people in that role available
- Gap: shortfall (negative = need to hire or subcontract)

### Open Roles
Demand slots with no assigned person yet:
- Required role + skills
- Engagement name + client
- Start date urgency
- "Find Match" — AI suggestion of best-fit available employees (by skill score + availability)

---

## Skills Registry

Brief employee skills catalogue (PSA-specific layer on top of HR):
- Skills: list of technologies, methodologies, domain expertise
- Proficiency: Basic / Practitioner / Expert
- Certifications (e.g., AWS Solutions Architect, PMP)

Used for "Find Match" and for proposal stage ("do we have the skills to take this engagement?").

---

## Data Model

### `psa_resource_demands`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| engagement_id | ulid | FK |
| role_title | varchar(100) | "Senior Backend Engineer" |
| required_skills | json | |
| hours_per_week | decimal(5,2) | |
| start_date | date | |
| end_date | date | |
| status | enum | open/partially_filled/filled |

### `psa_resource_allocations`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| demand_id | ulid | FK |
| employee_id | ulid | FK |
| hours_per_week | decimal(5,2) | |
| start_date | date | |
| end_date | date | |
| confirmed | bool | false = tentative |

---

## Business Rules

- Over-allocation threshold configurable (default: 100% of contracted hours)
- Tentative allocation: used during proposal stage before engagement confirmed; doesn't count as hard-allocated for utilisation
- Allocation changes trigger notification to project manager and employee

---

## Integrations

- **Projects** — demand informs project task assignment; allocations visible in project team view
- **HR** — contracted hours, approved leave (reduces available capacity)
- **Utilisation module** — allocations = scheduled billable time; actuals = logged time entries

---

## Migration

```
881000_create_psa_resource_demands_table
881001_create_psa_resource_allocations_table
881002_create_psa_employee_skills_table
```

---

## Related

- [[MOC_PSA]]
- [[utilisation-capacity-tracking]]
- [[client-engagement-management]]
- [[project-profitability]]
- [[MOC_Projects]] — task-level assignment
- [[MOC_HR]] — leave calendar, contracted hours
