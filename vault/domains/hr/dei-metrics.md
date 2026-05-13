---
type: module
domain: HR & People
panel: hr
module-key: hr.dei
status: planned
color: "#4ADE80"
---

# DEI Metrics

> Diversity, Equity, and Inclusion metrics — gender distribution, pay equity ratio, age distribution, representation by level, and trend reporting for HR and executive audiences.

**Panel:** `hr`
**Module key:** `hr.dei`

## What It Does

DEI Metrics is a read-only analytics page that aggregates anonymised workforce demographic data to give HR and leadership a structured view of diversity across the organisation. Employees optionally self-report demographic data (gender, age band, ethnicity) — participation is voluntary and data is never shown at the individual level. The module computes representation ratios, pay equity comparisons across groups, and promotion rate parity. Trends are tracked over time so companies can measure progress against their stated DEI goals.

## Features

### Core
- Gender distribution: percentage of employees by gender across the company and by department and level
- Pay equity ratio: median salary comparison across gender groups within the same pay grade — sourced from Compensation & Benefits module
- Age distribution: employee count by age band (18–25, 26–35, 36–45, 46–55, 55+)
- Voluntary demographic survey: configurable annual survey where employees self-report demographic data — anonymous, opt-in
- Representation by level: percentage breakdown of demographic groups at each pay grade / seniority level

### Advanced
- Promotion parity: promotion rate comparison across demographic groups in the same period — surfaced as a parity ratio
- Hiring funnel diversity: percentage of applicants, interviewees, and offers by demographic group — from Recruitment module
- DEI goals tracking: HR sets numeric goals (e.g. "30% women in leadership by 2027") and the module tracks progress
- Reporting export: download a DEI summary report PDF or CSV — used for board presentations, regulatory submissions, and ESG reports
- Peer benchmark: anonymised industry benchmark data optionally loaded for comparison

### AI-Powered
- Intersectionality analysis: AI identifies compounding gaps — e.g. women of colour are underrepresented at senior levels more than either group alone — surfaced as an insight card
- Goal trajectory: based on current hiring and promotion rates, AI forecasts whether the company is on track to meet each DEI goal by its target date

## Data Model

```erDiagram
    employee_demographics {
        ulid id PK
        ulid employee_id FK
        ulid company_id FK
        string gender
        string age_band
        string ethnicity
        boolean is_self_reported
        date survey_date
        timestamps created_at/updated_at
    }

    dei_goals {
        ulid id PK
        ulid company_id FK
        string metric_key
        string target_description
        decimal target_value
        date target_date
        decimal current_value
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `employee_demographics` | No name or identifier — linked only via employee_id for HR aggregation |
| `is_self_reported` | True when employee submitted; false when HR estimates from government reporting |
| `dei_goals.metric_key` | e.g. `gender_leadership_pct`, `pay_equity_ratio` |

## Permissions

- `hr.dei.view-aggregates`
- `hr.dei.manage-goals`
- `hr.dei.export`
- `hr.dei.manage-survey`
- `hr.dei.view-intersectionality`

## Filament

- **Resource:** `DeiGoalResource`
- **Pages:** `ListDeiGoals`
- **Custom pages:** `DeiDashboardPage` — visualisations: gender distribution, pay equity, age bands, promotion parity, hiring funnel diversity
- **Widgets:** `GenderDistributionWidget`, `PayEquityRatioWidget`
- **Nav group:** Analytics (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Syndio | Pay equity analysis |
| Workday VIBE Index | DEI analytics |
| HiBob DEI Dashboard | Diversity and inclusion metrics |
| Parity (Culture Amp) | Pay equity and DEI tracking |

## Related

- [[employee-profiles]]
- [[compensation-benefits]]
- [[recruitment]]
- [[hr-analytics]]
- [[workforce-planning]]
