---
type: module
domain: HR & People
panel: hr
module-key: hr.wellbeing
status: planned
color: "#4ADE80"
---

# Employee Wellbeing

> Wellbeing check-ins, mental health pulse surveys, anonymous feedback, and burnout risk indicators — proactive people care built into the HR platform.

**Panel:** `hr`
**Module key:** `hr.wellbeing`

## What It Does

Employee Wellbeing gives HR teams a structured way to monitor and support workforce mental health and engagement. Periodic pulse surveys are sent to employees asking simple wellbeing questions (mood, stress, workload). Responses are anonymous by default. HR sees aggregated trends — not individual responses — broken down by department and time period. When aggregate scores drop below a configurable threshold, HR managers are alerted. Employees can also submit anonymous feedback directly via the Self-Service portal without being prompted by a survey.

## Features

### Core
- Pulse surveys: HR schedules recurring surveys (weekly, bi-weekly, monthly) with 1–5 questions
- Question types: emoji mood scale, number scale (1–10), yes/no, open text
- Anonymity default: all individual responses stored anonymously — only HR sees aggregates; no manager can identify individual responses
- Wellbeing score: aggregate score per department per survey period — trend line over time
- Alert threshold: configurable score floor — if average drops below threshold, HR manager receives a notification

### Advanced
- Company resource library: HR uploads mental health resources (links, PDFs, contacts) displayed in the Self-Service portal
- Anonymous feedback channel: employees submit free-text feedback at any time — HR reads responses in aggregate; no identifying information attached
- Burnout risk indicator: combines high overtime hours (from Time & Attendance), low wellbeing score, and high leave usage — flags employees showing all three simultaneously
- Survey cadence management: HR can pause surveys during high-pressure periods (e.g. end of quarter) and resume with a message explaining the pause
- Department drill-down: wellbeing trends disaggregated by department — HR can identify which team needs support

### AI-Powered
- Sentiment analysis: open-text survey responses processed by AI to extract common themes (workload, management, culture, tools) — shown as a word cloud and theme frequency chart without exposing individual responses
- Early warning system: AI model trained on wellbeing scores + leave + overtime data predicts teams at elevated burnout risk 4 weeks ahead

## Data Model

```erDiagram
    wellbeing_surveys {
        ulid id PK
        ulid company_id FK
        string name
        string frequency
        json questions
        string status
        timestamps created_at/updated_at
    }

    wellbeing_responses {
        ulid id PK
        ulid survey_id FK
        ulid company_id FK
        ulid department_id FK
        integer period_year
        integer period_week
        json scores
        text open_text
        timestamps created_at/updated_at
    }

    anonymous_feedback {
        ulid id PK
        ulid company_id FK
        ulid department_id FK
        text message
        boolean is_read
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `wellbeing_responses` | No `employee_id` — responses are anonymous |
| `department_id` | Stored for aggregate filtering — cannot identify individual |
| `anonymous_feedback.is_read` | HR marks as read when actioned |

## Permissions

- `hr.wellbeing.submit-response`
- `hr.wellbeing.view-aggregates`
- `hr.wellbeing.manage-surveys`
- `hr.wellbeing.view-alerts`
- `hr.wellbeing.manage-resources`

## Filament

- **Resource:** `WellbeingSurveyResource`
- **Pages:** `ListWellbeingSurveys`, `WellbeingDashboardPage` — trends, department scores, anonymous feedback inbox
- **Custom pages:** `WellbeingDashboardPage`
- **Widgets:** `WellbeingScoreWidget` — current company wellbeing score trend on HR dashboard
- **Nav group:** Employees (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Culture Amp | Employee engagement surveys |
| Peakon (Workday) | Pulse surveys and wellbeing tracking |
| Leapsome | Engagement and wellbeing |
| Officevibe | Team wellbeing and pulse surveys |

## Related

- [[employee-profiles]]
- [[employee-feedback]]
- [[time-attendance]]
- [[hr-analytics]]
