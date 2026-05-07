---
tags: [flowflex, domain/analytics, velocity, metrics, phase/6]
domain: Analytics
panel: analytics
color: "#9333EA"
status: planned
last_updated: 2026-05-07
---

# Team Velocity & Ops Metrics

Operational performance metrics. Cycle times, throughput, and early burnout signals — computed automatically from task and time-tracking data.

**Who uses it:** Engineering managers, operations managers, HR, team leads
**Filament Panel:** `analytics`
**Depends on:** [[Task Management]], [[Timesheets & Time Tracking]], [[HR — Employee Profiles]]
**Phase:** 6
**Build complexity:** Medium — 2 resources, 2 pages, 2 tables

---

## Features

- **Velocity snapshots** — weekly snapshot per team: tasks completed, story points, cycle time average, throughput; computed from Projects task data
- **Cycle time tracking** — time from task `in_progress` to `completed`; median and 85th percentile per team per period
- **Throughput charts** — tasks and story points completed per week; bar chart with trend line; compare against prior periods
- **Bottleneck detection** — identify stages where work queues up; highlight tasks stuck in a status for more than a configurable threshold
- **Predictive delivery estimates** — extrapolate current velocity to estimate project completion date; shown on project detail page
- **Cross-team comparison** — normalised performance table comparing all teams; filter by department
- **`BurnoutSignalDetected` event** — if a `burnout_score` (computed from overtime hours, task rejection rate, and leave flags) exceeds threshold, notify HR manager
- **Ops metrics store** — generic `ops_metrics` table for recording any key/value operational metric (e.g. "tickets_resolved_today", "field_jobs_completed_this_week") that doesn't fit a specific domain model
- **Custom metric definitions** — ops managers define the `metric_key` and `dimension` combinations they want to track; values written by domain events or manual entry
- **Dashboard integration** — velocity snapshots and ops metrics are queryable as data sources in Custom Dashboards widgets

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `velocity_snapshots`
| Column | Type | Notes |
|---|---|---|
| `team_id` | ulid FK | → teams |
| `period_start` | date | |
| `period_end` | date | |
| `tasks_completed` | integer default 0 | |
| `story_points` | integer default 0 | |
| `cycle_time_avg` | decimal(8,2) nullable | hours |
| `cycle_time_p85` | decimal(8,2) nullable | 85th percentile hours |
| `throughput` | decimal(8,2) nullable | tasks per day |
| `burnout_score` | decimal(5,2) nullable | 0-100 computed score |

### `ops_metrics`
| Column | Type | Notes |
|---|---|---|
| `metric_key` | string | e.g. "pos_transactions_today" |
| `value` | decimal(15,4) | |
| `dimension` | string nullable | e.g. team name, location |
| `period` | date | |
| `recorded_at` | timestamp | |
| `source` | string nullable | which job/event wrote this |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `BurnoutSignalDetected` | `team_id`, `tenant_id`, `burnout_score` | Notification to HR manager |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `TaskCompleted` | [[Task Management]] | Updates velocity snapshot calculations for the owning team |

---

## Permissions

```
analytics.velocity-snapshots.view
analytics.ops-metrics.view
analytics.ops-metrics.create
analytics.burnout-signals.view
```

---

## Related

- [[Analytics Overview]]
- [[Task Management]]
- [[Agile & Sprint Management]]
- [[HR — Employee Profiles]]
- [[Employee Feedback]]
