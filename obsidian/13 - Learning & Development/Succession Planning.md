---
tags: [flowflex, domain/lms, succession, talent, phase/7]
domain: Learning & Development
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-07
---

# Succession Planning

Know who your successors are before you need them. Identify key-person dependencies and single-point-of-failure risks before they become a business crisis.

**Who uses it:** HR leadership, senior management, executive team
**Filament Panel:** `lms`
**Depends on:** [[HR — Employee Profiles]], [[Performance & Reviews]], [[Skills Matrix & Gap Analysis]]
**Phase:** 7
**Build complexity:** Medium — 3 resources, 2 pages, 3 tables

---

## Features

- **Key role registry** — identify critical roles where loss of the current holder would have high or critical business impact; document the role and its business impact rating
- **Succession candidate pipeline** — for each key role, list potential successors from the employee base; assign readiness rating: ready_now / 1_2_years / 3_5_years
- **9-box talent grid** — position each candidate on the performance × potential matrix; `nine_box_x` (performance, 1-3) and `nine_box_y` (potential, 1-3) axes; candidates plotted on the grid in the UI
- **Key-person dependency flag** — if a key role has zero candidates rated `ready_now`, flag as a single-point-of-failure risk; show in dashboard widget
- **Succession plan reviews** — periodic formal reviews of each plan stored in `succession_reviews`; reviewer notes and timestamp; show when last reviewed
- **Development path linking** — from the succession candidate record, link to a skills development plan and relevant LMS courses targeting readiness gaps
- **`EmployeeTerminated` event handling** — when a tenant in any succession plan is terminated, flag the affected plans as needing urgent review
- **Confidential access** — succession planning data is highly sensitive; only tenants with the `lms.succession-plans.view` permission can access; plans are not visible in standard HR views
- **Export for board** — PDF export of succession plan summary for board or exec team; includes key roles, candidates, readiness, and gap actions

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `succession_plans`
| Column | Type | Notes |
|---|---|---|
| `key_role` | string | e.g. "Head of Engineering" |
| `description` | text nullable | |
| `business_impact` | enum | `critical`, `high`, `medium` |
| `current_holder_id` | ulid FK nullable | → tenants |
| `is_at_risk` | boolean default false | no ready_now candidate |
| `last_reviewed_at` | timestamp nullable | |

### `succession_candidates`
| Column | Type | Notes |
|---|---|---|
| `succession_plan_id` | ulid FK | → succession_plans |
| `tenant_id` | ulid FK | → tenants |
| `readiness` | enum | `ready_now`, `1_2_years`, `3_5_years` |
| `nine_box_x` | integer | performance score 1-3 |
| `nine_box_y` | integer | potential score 1-3 |
| `notes` | text nullable | development notes |
| `development_plan` | text nullable | |

### `succession_reviews`
| Column | Type | Notes |
|---|---|---|
| `succession_plan_id` | ulid FK | → succession_plans |
| `reviewed_by` | ulid FK | → tenants |
| `reviewed_at` | timestamp | |
| `notes` | text nullable | |

---

## Events Fired

None — succession reviews are manual.

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `EmployeeTerminated` | [[HR — Employee Profiles]] | Flag any `succession_plans` or `succession_candidates` that reference the departing tenant as needing urgent review |

---

## Permissions

```
lms.succession-plans.view
lms.succession-plans.create
lms.succession-plans.edit
lms.succession-plans.delete
lms.succession-candidates.view
lms.succession-candidates.create
lms.succession-candidates.edit
lms.succession-candidates.delete
lms.succession-reviews.view
lms.succession-reviews.create
lms.succession-plans.export
```

---

## Related

- [[LMS Overview]]
- [[Skills Matrix & Gap Analysis]]
- [[Performance & Reviews]]
- [[HR — Employee Profiles]]
