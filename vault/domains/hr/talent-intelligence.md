---
type: module
domain: HR & People
panel: hr
module-key: hr.talent
status: planned
color: "#4ADE80"
---

# Talent Intelligence

> Skills inventory, skills gap analysis, high-potential employee identification, and succession planning inputs — AI-assisted talent strategy for HR leaders.

**Panel:** `hr`
**Module key:** `hr.talent`

## What It Does

Talent Intelligence gives HR a structured view of the skills and capabilities across the workforce. Employees and managers add skills to employee profiles with proficiency levels. HR defines required skills per role. The skills gap analysis compares current employee skills against role requirements and surfaces gaps at individual, team, and company level. High-potential employees are identified based on performance review scores, growth trajectory, and manager nominations. This data feeds directly into Succession Planning.

## Features

### Core
- Skills inventory: employees add skills (from a managed taxonomy) with self-assessed proficiency (1=beginner, 5=expert)
- Role skill requirements: HR defines the skills and minimum proficiency levels required for each job title
- Skills gap report: per employee, shows which required skills for their role are missing or below required proficiency
- High-potential flag: manager can nominate an employee as high-potential — stored as a flag on the employee record
- Talent heatmap: department-level view of skills coverage — green (sufficient), amber (gap), red (critical gap)

### Advanced
- Skills taxonomy management: HR manages the master list of skill categories and individual skills — prevents free-text proliferation
- Peer skill endorsements: colleagues endorse each other's skills — weighted lower than self-assessment but included in proficiency score
- Learning path suggestions: for each skills gap, HR can link a learning resource or course (via LMS module if active)
- Succession readiness: for each key role, show the top three potential successors ranked by skills match + performance score
- Talent pipeline export: export current high-potential list and skills profiles for CHRO/board review

### AI-Powered
- AI skills inference: from performance review comments and goal descriptions, AI suggests additional skills to add to an employee's profile — employee confirms or rejects suggestions
- Future skills forecast: based on company industry and strategic goals (from OKR module), AI predicts which skills will be in highest demand in 12–24 months

## Data Model

```erDiagram
    skill_taxonomy {
        ulid id PK
        ulid company_id FK
        string category
        string name
        timestamps created_at/updated_at
    }

    employee_skills {
        ulid id PK
        ulid employee_id FK
        ulid skill_id FK
        ulid company_id FK
        integer proficiency
        boolean is_ai_suggested
        boolean is_confirmed
        timestamps created_at/updated_at
    }

    role_skill_requirements {
        ulid id PK
        ulid company_id FK
        string job_title
        ulid skill_id FK
        integer required_proficiency
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `proficiency` | 1 (beginner) to 5 (expert) |
| `is_ai_suggested` | True when AI inferred from review/goal text |
| `is_confirmed` | Employee or manager confirmed AI suggestion |

## Permissions

- `hr.talent.view`
- `hr.talent.manage-skills-taxonomy`
- `hr.talent.edit-own-skills`
- `hr.talent.nominate-high-potential`
- `hr.talent.view-reports`

## Filament

- **Resource:** `SkillTaxonomyResource`
- **Pages:** `SkillsGapPage`, `TalentHeatmapPage`
- **Custom pages:** `SkillsGapPage`, `TalentHeatmapPage`
- **Widgets:** `SkillsCoverageWidget` — company-wide skills gap severity on HR dashboard
- **Nav group:** Analytics (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Workday Skills Cloud | Skills management and talent intelligence |
| Cornerstone | Talent management and skills tracking |
| Eightfold.ai | AI talent intelligence platform |
| LinkedIn Talent Insights | Workforce skills analytics |

## Implementation Notes

**Filament:** `SkillsGapPage` and `TalentHeatmapPage` are custom `Page` classes — neither is a standard Resource list. `SkillsGapPage` renders a per-employee breakdown of required vs actual skills for their role — a matrix table with colour-coded cells (green = meets requirement, amber = below required proficiency, red = missing). `TalentHeatmapPage` renders the same at department level as a grid of coloured squares — this requires a custom Blade view with inline CSS colour logic, not a Filament table.

**The spec lists both as Resource Pages AND Custom Pages simultaneously** — this is a conflict. Correct structure: `SkillTaxonomyResource` (standard CRUD for the taxonomy list), `SkillsGapPage` (custom Page), `TalentHeatmapPage` (custom Page).

**Meilisearch:** `SkillTaxonomy` model should implement `Laravel\Scout\Searchable` so the skill picker (when employees add skills) has a fast typeahead search. Index on `name` and `category`.

**AI features — skills inference from performance reviews:** Calls `app/Services/AI/TalentInsightService.php`. The service retrieves the employee's last 3 performance review comment texts and sends them to OpenAI GPT-4o with a prompt asking it to extract skill names from the taxonomy that are evidenced in the text. Returns a JSON array of `{skill_id, evidence_quote}`. The employee then sees these as `is_ai_suggested = true` suggestions to confirm or reject.

**Future skills forecast:** Calls the same service with company OKR text from the `projects.okrs` module (if active) plus industry vertical (from company settings). Returns a JSON array of emerging skill names. These are not added to `employee_skills` — they are displayed as a strategic planning advisory in the `TalentHeatmapPage`.

**Missing from data model:** `employee_skills.proficiency` is a 1–5 integer representing self-assessment. If peer endorsements are to be included in the proficiency calculation, a separate `skill_endorsements {ulid id, ulid employee_id FK, ulid skill_id FK, ulid endorser_id FK, integer proficiency, timestamp endorsed_at}` table is needed — not currently defined. Without it, the "peer endorsements" feature cannot be built.

## Related

- [[employee-profiles]]
- [[performance-reviews]]
- [[succession-planning]]
- [[workforce-planning]]
