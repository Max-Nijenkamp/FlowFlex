---
domain: lms
module: skills-matrix
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Skills Matrix — Architecture

## Proficiency Scale

`0 none · 1 beginner · 2 intermediate · 3 expert` (enum). Course completion raises to the course's `taught_level`, never lowers.

## Services & Actions

| Method | Responsibility |
|---|---|
| `SkillService::assess(AssessSkillData)` | Resolve assessor type (self / manager) and upsert one row per assessor type. |
| `SkillService::gapAnalysis(employeeId): Collection` | Role requirements (`lms_role_skills`) vs manager-assessed levels. |
| `SkillService::raiseFromCourse(enrolment)` | Hook from `EnrolmentService`; `max(current, taught_level)` for each linked skill. |
| `SkillService::recommendations(employeeId): Collection` | Courses teaching the employee's gap skills. |

### Assessor resolution

- **self** — the employee assessing their own record (`lms.skills.assess-own`, own employee id only).
- **manager** — a manager assessing a direct report (`lms.skills.assess-reports`, scoped by HR reporting line).
- Both values are stored (unique per assessor type); the **manager** value is authoritative for gap analysis *(assumed)*.

## Events

None. `raiseFromCourse` is a same-domain call from `EnrolmentService` on completion.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `SkillResource` | Skills | #1 CRUD resource | Catalogue + role requirements. |
| `SkillsMatrixPage` | Skills | #18 heat-map/matrix custom page | Employees × skills, gap highlighting. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.skills.view-any')
        && BillingService::hasModule('lms.skills');
}
```

## Jobs & Scheduling

None.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| `assess` | n-a | Upsert unique per `(employee, skill, assessor_type)` -- raced saves converge, last assessment wins by design |
| `raiseFromCourse` | Pessimistic | `lockForUpdate` on the skill row -- `max(current, taught)` must not lose a raced raise |
| Catalogue / role-requirement CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |
| Gap analysis / recommendations / heatmap | n-a | Read-only |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

None. The heat-map query is optimised to avoid N+1 across employees × skills.
