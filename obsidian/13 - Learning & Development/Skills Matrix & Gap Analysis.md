---
tags: [flowflex, domain/lms, skills, gap-analysis, phase/7]
domain: Learning & Development
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-07
---

# Skills Matrix & Gap Analysis

Know what skills your team has, what each role needs, and bridge the gap with targeted training recommendations. Structured skills taxonomy ensures everyone uses the same language.

**Who uses it:** L&D team, HR managers, people managers
**Filament Panel:** `lms`
**Depends on:** [[HR — Employee Profiles]], [[Course Builder & LMS]]
**Phase:** 7
**Build complexity:** High — 4 resources, 2 pages, 5 tables

---

## Features

- **Skills taxonomy library** — structured list of all skills in the organisation; categorised by domain (technical/leadership/communication/etc.); each skill has defined level descriptors
- **Skill levels** — defined per skill: none/beginner/intermediate/advanced/expert; each level has a description to calibrate assessments
- **Role skill requirements** — define the required skill and minimum level per role title; used as the benchmark for gap analysis
- **Employee skill assessment** — L&D or manager assesses each employee's current skill level per skill; stored in `employee_skills` with `assessed_by` and `assessed_at`
- **Employee self-assessment** — optionally allow employees to rate their own skills; manager can approve or adjust
- **Gap analysis computation** — computed from `skill_requirements` (role/level required) vs `employee_skills` (current level); `gap_score` = required_level_int - current_level_int; negative = exceeds requirement
- **`SkillGapIdentified` event** — fires when a gap_score above threshold is computed; recommends a training course from the LMS catalogue
- **Skills matrix view** — grid view showing all employees × skills with colour-coded levels; filterable by team or department
- **Team skills heatmap** — aggregated skills coverage by team; identify which skills have no coverage or are critically low across the team
- **Training recommendations** — for each identified gap, suggest relevant courses from the [[Course Builder & LMS]] catalogue based on skill category match
- **Succession planning integration** — `skill_gaps` data feeds into [[Succession Planning]] readiness scoring
- **Export skills matrix** — CSV export of the full matrix for performance reviews or external reporting

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `skills`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `category` | string nullable | e.g. "technical", "leadership", "communication" |
| `description` | text nullable | |
| `is_active` | boolean default true | |

### `skill_levels`
| Column | Type | Notes |
|---|---|---|
| `skill_id` | ulid FK | → skills |
| `level` | enum | `none`, `beginner`, `intermediate`, `advanced`, `expert` |
| `level_int` | integer | 0-4 for gap calculation |
| `description` | text | what this level means for this skill |

### `employee_skills`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | → tenants |
| `skill_id` | ulid FK | → skills |
| `level` | enum | `none`, `beginner`, `intermediate`, `advanced`, `expert` |
| `assessed_at` | timestamp | |
| `assessed_by` | ulid FK nullable | → tenants (manager or L&D) |
| `is_self_assessed` | boolean default false | |
| `notes` | string nullable | |

### `skill_requirements`
| Column | Type | Notes |
|---|---|---|
| `role` | string | role title e.g. "Software Engineer" |
| `skill_id` | ulid FK | → skills |
| `required_level` | enum | minimum required level |
| `required_level_int` | integer | 0-4 |
| `is_mandatory` | boolean default true | |

### `skill_gaps`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | → tenants |
| `skill_id` | ulid FK | → skills |
| `current_level` | enum | |
| `required_level` | enum | |
| `gap_score` | integer | required_int - current_int |
| `training_recommended` | boolean default false | |
| `recommended_course_id` | ulid FK nullable | → courses |
| `computed_at` | timestamp | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `SkillGapIdentified` | `tenant_id`, `skill_id`, `gap_score`, `recommended_course_id` | Notification to employee and their manager with course recommendation |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `CourseCompleted` | [[Course Builder & LMS]] | Re-compute `skill_gaps` for the employee and relevant skills |

---

## Permissions

```
lms.skills.view
lms.skills.create
lms.skills.edit
lms.skills.delete
lms.employee-skills.view
lms.employee-skills.create
lms.employee-skills.edit
lms.skill-requirements.view
lms.skill-requirements.create
lms.skill-requirements.edit
lms.skill-gaps.view
lms.skills-matrix.export
```

---

## Related

- [[LMS Overview]]
- [[Course Builder & LMS]]
- [[Performance & Reviews]]
- [[Succession Planning]]
