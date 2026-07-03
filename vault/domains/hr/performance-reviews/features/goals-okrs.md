---
domain: hr
module: performance-reviews
feature: goals-okrs
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Goals & OKRs

Intended, not built. See [[../_module]].

## Purpose

SMART goals linked to a review, with employee-updated progress.

## Behavior

- Goals attach to a review (`review_id`) and an employee (`employee_id`).
- Each goal has a title/description, `progress_percent` (0–100, default 0), and an optional `rating`.
- Employees update their own goal progress via `MyGoalsPage` (custom page, lives with self-service nav *(assumed)*).

## Tables

`hr_review_goals` (owner).

## Permissions

`hr.performance.view` / `hr.performance.submit` for the owning employee; visibility follows [[../security]].

## UI

- **Kind**: simple-resource (surfaced via the self-service `MyGoalsPage` custom page for progress updates)
- **Page**: "My Goals" (`/hr/my-goals`) *(assumed self-service nav)*; HR views goals within the owning review
- **Layout**: list of the employee's goals (title, description, `progress_percent` slider/bar, optional rating) attached to a review; HR sees goals read-through the review record
- **Key interactions**: employee updates own goal progress (0–100); HR/manager sets ratings during calibration
- **States**: empty (no goals for the review → "No goals set") · loading (list skeleton) · error (progress out of 0–100 range) · selected (goal detail with progress control)
- **Gating**: visible with `hr.performance.view`; updating own progress requires `hr.performance.submit` and ownership (own `employee_id`)

## Data

- Owns / writes: `hr_review_goals`
- Reads: `hr_reviews` (owning `review_id`) — own module
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none — goals stay within HR; a `related_goal_id` link is read by hr.feedback ([[../../employee-feedback/_module|feedback]])
- Shared entity: `hr_employees` (hr.profiles) as goal owner

## Test Checklist

### Unit
- [ ] `progress_percent` constrained to 0–100 (out-of-range rejected)
- [ ] Goal requires both `review_id` and `employee_id`

### Feature (Pest)
- [ ] Employee updates own goal progress; updating another employee's goal denied (ownership)
- [ ] HR/manager sets a `rating` during calibration

### Livewire
- [ ] `MyGoalsPage` denied without `hr.performance.submit` + ownership of the `employee_id`
- [ ] Progress control writes `progress_percent`; invalid value blocked

Back to [[../_module]].
