---
tags: [flowflex, domain/projects, okr, goals, phase/8]
domain: Projects & Work
panel: projects
color: "#4F46E5"
status: planned
last_updated: 2026-05-08
---

# OKR & Goal Management

Objectives and Key Results that connect company strategy to team work. Every project, task, and KPI traces back to a business goal. Replace Lattice, Perdoo, and Weekdone with something already inside your platform.

**Who uses it:** Leadership, managers, all employees
**Filament Panel:** `projects`
**Depends on:** [[Task Management]], [[Analytics Overview]], [[Employee Profiles]]
**Phase:** 8
**Build complexity:** Medium — 3 resources, 2 pages, 5 tables

---

## Features

### Objective Hierarchy

- Company OKRs (set by leadership)
  - Department OKRs (aligned to company objectives)
    - Team OKRs (aligned to department)
      - Individual OKRs (aligned to team)
- Visual cascade: see how your personal goals connect to company strategy
- Alignment score: % of department OKRs linked to company OKR

### Objectives

- Title, description, owner (person or department)
- Time period: quarterly (default), annual, custom
- Visibility: public / team / private
- Status: On Track / At Risk / Behind / Completed
- Parent objective link (alignment)
- Up to 5 key results per objective

### Key Results

- Description of measurable outcome
- Metric type: number / percentage / binary (yes/no) / milestone list
- Start value, target value, current value
- Auto-update option: link to a FlowFlex metric (e.g. "MRR from Finance", "Deals closed from CRM", "Tasks completed from Projects")
- Confidence score (1–10 slider, updated by owner weekly)
- Progress bar and status badge

### Check-ins

- Weekly check-in prompt (email/in-app reminder on Monday)
- Update current value + confidence + comment
- Check-in history visible (timeline of updates)
- Manager can comment on check-ins
- Missed check-in alert after 2 weeks

### OKR Views

- Company tree view (full cascade, zoom/pan)
- My OKRs (personal dashboard)
- Team OKRs (manager's direct reports)
- Department board (grid of OKRs with status colour coding)
- Timeline view (OKRs across periods, like Gantt)

### OKR Scoring & Retrospectives

- End-of-period grading: 0.0–1.0 per key result, average = objective score
- Scoring guide: 0.7 = great (ambitious), 1.0 may mean not ambitious enough
- Retrospective notes: what worked, what didn't, what to carry forward
- Score history: track improvement over time

### Integration with Tasks

- Link tasks to key results
- Task completion contributes to key result progress
- "Tasks for this OKR" filter in task views

### Integration with Performance Reviews

- OKR scores auto-populated in [[Performance & Reviews]] review form
- Manager can see direct report's OKR achievement when writing review
- OKR trend shown in employee profile

---

## Database Tables (5)

### `okr_periods`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. `2026-Q3` |
| `type` | enum | `quarterly`, `annual`, `custom` |
| `starts_at` | date | |
| `ends_at` | date | |
| `is_active` | boolean | |
| `checkin_frequency_days` | integer default 7 | |

### `okr_objectives`
| Column | Type | Notes |
|---|---|---|
| `period_id` | ulid FK | |
| `parent_objective_id` | ulid FK nullable | alignment |
| `owner_type` | enum | `company`, `department`, `team`, `employee` |
| `owner_id` | ulid FK | polymorphic |
| `title` | string | |
| `description` | text nullable | |
| `visibility` | enum | `public`, `team`, `private` |
| `status` | enum | `on_track`, `at_risk`, `behind`, `completed`, `cancelled` |
| `final_score` | decimal nullable | 0.0–1.0 |

### `okr_key_results`
| Column | Type | Notes |
|---|---|---|
| `objective_id` | ulid FK | |
| `title` | string | |
| `metric_type` | enum | `number`, `percentage`, `binary`, `milestone` |
| `start_value` | decimal | |
| `target_value` | decimal | |
| `current_value` | decimal | |
| `auto_metric_key` | string nullable | FlowFlex metric to auto-pull |
| `confidence_score` | integer nullable | 1-10 |
| `sort_order` | integer | |

### `okr_checkins`
| Column | Type | Notes |
|---|---|---|
| `key_result_id` | ulid FK | |
| `author_id` | ulid FK | → tenants |
| `value` | decimal | new current value |
| `confidence` | integer | 1-10 |
| `comment` | text nullable | |
| `checked_in_at` | timestamp | |

### `okr_task_links`
| Column | Type | Notes |
|---|---|---|
| `key_result_id` | ulid FK | |
| `task_id` | ulid FK | |
| `linked_at` | timestamp | |
| `linked_by` | ulid FK | |

---

## Permissions

```
projects.okr.view-company
projects.okr.view-department
projects.okr.view-own
projects.okr.create
projects.okr.edit-own
projects.okr.edit-any
projects.okr.checkin
projects.okr.grade
```

---

## Competitor Comparison

| Feature | FlowFlex | Lattice | Perdoo | Weekdone | Asana Goals |
|---|---|---|---|---|---|
| Company → individual cascade | ✅ | ✅ | ✅ | ✅ | ✅ |
| Auto-update from platform data | ✅ | ❌ | ❌ | ❌ | ✅ |
| Linked to task completion | ✅ | ❌ | ❌ | ❌ | ✅ |
| Integrated with performance reviews | ✅ | ✅ | ❌ | ❌ | ❌ |
| No extra subscription | ✅ | ❌ (€11/user/mo) | ❌ | ❌ | ❌ (Asana Premium) |

---

## Related

- [[Projects Overview]]
- [[Task Management]]
- [[Performance & Reviews]]
- [[KPI & Goal Tracking]]
