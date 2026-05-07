---
tags: [flowflex, domain/analytics, kpi, goals, phase/6]
domain: Analytics
panel: analytics
color: "#9333EA"
status: planned
last_updated: 2026-05-07
---

# KPI & Goal Tracking

Define company KPIs and cascade them down to teams and individuals. Traffic-light status keeps everyone aligned and alerts fire before targets are missed.

**Who uses it:** Leadership, managers, all employees
**Filament Panel:** `analytics`
**Depends on:** Core
**Phase:** 6
**Build complexity:** Medium — 3 resources, 1 page, 3 tables

---

## Features

- **KPI definition** — create KPIs with name, description, formula, unit of measurement, target value, and direction (higher_better / lower_better)
- **Check-in cadence** — each KPI has a frequency (daily/weekly/monthly/quarterly); tenants log a value for each period
- **Traffic-light status** — computed automatically per check-in: on_track (green), at_risk (amber), off_track (red) based on % deviation from target
- **KPI owner assignment** — assign a tenant as owner; owner is notified when KPI goes off-track
- **`KPIOffTrack` alert** — event fired when a check-in is saved with status `off_track`; notifies KPI owner and their manager
- **Goal setting** — link goals to a KPI; goals have a target value, date range, and owner; status: active/achieved/missed
- **Team goals** — goals can be assigned to a team (team_id) rather than an individual
- **Trend charts** — line chart of KPI value over time with target line; visualised in the analytics panel and embeddable in custom dashboards
- **Benchmark comparisons** — compare current period vs prior period and vs target; delta shown as percentage
- **KPI cascade view** — tree view showing company KPI → department KPI → individual goal hierarchy
- **Check-in notes** — each check-in includes a notes field for context on the period's result
- **Historical check-in log** — full history of all check-ins per KPI; useful for trend analysis and accountability

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `kpis`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text nullable | |
| `formula` | string nullable | e.g. "Closed deals / Total leads" |
| `unit` | string nullable | e.g. "£", "%", "count" |
| `target` | decimal(15,4) | |
| `direction` | enum | `higher_better`, `lower_better` |
| `frequency` | enum | `daily`, `weekly`, `monthly`, `quarterly` |
| `owner_id` | ulid FK nullable | → tenants |
| `is_active` | boolean default true | |
| `at_risk_threshold_pct` | integer default 10 | % below target = at_risk |
| `off_track_threshold_pct` | integer default 25 | % below target = off_track |

### `kpi_check_ins`
| Column | Type | Notes |
|---|---|---|
| `kpi_id` | ulid FK | → kpis |
| `value` | decimal(15,4) | |
| `period_start` | date | |
| `period_end` | date | |
| `tenant_id` | ulid FK | who logged → tenants |
| `notes` | text nullable | |
| `status` | enum | `on_track`, `at_risk`, `off_track` |

### `goals`
| Column | Type | Notes |
|---|---|---|
| `kpi_id` | ulid FK nullable | → kpis |
| `name` | string | |
| `target_value` | decimal(15,4) | |
| `start_date` | date | |
| `end_date` | date | |
| `owner_id` | ulid FK nullable | → tenants (individual goal) |
| `team_id` | ulid FK nullable | → teams (team goal) |
| `status` | enum | `active`, `achieved`, `missed` |
| `notes` | text nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `KPIOffTrack` | `kpi_id`, `check_in_id`, `owner_id` | Notification to KPI owner |

---

## Events Consumed

None — KPIs are manually checked in by owners or automated via scheduled jobs.

---

## Permissions

```
analytics.kpis.view
analytics.kpis.create
analytics.kpis.edit
analytics.kpis.delete
analytics.kpi-check-ins.view
analytics.kpi-check-ins.create
analytics.kpi-check-ins.edit
analytics.goals.view
analytics.goals.create
analytics.goals.edit
analytics.goals.delete
```

---

## Related

- [[Analytics Overview]]
- [[Custom Dashboards]]
- [[Performance & Reviews]]
