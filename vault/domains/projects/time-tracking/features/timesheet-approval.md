---
domain: projects
module: time-tracking
feature: timesheet-approval
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Timesheet & Approval

Weekly timesheet grid and manager week-level approval.

## Behaviour

- Weekly grid: all of a user's entries for the week with per-user totals.
- Approve a week (`ApproveWeekAction`): stamps every entry in the week; approver ≠ owner.

## UI

- **Kind**: custom-page (report — weekly grid).
- **Page**: `TimesheetPage` at `/app/projects/time/timesheet` (nav group Time).
- **Layout**: grid users × days (or one user × days for self); week navigator; row/column totals; approve-week action.
- **Key interactions**: navigate weeks; edit a cell (opens entry); approve week → confirm → all entries stamped.
- **States**: empty (no time this week) · loading (grid skeleton) · error (self-approval blocked → toast) · selected (week highlighted).
- **Gating**: view own with `log-own`; team grid + approve requires `projects.time.approve`.

## Data

- Owns / writes: `proj_time_entries` (`approved_by`, `approved_at`).
- Reads: entries for the week/user.
- Cross-domain writes: none.

## Relations

- Consumes / Feeds: nothing.
- Shared entity: `users`.

## Unknowns

- Day-level vs week-level approval granularity *(assumed week)*. See [[../unknowns]].

## Related

- [[../_module|Time Tracking]] · [[time-entry-timer|Entry & Timer]] · [[time-report-export|Report & Export]]
