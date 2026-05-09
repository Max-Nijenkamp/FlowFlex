---
type: module
domain: HR & People Management
panel: hr
phase: 3
status: planned
cssclasses: domain-hr
migration_range: 108000–108499
last_updated: 2026-05-09
---

# Performance Reviews & 360° Feedback

Structured performance cycles: self-assessment, manager review, 360° peer feedback, calibration, and outcome tracking. Links performance to compensation decisions.

---

## Review Types

| Type | Frequency | Scope |
|---|---|---|
| Annual performance review | Yearly | Self + manager |
| Mid-year check-in | 6-monthly | Manager feedback only |
| 360° feedback | Annual | Self + manager + peers + reports |
| Probation review | At 3 months | Manager only |
| Project retrospective | Post-project | Team members |

Configurable per role level.

---

## Review Cycle

```
HR opens cycle → Employees complete self-assessment
→ 360° nominates peers (manager approves nominees)
→ Peers submit feedback (anonymous)
→ Manager drafts review (reads 360° + self-assessment)
→ Calibration session (managers normalise ratings)
→ Manager delivers review to employee
→ Employee acknowledges
→ Review finalised + linked to compensation decision
```

---

## Rating Framework

Configurable rating scale (e.g., 5-point or 3-point):
- 5 = Exceptional
- 4 = Exceeds expectations
- 3 = Meets expectations
- 2 = Partially meets
- 1 = Does not meet

Dimensions:
- Delivery/results
- Collaboration
- Growth/learning
- Leadership (for managers)
- Culture/values fit

---

## Calibration

Manager calibration session:
- Distribution view: how are ratings distributed across team?
- Identify rating inflation/deflation across managers
- Adjustment capability: manager changes rating with justification
- Forced distribution option: e.g., maximum 15% "Exceptional"

---

## Goal Setting & OKRs

Employees set goals at start of cycle (quarterly or annual):
- Company OKRs cascade down to department → individual
- Progress tracked throughout cycle
- Performance rating informed by goal completion %

---

## Data Model

### `hr_review_cycles`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | "2026 Annual Review" |
| type | enum | annual/mid_year/360/probation |
| status | enum | draft/active/calibration/complete |
| opens_at | date | |
| closes_at | date | |

### `hr_review_submissions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| cycle_id | ulid | FK |
| reviewee_id | ulid | FK employee |
| reviewer_id | ulid | FK employee |
| reviewer_type | enum | self/manager/peer/report |
| overall_rating | tinyint | nullable |
| ratings_json | json | dimension ratings |
| comments | text | nullable |
| submitted_at | timestamp | nullable |

---

## Migration

```
108000_create_hr_review_cycles_table
108001_create_hr_review_submissions_table
108002_create_hr_review_goals_table
```

---

## Related

- [[MOC_HR]]
- [[compensation-benefits]]
- [[org-chart-workforce-planning]]
- [[employee-self-service-portal]]
