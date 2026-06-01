---
type: module
domain: Learning & Development
panel: lms
module-key: lms.skills
status: planned
color: "#4ADE80"
---

# Skills Matrix

Track employee skills and proficiency levels. Identify skill gaps and link training to skill development.

## Core Features

- Skill catalogue: skills grouped by category (technical, soft, compliance)
- Employee skills: proficiency level per skill (none/beginner/intermediate/expert)
- Skill assessment: self-assessment + manager assessment
- Skill gap analysis: required vs actual skills per role
- Link courses to skills: completing a course raises skill level
- Team skills heat-map: visualise skills across a team
- Required skills per role/position
- Skill development recommendations (suggest courses for gaps)

## Data Model

| Table | Key Columns |
|---|---|
| `lms_skills` | company_id, name, category |
| `lms_employee_skills` | company_id, employee_id, skill_id, proficiency_level, assessed_by, assessed_at |
| `lms_role_skills` | company_id, role_name, skill_id, required_level |

## Filament

**Nav group:** Skills

- `SkillResource` — manage skill catalogue
- `SkillsMatrixPage` (custom page) — team heat-map: employees × skills
- Gap analysis view

## Cross-Domain / Events

- Consumes `CourseCompleted` → raise linked skill level
- Integrates with HR performance reviews

## Related

- [[domains/lms/courses]]
- [[domains/hr/performance-reviews]]
