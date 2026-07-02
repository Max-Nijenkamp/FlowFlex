---
domain: hr
module: performance-reviews
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Performance Reviews

> [!warning] Rebuild blueprint — not built
> HR domain code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. This spec is the **intended** design for a future rebuild. Nothing here is built, shipped, or tested. `build-status: planned`.

Structured review cycles with 360 feedback, self-assessments, goal tracking, and rating calibration. Intended to replace manual review processes done in spreadsheets or email.

- **module-key:** `hr.performance`
- **panel:** hr — Nav group: Performance
- **priority:** v1
- **tables:** `hr_review_cycles`, `hr_reviews`, `hr_review_goals`
- **patterns:** [[../../../architecture/patterns/states|states]], [[../../../architecture/packages|pdf]]

---

## Intended Behavior

- Configurable review cycles (annual, bi-annual, quarterly).
- Review types: self-assessment, manager review, peer review (360).
- SMART goal tracking linked to a review; progress updated by the employee.
- Configurable rating scale (1–5 or custom labels per company).
- HR calibration before finalising a cycle.
- Per-employee PDF report of the cycle outcome.
- Due-review notifications to employees and managers.
- Visibility rules by role (reviewer / reviewee / manager / HR) — see [[security]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module\|hr.profiles]] | reviewees/reviewers are employees; manager chain routes reviews |
| Hard | core.billing + core.rbac + core.notifications | gating, permissions, due reminders |
| Soft | [[../employee-feedback/_module\|hr.feedback]] | continuous feedback surfaces in review context; absent = reviews stand alone |

---

## Data Ownership

Owns tables `hr_review_cycles`, `hr_reviews`, `hr_review_goals` ([[data-model]]) — all `company_id`-scoped; writes to no other domain's tables (cross-domain only via events — [[../../../security/data-ownership]]).

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Fires | `review-completed` *(assumed — not yet specified)* | hr.feedback | review context surfaces alongside continuous feedback (index: `performance -.feeds.-> feedback`) |
| Reads | (no event) | hr.profiles / org | reviewees/reviewers + manager chain routing |

> [!warning] UNVERIFIED
> A `review-completed` event name/payload is assumed from the index dependency graph, not confirmed in the spec.

---

## Entity Notes

- [[architecture]] — services, actions, review-cycle state machine, PDF export
- [[data-model]] — 3 tables + ERD
- [[api]] — DTOs and service methods
- [[security]] — permissions, visibility, authz, tenancy
- [[unknowns]] — assumptions and open questions

## Features

- [[features/review-cycles]]
- [[features/review-forms-state-machine]]
- [[features/goals-okrs]]
- [[features/self-and-manager-reviews]]
- [[features/pdf-export]]

---

## Build Manifest

```
database/migrations/xxxx_create_hr_review_cycles_table.php
database/migrations/xxxx_create_hr_reviews_table.php
database/migrations/xxxx_create_hr_review_goals_table.php
app/Models/HR/{ReviewCycle,Review,ReviewGoal}.php
app/States/HR/ReviewCycle/{ReviewCycleState,Draft,Active,Calibration,Finalised}.php
app/Data/HR/{CreateCycleData,SubmitReviewData,CalibrateRatingData,ReviewData}.php
app/Contracts/HR/PerformanceServiceInterface.php
app/Services/HR/PerformanceService.php
app/Exceptions/HR/{ReviewLockedException,NotYourReviewException,EmptyCycleException}.php
app/Jobs/HR/GenerateReviewReportPdfJob.php
app/Console/Commands/HR/ReviewDueReminderCommand.php
app/Filament/HR/Resources/{ReviewCycleResource,ReviewResource}.php
app/Filament/HR/Pages/MyGoalsPage.php
database/factories/HR/{ReviewCycleFactory,ReviewFactory,ReviewGoalFactory}.php
tests/Feature/HR/{ReviewCycleTest,ReviewSubmissionTest,CalibrationTest}.php
```

---

## Related

- [[../employee-profiles/_module]]
- [[../employee-feedback/_module]]
- [[../../../glossary]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]
