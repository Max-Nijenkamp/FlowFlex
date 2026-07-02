---
domain: lms
module: skills-matrix
feature: skill-catalogue
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Skill Catalogue

Manage the skill list, role requirements, course→skill links, and record assessments.

## Behaviour

- Skills grouped by category (technical/soft/compliance); unique name per company.
- Role requirements (`SetRoleSkillData`): `required_level` per `(role_name, skill)`.
- Course→skill links carry a `taught_level`.
- Assessments (`AssessSkillData`): self or manager, scoped by permission.

## UI

- **Kind**: simple-resource
- **Page**: "Skills" (`SkillResource`, `/lms/skills`)
- **Layout**: table (name, category, #roles requiring, #courses teaching) + form (name, category); relation managers for role requirements + course links; assessment action (self/manager scoped).
- **Key interactions**: create skill; set role requirement; link course + taught level; assess an employee (own or report).
- **States**: empty (no skills → "Build your skill catalogue") · loading (skeleton) · error (self-assessing someone else / manager assessing a non-report → 403) · selected (row → edit).
- **Gating**: view `lms.skills.view-any`; manage `lms.skills.manage`; assess `lms.skills.assess-own` / `lms.skills.assess-reports`.

## Data

- Owns / writes: `lms_skills`, `lms_role_skills`, `lms_course_skills`, `lms_employee_skills`.
- Reads: HR employees + reporting line (scope); courses (links).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: catalogue + assessments power the heat-map + gap analysis.
- Shared entity: employee/reporting (HR, read-only), courses.

## Unknowns

- `role_name` FK vs string; shared competency model with HR — see [[../unknowns]].

## Related

- [[../_module|Skills Matrix module]] · [[skills-heatmap]] · [[gap-analysis]]
