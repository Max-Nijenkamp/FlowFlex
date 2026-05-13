---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: complete
migration_range: 480004–480005
last_updated: 2026-05-12
right_brain_log: "[[builder-log-lms-phase7]]"
---

# Skills Matrix & Gap Analysis

Org-wide skills taxonomy, employee self-assessment and manager ratings, skill gap heatmap, and automated learning path recommendations to close gaps.

**Panel:** `lms`  
**Phase:** 7  
**Migration range:** `710000–719999`

---

## Features

### Core (MVP)

- Skills taxonomy: categories → skills → proficiency levels (1–5)
- Employee self-assessment: rate own proficiency per skill
- Manager assessment: override or confirm employee self-rating
- Role-skill mapping: required skills + minimum proficiency per job title
- Skills matrix view: employees × skills heatmap
- Gap report: who is below required proficiency per role

### Advanced

- Skill endorsements: colleagues endorse each other's skills
- Team skills dashboard: skills coverage per department
- Succession risk: identify roles with single points of failure
- Skills inventory export (CSV, Excel)

### AI-Powered

- Auto-suggest skills based on job title (uses role taxonomy)
- Learning path recommendation: gap detected → suggest courses from [[course-builder-lms]]
- Skills trend analysis: identify emerging skills in the market vs internal gaps

---

## Data Model

```erDiagram
    skills {
        ulid id PK
        ulid company_id FK
        string name
        string category
        text description
    }

    skill_assessments {
        ulid id PK
        ulid skill_id FK
        ulid employee_id FK
        integer self_rating
        integer manager_rating
        integer final_rating
        ulid assessed_by FK
        date assessed_at
    }

    role_skill_requirements {
        ulid id PK
        ulid company_id FK
        string job_title
        ulid skill_id FK
        integer required_level
    }

    skills ||--o{ skill_assessments : "assessed"
    skills ||--o{ role_skill_requirements : "required for"
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `SkillGapIdentified` | Assessment below required | LMS (recommend course), HR (flag in profile) |
| `SkillAssessmentCompleted` | Rating saved | HR (update employee profile) |

### Consumed

| Event | From | Action |
|---|---|---|
| `CourseCompleted` | LMS | Auto-update relevant skill rating |
| `EmployeeHired` | HR | Trigger initial skills self-assessment |
| `JobTitleChanged` | HR | Re-evaluate gaps for new role requirements |

---

## Permissions

```
lms.skills.view-any
lms.skills.manage-taxonomy
lms.skills.self-assess
lms.skills.manager-assess
lms.skills.view-gaps
```

---

## Related

- [[MOC_LMS]]
- [[course-builder-lms]] — gap closure via courses
- [[succession-planning]] — skills underpin succession readiness
- [[entity-employee]]
