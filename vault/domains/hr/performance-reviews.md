---
type: module
domain: HR & People
panel: hr
module-key: hr.performance
status: planned
color: "#4ADE80"
---

# Performance Reviews

> 360-degree review cycles, self-assessments, manager ratings, peer feedback, and goal tracking — structured performance management from setup to calibration.

**Panel:** `hr`
**Module key:** `hr.performance`

## What It Does

Performance Reviews enables companies to run structured review cycles — annual, bi-annual, or quarterly — with configurable rating scales and question templates. A cycle includes self-assessments, manager reviews, and optional peer reviews. HR configures the cycle, assigns participants, and opens the review window. Employees complete their self-assessment and see their manager's review after calibration. Goals set in prior cycles roll forward as context. Review scores feed into the Talent Intelligence and Succession Planning modules.

## Features

### Core
- Review cycles: configurable name, period (Q1, annual, etc.), review type (self + manager / 360 / manager-only), and open/close dates
- Question templates: reusable sets of rating questions per competency area with 1–5 scale or text response
- Participant assignment: HR assigns reviewees and their reviewers (manager + selected peers for 360)
- Review forms: employee completes self-assessment; manager completes manager review; peers complete peer review
- Calibration lock: HR locks the cycle for calibration — employees cannot edit responses; HR and managers compare scores

### Advanced
- Goal tracking: employees set goals at cycle start, rate progress at cycle end — goals carry forward to next cycle
- Calibration view: side-by-side rating comparison across all employees in a cycle — used by HR to normalise scores
- Review release: HR releases completed reviews to employees on a chosen date — employee sees their manager's comments after release
- Historical reviews: full review history per employee retained — visible on employee profile and in Talent Intelligence
- Export: cycle results to CSV/PDF for external storage or sharing with board

### AI-Powered
- Review quality nudge: if a manager writes fewer than 50 words for a rating, AI prompts them to add more specific evidence before submission
- Suggested goals: based on job title and prior cycle goals, AI suggests three goal templates for the employee to adopt or modify

## Data Model

```erDiagram
    review_cycles {
        ulid id PK
        ulid company_id FK
        string name
        string period
        string review_type
        date open_date
        date close_date
        string status
        timestamps created_at/updated_at
    }

    review_participants {
        ulid id PK
        ulid cycle_id FK
        ulid employee_id FK
        ulid manager_id FK
        string self_status
        string manager_status
        string overall_status
        timestamps created_at/updated_at
    }

    review_responses {
        ulid id PK
        ulid cycle_id FK
        ulid participant_id FK
        ulid reviewer_id FK
        string reviewer_type
        string question_key
        integer rating
        text comment
        timestamps created_at/updated_at
    }

    performance_goals {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        ulid cycle_id FK
        string title
        text description
        string status
        integer progress_rating
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `review_type` | self_manager / three_sixty / manager_only |
| `reviewer_type` | self / manager / peer |
| `overall_status` | not_started / in_progress / complete / released |

## Permissions

- `hr.performance.view-own`
- `hr.performance.complete-review`
- `hr.performance.view-team-reviews`
- `hr.performance.manage-cycles`
- `hr.performance.calibrate`

## Filament

- **Resource:** `ReviewCycleResource`
- **Pages:** `ListReviewCycles`, `CreateReviewCycle`, `ManageReviewCycle` (participant + status view)
- **Custom pages:** `ReviewFormPage` — individual review completion form for employee/manager
- **Widgets:** `ActiveReviewCycleWidget` — open cycles with days remaining countdown
- **Nav group:** Employees (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Lattice | Performance management and review cycles |
| Culture Amp | Employee performance reviews |
| Workday | Performance and development management |
| Leapsome | 360 reviews and goal management |

## Related

- [[employee-profiles]]
- [[talent-intelligence]]
- [[succession-planning]]
- [[employee-feedback]]
- [[compensation-benefits]]
