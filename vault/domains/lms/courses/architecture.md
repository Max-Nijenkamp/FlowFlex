---
domain: lms
module: courses
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Courses — Architecture

## Status Lifecycle

Plain string field (no `spatie/laravel-model-states` machine specified in source *(assumed)*):

```
draft → published → archived
```

- `published` requires ≥ 1 lesson *(assumed)* — enforced by `CourseService::publish`.
- `archived` hides the course from new enrolments; existing enrolments continue.

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\LMS\CourseService` | service | `publish()` (guards ≥ 1 lesson), `prerequisitesMet(learnerId, courseId): bool` (called by enrolments). |

### publish flow

1. Load course; guard `status = draft`.
2. Assert the course has ≥ 1 lesson across its modules *(assumed)*; else `ValidationException`.
3. Assert prerequisites contain no cycle (checked at write, re-checked here).
4. Set `status = published`.

### prerequisitesMet

- Walks the course's `prerequisites` (course ids); returns `true` only when the learner has a `completed` enrolment for every prerequisite course. Enrolments module calls this at enrol time — a read-only query, no writes.

## Events

None fired or consumed. Completion side effects (certificate, skill raise, path advance) are same-domain direct calls from `EnrolmentService` — the v1 `CourseCompleted` event was dropped ([[../_index]]).

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `CourseResource` | Courses | #1 CRUD resource | Structure relation (modules), publish action, status + category filters. |
| `CourseBuilderPage` | Courses | #3-style custom page | Drag-drop module/lesson ordering. |

Learner-facing course pages are served by the [[../enrolments/_module\|enrolments]] Vue+Inertia learner portal, not here.

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.courses.view-any')
        && BillingService::hasModule('lms.courses');
}
```

See [[../../../architecture/filament-patterns]] · [[../../../architecture/ui-strategy]].

## Jobs & Scheduling

None.

## Search & Realtime

Course slugs via `spatie/laravel-sluggable`. No realtime.
