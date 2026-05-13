---
type: module
domain: HR & People
panel: hr
module-key: hr.succession
status: planned
color: "#4ADE80"
---

# Succession Planning

> Identify successors for key roles, assess readiness, visualise the bench strength of critical positions, and reduce key-person dependency risk.

**Panel:** `hr`
**Module key:** `hr.succession`

## What It Does

Succession Planning gives HR and leadership a structured process for identifying and developing successors for critical roles. HR marks specific roles as "key positions". For each key position, up to three successors are nominated and rated on readiness (ready now / ready in 1–2 years / ready in 3+ years). The succession bench view shows all key positions, their incumbents, and the depth of their succession pipeline. Readiness assessments draw on performance review scores, skills data from Talent Intelligence, and manager assessments. The nine-box grid plots employees by performance and potential for talent strategy discussions.

## Features

### Core
- Key positions: HR marks specific job titles or individual roles as succession-critical
- Successor nominations: for each key position, nominate up to three successors with a readiness rating
- Readiness ratings: `ready_now`, `ready_1_2_years`, `ready_3_plus_years`
- Bench strength view: list of all key positions showing incumbent and successor pipeline depth — RAG status (green = 2+ successors, amber = 1, red = none)
- Readiness notes: manager adds development notes per successor explaining what is needed to reach readiness

### Advanced
- Nine-box grid: plot employees on a 3×3 grid of performance (low/medium/high) vs potential (low/medium/high) — used in talent review sessions
- Development plans: for each nominated successor, create a structured development plan with milestones and target date for readiness advancement
- Incumbent risk: flag key position incumbents with a high flight risk score (from Talent Intelligence) — "at risk" badge on bench view
- Succession pipeline report: PDF export of all key positions with successor details — for board or CHRO review
- Historical tracking: store readiness changes over time — see how a successor's readiness progressed from one review to the next

### AI-Powered
- Successor recommendations: AI analyses performance scores, skills match, and career trajectory to recommend the top three potential successors for any key position — HR reviews and confirms
- Readiness acceleration insights: for each successor, AI highlights the two or three most impactful development actions based on the skills gap between them and the key position requirements

## Data Model

```erDiagram
    key_positions {
        ulid id PK
        ulid company_id FK
        string job_title
        ulid incumbent_id FK
        string risk_level
        timestamps created_at/updated_at
    }

    succession_nominees {
        ulid id PK
        ulid key_position_id FK
        ulid employee_id FK
        ulid company_id FK
        string readiness
        text development_notes
        integer priority
        timestamps created_at/updated_at
    }

    nine_box_ratings {
        ulid id PK
        ulid employee_id FK
        ulid company_id FK
        integer performance_rating
        integer potential_rating
        string review_period
        ulid rated_by FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `readiness` | ready_now / ready_1_2_years / ready_3_plus_years |
| `risk_level` | low / medium / high (key position business risk if role vacates) |
| `nine_box_ratings.performance_rating` | 1 (low), 2 (medium), 3 (high) |

## Permissions

- `hr.succession.view`
- `hr.succession.manage-key-positions`
- `hr.succession.nominate-successors`
- `hr.succession.rate-readiness`
- `hr.succession.export`

## Filament

- **Resource:** `KeyPositionResource`
- **Pages:** `ListKeyPositions`, `ViewKeyPosition` (with nominee pipeline)
- **Custom pages:** `BenchStrengthPage`, `NineBoxGridPage`
- **Widgets:** `BenchStrengthSummaryWidget` — count of at-risk positions (no successors) on HR dashboard
- **Nav group:** Analytics (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Workday Succession | Succession planning and management |
| Oracle HCM Succession | Key position and talent bench |
| Cornerstone | Succession planning |
| SAP SuccessFactors | Succession and talent management |

## Related

- [[talent-intelligence]]
- [[performance-reviews]]
- [[employee-profiles]]
- [[workforce-planning]]
