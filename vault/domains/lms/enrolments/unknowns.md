---
domain: lms
module: enrolments
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Enrolments — Unknowns

## Assumed Items

- `CourseCompleted` cross-domain event dropped in favour of same-domain calls *(assumed)* — inherited from the v2 spec pass.
- External-learner portal login via signed magic link / scoped token *(assumed)* — exact issuance UX undocumented.
- Reminder cadence is a single 7-day window *(assumed)*.
- Learner PII (email/name) stored plaintext, no encryption requirement *(assumed)*.

## Open Questions

- **HR integration on completion**: should completion (or certification) feed an HR training record / performance-review input? Currently LMS-internal only — no event crosses to HR. This is the biggest cross-domain gap.
- How is a learner's "employee vs external" determined at enrol — always explicit, or inferred from email match to a `users` row?
- Mandatory-course assignment by role/department: is the role→course mapping its own table, or config? (Not modelled here.)
- Multiple reminder tiers (e.g. 14d + 3d + overdue) vs the single 7d window?
- Should dropping an enrolment cascade-clean `lms_lesson_progress`, or retain it for re-enrolment history?
- GDPR: retention/erasure of external `lms_learners` on request.
