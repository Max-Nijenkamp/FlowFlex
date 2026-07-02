---
domain: lms
module: skills-matrix
feature: gap-analysis
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Gap Analysis & Recommendations

Compare an employee's skills against their role's requirements and recommend courses that close the gaps.

## Behaviour

- `gapAnalysis(employeeId)` returns `{skill, required_level, actual_level, gap}` using manager-assessed levels vs `lms_role_skills`.
- `recommendations(employeeId)` returns courses whose `lms_course_skills` teach a gap skill (at or above the required level).
- Recommendations link straight into enrolment.

## UI

- **Kind**: custom-page  <!-- gap report; may also surface as a widget on the LMS dashboard -->
- **Page**: "Gap Analysis" (`SkillsMatrixPage` tab / `GapAnalysisPage`, `/lms/skills/gaps`); optionally a widget.
- **Layout**: per-employee panel — required vs actual bar per skill, gap flagged; below = recommended courses with an "Enrol" action.
- **Key interactions**: pick employee (own/report scope); view gaps; enrol from a recommendation.
- **States**: empty (no role requirements set → "Set role requirements to see gaps") · loading (skeleton) · error (no reporting scope → 403) · selected (skill row expanded).
- **Gating**: `lms.skills.view-any`; enrol-from-recommendation needs `lms.enrolments.enrol`.

## Data

- Owns / writes: nothing (read/compute); enrol action routes through `EnrolmentService`.
- Reads: `lms_employee_skills`, `lms_role_skills`, `lms_course_skills`, courses.
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: enrolment (via recommendations → `EnrolmentService::enrol`).
- Shared entity: courses, HR employee/role.

## Unknowns

- Whether `role_name` should be an HR position FK (affects gap accuracy) — see [[../unknowns]].

## Related

- [[../_module|Skills Matrix module]] · [[skills-heatmap]] · [[../../enrolments/_module|Enrolments]]
