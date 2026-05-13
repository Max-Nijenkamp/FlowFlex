---
type: module
domain: Projects & Work
panel: projects
module-key: projects.okrs
status: planned
color: "#4ADE80"
---

# OKRs

> Objectives and Key Results — quarterly cycles, cascading goals from company to team to individual, progress check-ins, and alignment visualisation.

**Panel:** `projects`
**Module key:** `projects.okrs`

## What It Does

The OKR module lets companies run structured goal-setting cycles aligned to quarterly or annual cadences. Objectives are qualitative directional goals; Key Results are measurable outcomes that define success for each objective. OKRs cascade from company-level down to team-level and optionally to individual level. Each Key Result has a current value and a target — progress is entered via periodic check-ins. The module is linked to Performance Reviews (individual OKRs surfaced as context in review forms) and Tasks (tasks can be linked to a Key Result to show work in service of the goal).

## Features

### Core
- Objectives: name, description, owner, level (company / team / individual), cycle (Q1 2026, Annual 2026)
- Key Results: each objective has 1–5 key results with a measurable target (numeric or percentage)
- Check-ins: weekly or bi-weekly progress update per key result — record current value and a brief status note
- Progress calculation: automated — current value ÷ target value × 100 = progress percentage
- Cascade view: company OKRs → team OKRs → individual OKRs displayed as a tree showing alignment

### Advanced
- Parent alignment: team OKR links to the company OKR it supports — visualised in the cascade tree
- Confidence rating: during check-in, owner sets a confidence level (on track / at risk / off track) with a comment
- Task linkage: tasks in the Projects module can be linked to a Key Result — demonstrates execution against the goal
- Historical cycles: past cycle OKRs retained; view prior quarter results for retrospective
- OKR scoring: at cycle end, owner scores each Key Result (0–1.0 score) — Objective score = average of Key Results; company convention (aim for 0.7 not 1.0) documented in help text

### AI-Powered
- Ambition check: AI reviews the key result targets against prior cycle performance and flags whether targets are too conservative or likely unachievable based on historical velocity
- Alignment gaps: AI identifies teams with no OKRs linked to any company-level objective — surfaces as an alignment health warning

## Data Model

```erDiagram
    okr_objectives {
        ulid id PK
        ulid company_id FK
        ulid parent_objective_id FK
        string title
        text description
        ulid owner_id FK
        string level
        string cycle
        string status
        decimal score
        timestamps created_at/updated_at
    }

    okr_key_results {
        ulid id PK
        ulid objective_id FK
        ulid company_id FK
        string title
        decimal current_value
        decimal target_value
        string unit
        string confidence
        decimal score
        timestamps created_at/updated_at
    }

    okr_checkins {
        ulid id PK
        ulid key_result_id FK
        ulid company_id FK
        decimal value
        string confidence
        text note
        ulid submitted_by FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `level` | company / team / individual |
| `confidence` | on_track / at_risk / off_track |
| `unit` | %, $, users, NPS score, etc. |

## Permissions

- `projects.okrs.view`
- `projects.okrs.create`
- `projects.okrs.check-in`
- `projects.okrs.score-cycle`
- `projects.okrs.manage-cycles`

## Filament

- **Resource:** `OkrObjectiveResource`
- **Pages:** `ListOkrObjectives`, `ViewOkrObjective` (with key results and check-in history)
- **Custom pages:** `OkrCascadeTreePage` — tree visualisation of company → team → individual OKRs
- **Widgets:** `OkrProgressWidget` — current cycle average company OKR progress on dashboard
- **Nav group:** Planning (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Lattice OKRs | Goal management and OKRs |
| Leapsome | OKR tracking and goal setting |
| Betterworks | Enterprise OKR management |
| Perdoo | OKR software |

## Related

- [[tasks]]
- [[portfolios]]
- [[performance-reviews]]
- [[workforce-planning]]
