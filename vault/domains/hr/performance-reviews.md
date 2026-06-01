---
type: module
domain: HR & People
panel: hr
module-key: hr.performance
status: planned
color: "#4ADE80"
---

# Performance Reviews

Structured review cycles with 360 feedback, self-assessments, goal tracking, and rating calibration. Replaces manual review processes done in spreadsheets or email.

---

## Core Features

- Review cycles: configurable frequency (annual, bi-annual, quarterly)
- Review types: self-assessment, manager review, peer review (360)
- Goal tracking: SMART goals linked to review; progress updated by employee
- Rating scale: configurable 1–5 or custom labels per company
- Calibration: HR can adjust ratings before finalising the cycle
- Review report: per-employee PDF of the cycle outcome
- Notifications to employees and managers when review is due

---

## Data Model

| Table | Key Columns |
|---|---|
| `hr_review_cycles` | company_id, name, period_start, period_end, type, status |
| `hr_reviews` | company_id, cycle_id, employee_id, reviewer_id, type (self/manager/peer), status, submitted_at |
| `hr_review_goals` | company_id, review_id, employee_id, title, description, progress_percent, rating |

---

## Filament

- `ReviewCycleResource` — create/manage cycles, track completion %
- `ReviewResource` — list all reviews in a cycle; edit form for manager reviews

---

## Related

- [[domains/hr/employee-profiles]]
- [[domains/hr/employee-feedback]]
