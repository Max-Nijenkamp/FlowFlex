---
domain: lms
module: enrolments
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Enrolments — Architecture

## State Machine (`spatie/laravel-model-states`)

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `enrolled` | `in_progress` | First lesson activity | `started_at` |
| `in_progress` | `completed` | Progress = 100% | certificate + skills + path advance (soft, direct); notification |
| any | `dropped` | Learner / admin | — |

`completed` is where same-domain completion side effects fire — all direct service calls, no cross-domain event.

## Services & Actions

Interface → Service: `EnrolmentServiceInterface` → `EnrolmentService`.

| Method | Responsibility |
|---|---|
| `enrol(EnrolData)` | Prerequisite check (`CourseService::prerequisitesMet`), duplicate-active guard, create row. |
| `bulkEnrol(BulkEnrolData)` | Per-row try/catch result; rate-limited. |
| `recomputeProgress(enrolmentId)` | Called by `LessonProgressService`; recomputes %; at 100% transitions to `completed` and triggers side effects. |

On `completed`: `CertificateService::issue`, `SkillService::raiseFromCourse`, `PathService::onCourseCompleted` — each a same-domain call into the sibling module's own service (which writes its own tables).

## Listeners

| Listener | Event | Effect |
|---|---|---|
| `AutoEnrolOnHireListener` | `EmployeeHired` (hr.profiles) | Enrols the new employee into mandatory internal-audience courses; idempotent (no-op if none / already enrolled). `implements ShouldQueue` + `WithCompanyContext`. |

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `DueDateReminderCommand` | notifications | daily | `reminded_at` guard, 7d window |

## Learner Portal

- Vue + Inertia `/learn` (ui-strategy row #15): my courses, lesson player, progress.
- External learners authenticate via a **Sanctum scoped portal (learner) guard**; `lms_learners.portal_token` issuance/rotation flows through that guard, not ad-hoc token checks.
- Learner sees **own** enrolments only (token path for external, user link for employees) — the domain's headline isolation test.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `EnrolmentResource` | Enrolments | #1 CRUD resource | Course/status filters, bulk enrol (throttled), compliance tab (mandatory overdue). |
| `EnrolmentProgressWidget` | Enrolments | #6 widget | Completion rates. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.enrolments.view-any')
        && BillingService::hasModule('lms.enrolments');
}
```

## Events

Consumes `EmployeeHired`. Fires none (v1 `CourseCompleted` dropped — side effects are same-domain calls). See [[../../../architecture/event-bus]].
