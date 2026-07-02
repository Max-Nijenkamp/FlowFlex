---
domain: lms
module: skills-matrix
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Skills Matrix

Track employee skills and proficiency levels. Identify skill gaps and link training to skill development.

## Module-key

| Field | Value |
|---|---|
| key | `lms.skills` |
| priority | p3 |
| panel | lms |
| permission-prefix | `lms.skills` |
| tables | `lms_skills`, `lms_employee_skills`, `lms_role_skills`, `lms_course_skills` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|HR Profiles]] | Skills per employee |
| Hard | [[../../core/billing/_module\|Billing]] + [[../../core/rbac/_module\|RBAC]] | Gating + permissions |
| Soft | [[../courses/_module\|Courses]] + [[../enrolments/_module\|Enrolments]] | Course→skill links; completion raises level (direct call) |
| Soft | [[../../hr/performance-reviews/_module\|HR Performance]] | Review-context display |

## Core Features

- **Skill catalogue** — skills grouped by category (technical, soft, compliance).
- **Employee skills** — proficiency per skill (none/beginner/intermediate/expert).
- **Assessment** — self + manager (both stored; manager authoritative for gaps *(assumed)*).
- **Gap analysis** — required vs actual per role.
- **Course→skill links** — completing a course raises the skill to the course's taught level (never lowers).
- **Team heat-map** — employees × skills.
- **Required skills per role/position**.
- **Development recommendations** — courses teaching gap skills.

## See features/

- [[features/skill-catalogue|Skill Catalogue]] — skills + role requirements + assessment (simple-resource).
- [[features/skills-heatmap|Skills Heat-map]] — employees × skills matrix (custom-page).
- [[features/gap-analysis|Gap Analysis]] — role gaps + course recommendations (custom-page/widget).

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Self vs manager assessments separate; manager drives gaps.
- [ ] Course completion raises to taught level, never lowers.
- [ ] Gap analysis vs role requirements fixtures.
- [ ] Recommendations = courses covering gaps.
- [ ] Self-assess restricted to own record; manager to reports.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | employee + reporting line | hr.profiles | Skills attach to employees; manager scope from reporting line |
| Commanded by | `SkillService::raiseFromCourse` | lms.enrolments | Completion raises skill level (same-domain) |
| Reads | taught_level per course | lms.courses | `lms_course_skills` links |
| Reads (by) | skill context | hr.performance | Review display reads skills read-only |

**Data ownership:** `lms.skills` writes only its four tables. It **reads** HR employee/reporting data (never writes hr tables) and is invoked by enrolments on completion. Manager scope derives from HR's reporting line, read-only ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../courses/_module|Courses]] · [[../../hr/performance-reviews/_module|HR Performance]] · [[../_index|LMS index]]
