---
domain: hr
module: performance-reviews
feature: pdf-export
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — PDF Export

Intended, not built. See [[../_module]].

## Purpose

Per-employee PDF of the cycle outcome, generated when a cycle is finalised.

## Behavior

- On `finalise(cycleId)`, `GenerateReviewReportPdfJob` is dispatched per employee (queue: exports; overwrites the prior file per employee).
- Rendered with spatie/laravel-pdf ([[../../../../architecture/packages]]).
- Also relevant: `ReviewDueReminderCommand` (daily, notifications queue) reminds employees/managers of pending reviews due in 3d / overdue.
- Queue infra: [[../../../../infrastructure/queue-horizon]].

## Tables

Reads `hr_review_cycles`, `hr_reviews`, `hr_review_goals`; writes no new table.

## Permissions

Report visible to the owning employee after finalisation and to HR (`hr.performance.view` / `view-any`); see [[../security]].

## UI

- **Kind**: background (page-action trigger on cycle finalise)
- **Page**: none of its own; the finalise action lives on `ReviewCycleResource` and downloads land on the review/self-service view
- **Layout**: no standalone screen — per-employee PDF generated on `finalise(cycleId)` and offered as a download link on the finalised cycle/review record
- **Key interactions**: HR finalises a cycle (dispatches `GenerateReviewReportPdfJob` per employee, queue `exports`); employees/HR download the resulting PDF
- **States**: empty (cycle not finalised → no report) · loading (generation in flight on `exports` queue) · error (job failure re-runs; overwrites prior file per employee) · selected (single PDF view)
- **Gating**: report visible to the owning employee post-finalisation and to HR with `hr.performance.view` / `view-any`; the finalise trigger requires `hr.performance.calibrate`

## Data

- Owns / writes: none (writes only the PDF file per employee; no new table)
- Reads: `hr_review_cycles`, `hr_reviews`, `hr_review_goals` — own module
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none (triggered internally by cycle finalise)
- Feeds: none — `ReviewDueReminderCommand` sends reminders via core.notifications
- Shared entity: `hr_employees` (hr.profiles) for report recipient

## Test Checklist

### Unit
- [ ] `GenerateReviewReportPdfJob` targets the `exports` queue and overwrites the prior file per employee

### Feature (Pest)
- [ ] Finalising a cycle dispatches exactly one PDF job per employee
- [ ] Report visible to the owning employee post-finalisation and to HR (`view` / `view-any`); not before finalisation
- [ ] `ReviewDueReminderCommand` reminds once per 3d / overdue threshold

Back to [[../_module]].
