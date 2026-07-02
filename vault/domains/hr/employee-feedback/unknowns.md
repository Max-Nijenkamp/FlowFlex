---
domain: hr
module: employee-feedback
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Employee Feedback — Unknowns

Every assumption and open question. Resolve before / during rebuild.

## Assumptions (`*(assumed)*`)

- `hr_feedback.message` is plain text, no rich text editor. *(assumed)*
- `visibility` is forced by type: praise=public-capable, constructive=private, coaching-note=manager-chain. *(assumed)*
- `LogOneOnOneData.employee_id` must report to the current manager. *(assumed)*
- Coaching notes are visible up the manager chain and to HR, invisible to the recipient's peers. *(assumed — derived from visibility forcing)*
- `spatie/laravel-tags` table is company-scoped via team/tenant convention. *(assumed)*

## Open Questions

- None recorded beyond the assumptions above in the source spec.

## Unverified

- Whether HR `view-any` should ever see 1-on-1 notes (spec says participant-only; no override defined).
- Recognition feed polling interval (60s) is a spec default, not confirmed against a UX requirement.
