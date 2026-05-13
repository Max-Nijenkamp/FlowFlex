---
type: module
domain: Professional Services Automation
panel: psa
cssclasses: domain-psa
phase: 7
status: complete
migration_range: 874000–875999
last_updated: 2026-05-12
---

# Utilisation & Capacity Tracking

Track billable vs non-billable hours per person. Monitor utilisation rates against targets. Identify bench time, over-allocation, and capacity for new engagements.

---

## Core Concepts

### Utilisation Rate
```
Utilisation % = Billable hours logged / Available hours × 100
```

**Available hours** = contracted hours - public holidays - approved leave  
Excludes: sick leave (usually excluded from denominator to avoid penalising absence)

### Target Utilisation
- Per role: e.g., Senior Consultant = 75%, Director = 60%, Junior = 80%
- Per employee override (part-time staff)
- Company-wide target (e.g., 72% overall)

### Billable Classification
Time entries (from Projects module) are classified as:
- **Billable** — charged to client, counts toward utilisation
- **Non-billable client** — client work, not invoiced (e.g., pitch, relationship management)
- **Internal** — investment in own business (training, sales, admin, R&D)
- **Bench** — no assignment, available for work

---

## Utilisation Dashboard

### Per-Person View
- This week: billable hours / target hours → % utilisation with RAG badge
- Last 4 weeks trend (sparkline)
- Billable breakdown: which engagements and hours each
- Upcoming capacity: calendar view of assigned vs unassigned future weeks
- Bench flag: if person has < 20% billable in forward 2 weeks → flag as "on bench"

### Team View
- Table: all people, current utilisation%, billable this month, target, variance
- Sort by: name, utilisation%, bench flag
- Filter: by practice/team, seniority level

### Company Overview
- Average utilisation rate (company-wide)
- Total bench hours (billable capacity being wasted)
- Revenue at risk: bench hours × average hourly rate
- Trend: 12-month rolling average utilisation

---

## Capacity Planner

Forward-looking view: shows demand (confirmed + probable engagements) vs supply (available people) by week over next 12 weeks.

- Demand: sum of planned hours per role from active and confirmed engagements in [[resource-scheduling-psa]]
- Supply: available hours per person per week (contracted - leave - public holidays)
- Gap: shortfall by role → signals hiring need or contractor use

---

## Data Model

### `psa_utilisation_snapshots`
Nightly computed, one row per employee per week:

| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| employee_id | ulid | FK |
| week_start | date | Monday |
| available_hours | decimal(5,2) | |
| billable_hours | decimal(5,2) | |
| non_billable_client_hours | decimal(5,2) | |
| internal_hours | decimal(5,2) | |
| bench_hours | decimal(5,2) | |
| utilisation_pct | decimal(5,2) | billable/available × 100 |
| target_pct | decimal(5,2) | from role default |

---

## Integrations

- **Projects** — reads time entries from `project_time_entries` table; billable flag per entry
- **HR** — contracted hours, approved leave, public holidays calendar
- **Finance** — billable hours × blended rate → revenue forecast

---

## Migration

```
874000_create_psa_utilisation_snapshots_table
874001_create_psa_utilisation_targets_table
```

---

## Related

- [[MOC_PSA]]
- [[resource-scheduling-psa]]
- [[project-profitability]]
- [[MOC_Projects]] — time entry source
- [[MOC_HR]] — leave calendar
