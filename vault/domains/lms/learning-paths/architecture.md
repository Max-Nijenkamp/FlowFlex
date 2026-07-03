---
domain: lms
module: learning-paths
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Learning Paths — Architecture

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\LMS\PathService` | service | `enrol(EnrolPathData)` (sequential → first course; parallel → all), `onCourseCompleted(enrolment)` (unlock/enrol next, recompute path progress, issue path certificate at 100%). |

### enrol flow

1. Validate `path_id`; guard no active path enrolment for the learner.
2. Create `lms_path_enrolments` row.
3. **Sequential** path → call `EnrolmentService::enrol` for the **first** course only.
   **Parallel** path → call `EnrolmentService::enrol` for **all** courses.

### onCourseCompleted hook

- Called by `EnrolmentService` when a course enrolment completes.
- If the completed course belongs to an active path enrolment:
  - **Sequential** → enrol the learner in the **next** course (via `EnrolmentService::enrol`).
  - Recompute `progress_percent` (courses completed / total).
  - At 100% → stamp `completed_at`; if the path has a certificate template, call `CertificateService::issue`.

## Events

None. `PathService` is invoked by, and invokes, `EnrolmentService` via same-domain calls.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `LearningPathResource` | Courses | #1 CRUD resource | Ordered course repeater, bulk assign, progress columns. |

Learner path view is rendered by the [[../enrolments/_module\|enrolments]] portal.

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.paths.view-any')
        && BillingService::hasModule('lms.paths');
}
```

## Jobs & Scheduling

None (progress is event-free, driven by the completion hook).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Path `enrol` | Pessimistic | `lockForUpdate` on the learner's path enrolments -- no-active-duplicate guard race-safe |
| `onCourseCompleted` (unlock next / recompute / certify) | Pessimistic | Path enrolment row locked -- raced parallel-course completions recompute progress serially, path certificate issued once at 100% |
| Path CRUD / builder | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

None.
