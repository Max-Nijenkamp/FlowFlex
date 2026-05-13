---
type: module
domain: Learning & Development
panel: lms
module-key: lms.skills
status: planned
color: "#4ADE80"
---

# Skills

> Organisation-wide skills taxonomy with proficiency levels, employee skill records, role-to-skill mapping, and gap analysis.

**Panel:** `lms`
**Module key:** `lms.skills`

---

## What It Does

Skills provides the foundational competency framework for the entire FlowFlex platform. L&D administrators define a taxonomy of skill categories and individual skills, each with proficiency levels from 1 to 5. Employees self-assess their proficiency and managers can confirm or adjust ratings. Job titles are mapped to required skills and minimum proficiency levels, enabling the system to automatically calculate who has skill gaps and surface targeted learning recommendations to close them.

---

## Features

### Core
- Skills taxonomy: categories → skills → proficiency level descriptors (1–5)
- Employee self-assessment: rate own proficiency on each relevant skill
- Manager assessment: review and confirm or adjust employee self-ratings
- Role-to-skill mapping: define required skills and minimum proficiency per job title
- Skills matrix view: employee × skill heatmap showing actual vs required proficiency
- Gap report: list employees below required proficiency for their current role

### Advanced
- Skill endorsements: colleagues can endorse each other's skill ratings
- Team skills dashboard: aggregate skills coverage by department or team
- Succession risk view: identify roles with single-point-of-failure skill concentration
- Skills inventory export: CSV or Excel for external analysis
- Historical tracking: view how an employee's skill profile has changed over time

### AI-Powered
- Auto-suggest skills: recommend relevant skills based on an employee's job title
- Learning path recommendation: detect a skill gap and suggest courses or paths to close it
- Emerging skills intelligence: compare internal skill profiles to market skill trends

---

## Data Model

```erDiagram
    skills {
        ulid id PK
        ulid company_id FK
        string name
        string category
        text description
        json proficiency_descriptors
        timestamp deleted_at
        timestamps created_at_updated_at
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

    skills ||--o{ skill_assessments : "assessed via"
    skills ||--o{ role_skill_requirements : "required for"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `skills` | Skill definitions | `id`, `company_id`, `name`, `category`, `proficiency_descriptors` |
| `skill_assessments` | Employee ratings | `id`, `skill_id`, `employee_id`, `self_rating`, `manager_rating`, `final_rating` |
| `role_skill_requirements` | Role requirements | `id`, `company_id`, `job_title`, `skill_id`, `required_level` |

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

## Filament

- **Resource:** `App\Filament\Lms\Resources\SkillResource`
- **Pages:** `ListSkills`, `CreateSkill`, `EditSkill`, `ViewSkill`
- **Custom pages:** `SkillsMatrixPage` (heatmap view), `GapAnalysisPage`
- **Widgets:** `TopSkillGapsWidget`, `TeamCoverageWidget`
- **Nav group:** Catalog

---

## Displaces

| Feature | FlowFlex | Cornerstone | Docebo | TalentLMS |
|---|---|---|---|---|
| Skills taxonomy with levels | Yes | Yes | Yes | No |
| Role-to-skill gap analysis | Yes | Yes | No | No |
| AI skill recommendations | Yes | No | No | No |
| Native HR integration | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[courses]] — course completion can auto-update skill ratings
- [[learning-paths]] — paths are mapped to target skills
- [[certifications]] — certifications can be linked to skill levels
- [[compliance-training]] — compliance requirements map to skills
- [[assessments]] — assessment results inform skill ratings
- [[analytics]] — skill coverage reporting
