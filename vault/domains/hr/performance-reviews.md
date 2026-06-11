---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.performance
status: complete
priority: v1
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [hr.feedback]
fires-events: []
consumes-events: []
patterns: [states, pdf]
tables: [hr_review_cycles, hr_reviews, hr_review_goals]
permission-prefix: hr.performance
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Performance Reviews

Structured review cycles with 360 feedback, self-assessments, goal tracking, and rating calibration. Replaces manual review processes done in spreadsheets or email.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | reviewees/reviewers are employees; manager chain routes reviews |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, due reminders |
| Soft | [[domains/hr/employee-feedback\|hr.feedback]] | continuous feedback surfaces in review context; absent = reviews stand alone |

---

## Core Features

- Review cycles: configurable frequency (annual, bi-annual, quarterly)
- Review types: self-assessment, manager review, peer review (360)
- Goal tracking: SMART goals linked to review; progress updated by employee
- Rating scale: configurable 1–5 or custom labels per company
- Calibration: HR can adjust ratings before finalising the cycle
- Review report: per-employee PDF of the cycle outcome (spatie/laravel-pdf)
- Notifications to employees and managers when review is due
- Visibility: employee sees own reviews after cycle finalised; manager sees reports' reviews; HR sees all *(assumed)*

---

## Data Model

### hr_review_cycles

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| period_start / period_end | date | |
| type | string | annual / bi-annual / quarterly |
| rating_scale | jsonb | labels, default 1–5 *(assumed)* |
| status | string default `draft` | state machine |
| deleted_at | timestamp nullable | |

### hr_reviews

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), cycle_id FK, employee_id FK, reviewer_id FK | ulid | unique `(cycle_id, employee_id, reviewer_id, type)` |
| type | string | self / manager / peer |
| status | string default `pending` | pending / submitted |
| rating | decimal(3,1) nullable | calibratable |
| content | jsonb | answers per question *(assumed: question set on cycle)* |
| submitted_at | timestamp nullable | |

### hr_review_goals

| Column | Type | Notes |
|---|---|---|
| id, company_id, review_id FK, employee_id FK | ulid | |
| title / description | string / text | |
| progress_percent | int default 0 | 0–100 |
| rating | decimal(3,1) nullable | |

---

## State Machine

Column: `hr_review_cycles.status` — `ReviewCycleState`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `active` | `hr.performance.manage-cycles` | review rows generated (self + manager per employee; peers chosen by manager *(assumed)*); due notifications |
| `active` | `calibration` | `hr.performance.manage-cycles` | submissions locked |
| `calibration` | `finalised` | `hr.performance.calibrate` | ratings frozen; PDFs generated; employees see results |

---

## DTOs

### CreateCycleData — name (required), period_start/end (end after start), type (in set), rating_scale (optional labels array)
### SubmitReviewData — review_id, rating (in scale), content (per question answers); only own assigned review, only while cycle active
### CalibrateRatingData — review_id, rating, note (required on change *(assumed)*)

## Services & Actions

Interface→Service: `PerformanceServiceInterface` → `PerformanceService`.

- `activateCycle(string $cycleId): void` — generates review matrix; throws `EmptyCycleException` when no active employees
- `submitReview(SubmitReviewData $data): void` — throws `ReviewLockedException` outside active state, `NotYourReviewException`
- `calibrate(CalibrateRatingData $data): void` — calibration state only, audited
- `finalise(string $cycleId): void` — dispatches per-employee PDF jobs

---

## Filament

**Nav group:** Performance

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ReviewCycleResource` | #1 CRUD resource | completion % column; activate/calibrate/finalise actions |
| `ReviewResource` | #1 CRUD resource | own pending reviews list + submit form; HR sees all |
| `MyGoalsPage` | #7 custom page | employee goal progress updates *(assumed: lives with self-service nav)* |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('hr.performance.view-any') && BillingService::hasModule('hr.performance')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`hr.performance.view-any` · `hr.performance.view` · `hr.performance.submit` · `hr.performance.manage-cycles` · `hr.performance.calibrate`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ReviewDueReminderCommand` | notifications | daily | pending reviews due in 3d/overdue, once per threshold |
| `GenerateReviewReportPdfJob` | exports | on finalise | overwrites per employee |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Activation generates self + manager review per active employee
- [ ] Reviewer can only submit own assigned reviews
- [ ] Submission blocked outside `active`
- [ ] Calibration only in calibration state, audited with note
- [ ] Employee sees own results only after finalised
- [ ] Peer review visibility: reviewee never sees peer reviewer identity *(assumed)*

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

- [[domains/hr/employee-profiles]]
- [[domains/hr/employee-feedback]]
