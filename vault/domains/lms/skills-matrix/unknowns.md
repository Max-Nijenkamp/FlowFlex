---
domain: lms
module: skills-matrix
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Skills Matrix — Unknowns

## Assumed Items

- Manager value authoritative for gaps *(assumed)*.
- One `lms_employee_skills` row per assessor type *(assumed)*.
- `role_name` is a free-string, not an FK to an HR positions table *(assumed)* — see below.

## Open Questions

- Should `role_name` be an FK to an HR job/position entity rather than a string? (Cross-domain reference to HR.)
- Skill expiry/decay — do compliance skills lapse over time like certifications?
- Peer / 360 assessments beyond self + manager?
- Should the skills framework be shared with **HR performance-reviews** as a single competency model, or stay LMS-owned? (Biggest cross-domain question.)
- Evidence attachments per assessment (portfolio, certificate link)?
