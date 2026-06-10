---
type: module
domain: Learning & Development
domain-key: lms
panel: lms
module-key: lms.skills
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac]
soft-depends: [lms.courses, lms.enrolments, hr.performance]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [lms_skills, lms_employee_skills, lms_role_skills, lms_course_skills]
permission-prefix: lms.skills
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Skills Matrix

Track employee skills and proficiency levels. Identify skill gaps and link training to skill development.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | skills per employee |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/lms/courses\|lms.courses]] + [[domains/lms/enrolments\|lms.enrolments]] | course→skill links; completion raises level (direct call from EnrolmentService) |
| Soft | [[domains/hr/performance-reviews\|hr.performance]] | review context display |

---

## Core Features

- Skill catalogue: skills grouped by category (technical, soft, compliance)
- Employee skills: proficiency level per skill (none/beginner/intermediate/expert)
- Skill assessment: self-assessment + manager assessment (both stored; manager value authoritative for gaps *(assumed)*)
- Skill gap analysis: required vs actual skills per role
- Link courses to skills: completing a course raises skill to the course's taught level (never lowers)
- Team skills heat-map: employees × skills
- Required skills per role/position
- Skill development recommendations (courses teaching gap skills)

---

## Data Model

### lms_skills — id, company_id (indexed), name (unique per company), category (technical/soft/compliance)
### lms_employee_skills — id, company_id (indexed), employee_id FK, skill_id FK, proficiency_level (0–3 enum), assessed_by (self/manager + user id), assessed_at; unique `(employee_id, skill_id, assessor_type)` *(assumed: one row per assessor type)*
### lms_role_skills — id, company_id (indexed), role_name, skill_id FK, required_level; unique `(role_name, skill_id)`
### lms_course_skills *(formalised from v1 prose)* — id, company_id, course_id FK, skill_id FK, taught_level

---

## DTOs

### AssessSkillData — employee_id (self = own; manager = report), skill_id, proficiency_level (0–3)
### SetRoleSkillData — role_name, skill_id, required_level

## Services & Actions

- `SkillService::assess(AssessSkillData)` — assessor-type resolution
- `SkillService::gapAnalysis(employeeId): Collection` — role requirements vs manager-assessed levels
- `SkillService::raiseFromCourse(enrolment)` — hook from EnrolmentService; max(current, taught_level)
- `SkillService::recommendations(employeeId): Collection` — courses teaching gap skills

---

## Filament

**Nav group:** Skills

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SkillResource` | #1 CRUD resource | catalogue + role requirements |
| `SkillsMatrixPage` | #9 heat-map custom page | employees × skills, gap highlighting |

---

## Permissions

`lms.skills.view-any` · `lms.skills.manage` · `lms.skills.assess-own` · `lms.skills.assess-reports`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Self vs manager assessments separate; manager drives gaps
- [ ] Course completion raises to taught level, never lowers
- [ ] Gap analysis vs role requirements fixtures
- [ ] Recommendations = courses covering gaps
- [ ] Self-assess restricted to own record; manager to reports

---

## Build Manifest

```
database/migrations/xxxx_create_lms_skills_table.php
database/migrations/xxxx_create_lms_employee_skills_table.php
database/migrations/xxxx_create_lms_role_skills_table.php
database/migrations/xxxx_create_lms_course_skills_table.php
app/Models/LMS/{Skill,EmployeeSkill,RoleSkill,CourseSkill}.php
app/Data/LMS/{AssessSkillData,SetRoleSkillData}.php
app/Services/LMS/SkillService.php
app/Filament/LMS/Resources/SkillResource.php
app/Filament/LMS/Pages/SkillsMatrixPage.php
database/factories/LMS/{SkillFactory,EmployeeSkillFactory}.php
tests/Feature/LMS/SkillsMatrixTest.php
```

---

## Related

- [[domains/lms/courses]]
- [[domains/hr/performance-reviews]]
