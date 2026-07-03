---
domain: lms
module: enrolments
feature: enrolment-management
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Enrolment Management

Admin-side enrol, bulk-enrol, track progress, and monitor mandatory-training compliance.

## Behaviour

- Enrol a learner (`EnrolData`): course must be published; prerequisites met; no duplicate active enrolment.
- Bulk enrol (`BulkEnrolData`): per-row try/catch result; rate-limited.
- Progress % is read from lesson completion (recomputed by lessons); status follows the state machine.
- Compliance tab lists mandatory-course enrolments that are overdue.

## UI

- **Kind**: simple-resource
- **Page**: "Enrolments" (`EnrolmentResource`, `/lms/enrolments`)
- **Layout**: table (learner, course, status badge, progress bar, due date) + filters (course, status, overdue). Compliance tab = overdue mandatory list. Bulk-enrol action (throttled). `EnrolmentProgressWidget` header.
- **Key interactions**: enrol form (prerequisite check inline); bulk-enrol modal; drop action; filter to overdue; deep-link learner.
- **States**: empty (no enrolments → "Enrol your first learner") · loading (skeleton) · error (prerequisite unmet / duplicate → validation toast) · selected (row → detail).
- **Gating**: view `lms.enrolments.view-any`; enrol/bulk `lms.enrolments.enrol`; edit/drop `lms.enrolments.manage`.

## Data

- Owns / writes: `lms_enrolments`, `lms_learners`.
- Reads: `CourseService::prerequisitesMet` (courses), lesson totals (lessons).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing here (see [[auto-enrol-on-hire]]).
- Feeds: on completion, calls certifications / skills / paths services (same-domain).
- Shared entity: courses (published list), HR employees (as learners, read-only).

## Test Checklist

### Unit
- [ ] `EnrolData` validation; prerequisite check delegates to `CourseService::prerequisitesMet`

### Feature (Pest)
- [ ] Duplicate active enrolment rejected under race (lockForUpdate); bulkEnrol returns per-row results and is rate-limited
- [ ] Completion at 100% fires side effects exactly once (certificate, skills, path hook)
- [ ] Tenant isolation + permission: enrol/bulk verbs enforced

### Livewire
- [ ] Enrolment resource filters by status/course; bulk-enrol action validates file/rows; hidden without permission/module

## Unknowns

- Role→mandatory-course mapping storage is unmodelled — see [[../unknowns]].

## Related

- [[../_module|Enrolments module]] · [[learner-portal]] · [[auto-enrol-on-hire]] · [[../architecture]]
