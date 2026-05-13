---
type: module
domain: HR & People
panel: hr
module-key: hr.pulse-surveys
status: planned
color: "#4ADE80"
---

# Pulse Surveys

> Short recurring surveys, eNPS tracking, and AI sentiment analysis — HR gets a continuous real-time signal on employee engagement without waiting for the annual engagement survey.

**Panel:** `/hr`
**Module key:** `hr.pulse-surveys`

## What It Does

Pulse Surveys replaces ad-hoc employee feedback tools with a structured, always-on system for measuring engagement, wellbeing, and manager effectiveness. HR creates short surveys (3–10 questions) from a pre-built question library or from scratch, schedules them to run on a weekly, monthly, or quarterly cadence, and receives aggregated results with AI-extracted themes from open text responses. Anonymous mode protects individual privacy while still enabling meaningful department-level breakdowns — with a hard minimum anonymity threshold of 5 responses before any group breakdown is shown.

## Features

### Core
- Survey builder: create surveys with 3–10 questions; choose from question types: NPS scale (0–10), Likert scale (1–5), multiple choice, and open text
- Question library: pre-built questions across six categories — Employee Engagement, Wellbeing, Manager Effectiveness, D&I, Work-Life Balance, and Company Culture — drag in any combination to build a survey in seconds
- Scheduling: set a recurrence cadence (weekly / bi-weekly / monthly / quarterly) with a start date — the system automatically generates and dispatches each survey run
- Anonymous vs attributed: per survey, configure whether responses are fully anonymous (no employee_id stored), attributed (employee linked to response), or opt-in anonymous (employee chooses at submission time)
- eNPS tracking: a dedicated eNPS question ("How likely are you to recommend this company as a place to work? 0–10") is auto-computed into Promoters (9–10), Passives (7–8), and Detractors (0–6) with the net score displayed as a gauge

### Advanced
- Response rate tracking: per survey run, show response rate by department — target response rate (default 70%) is configurable per company; departments below target are highlighted
- Trend charts: time-series line chart of mean scores per question across survey runs — HR can see whether engagement is improving or declining over time
- Manager-level breakdowns: for companies that want to give managers visibility into their own team's results, show team-level aggregations — **minimum anonymity threshold: 5 responses required** before any breakdown is shown; suppressed if fewer than 5 to prevent de-anonymisation
- Action plan builder: after reviewing results, HR or a manager can create an action plan linked to a survey run — action items with owner, due date, and status (open / in progress / complete) tracked within the module
- Notification reminders: automated reminder emails sent at 24h and 48h before survey close to employees who have not yet responded — configurable on/off per survey
- Distribution targeting: target surveys at all employees, a specific department, a specific location, or a custom employee segment (uses HR employee data)

### AI-Powered
- Sentiment analysis on open text: all text answers are processed by OpenAI GPT-4o, classified as positive / neutral / negative, and key themes are extracted (e.g. "workload", "recognition", "remote work support") — themes are surfaced in the analytics view as a tag cloud with frequency count and sentiment colour
- Theme trend detection: AI compares themes across consecutive survey runs and highlights themes that are newly emerging or rapidly increasing in frequency — surfaced as a "Watch" alert on the analytics dashboard
- Action suggestion: given the top negative themes from a survey run, AI suggests 2–3 specific HR actions drawn from a curated playbook (e.g. theme = "unclear career path" → suggestion = "schedule career development conversations with all P2 employees") — HR can accept as action plan items with one click

## Data Model

```erDiagram
    hr_pulse_surveys {
        ulid id PK
        ulid company_id FK
        string name
        enum frequency
        boolean is_anonymous
        enum anonymity_mode
        enum status
        timestamp next_run_at
        integer min_responses_threshold
        timestamps created_at/updated_at
    }

    hr_pulse_questions {
        ulid id PK
        ulid survey_id FK
        string text
        enum type
        json options
        integer position
        boolean is_required
        timestamps created_at/updated_at
    }

    hr_pulse_responses {
        ulid id PK
        ulid survey_id FK
        ulid employee_id FK "nullable if anonymous"
        ulid survey_run_id FK
        timestamp submitted_at
        timestamps created_at/updated_at
    }

    hr_pulse_answers {
        ulid id PK
        ulid response_id FK
        ulid question_id FK
        integer score "nullable"
        text text_answer "nullable"
        enum ai_sentiment "nullable"
        timestamps created_at/updated_at
    }

    hr_pulse_survey_runs {
        ulid id PK
        ulid survey_id FK
        date run_date
        integer total_invited
        integer total_responded
        timestamp closed_at
        timestamps created_at/updated_at
    }

    hr_pulse_action_plans {
        ulid id PK
        ulid survey_run_id FK
        ulid company_id FK
        string title
        text description
        timestamps created_at/updated_at
    }

    hr_pulse_action_items {
        ulid id PK
        ulid action_plan_id FK
        string title
        ulid owner_id FK
        date due_date
        enum status
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `hr_pulse_surveys.frequency` | enum: `weekly` / `bi_weekly` / `monthly` / `quarterly` / `once` |
| `hr_pulse_surveys.anonymity_mode` | enum: `anonymous` / `attributed` / `opt_in` |
| `hr_pulse_surveys.min_responses_threshold` | Default 5 — breakdowns suppressed below this count |
| `hr_pulse_questions.type` | enum: `nps` / `likert` / `multiple_choice` / `open_text` |
| `hr_pulse_answers.ai_sentiment` | enum: `positive` / `neutral` / `negative` — populated by background job after submission |
| `hr_pulse_responses.employee_id` | NULL when survey is fully anonymous — never stored; when opt_in, stored if employee chose attributed |

## Permissions

```
hr.pulse-surveys.view-results
hr.pulse-surveys.manage-surveys
hr.pulse-surveys.view-team-results
hr.pulse-surveys.manage-action-plans
hr.pulse-surveys.export-results
```

## Filament

- **Resource:** `PulseSurveyResource` — standard CRUD for managing surveys and their question sets; includes a question builder with drag-and-drop position ordering and a question library picker modal
- **Custom page:** `PulseSurveyResultsPage` — scoped to a specific survey; tabs for each survey run showing: overall response rate progress bar, eNPS gauge (if eNPS question included), per-question score distribution (horizontal bar charts), open text answers with sentiment badges, AI theme word cloud (tag cloud coloured by sentiment — green=positive, red=negative, grey=neutral), trend line chart across all runs
- **Widget:** `EngagementPulseWidget` on HR dashboard — shows latest eNPS score, most recent response rate, and one highlight (top positive or top concern theme)
- **Nav group:** Analytics (hr panel)
- **Background job:** `ProcessPulseSurveyAiJob` — runs after each survey response batch closes; calls OpenAI GPT-4o in batches of 50 text answers; writes `ai_sentiment` and extracted themes back to `hr_pulse_answers`

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Lattice Engagement | Pulse surveys, eNPS, action plans |
| Culture Amp | Employee surveys, theme analysis, manager breakdowns |
| Peakon (Workday) | Continuous listening and engagement analytics |
| Glint (LinkedIn) | Employee engagement surveys and sentiment trends |
| Leapsome | Pulse surveys and manager effectiveness measurement |

## Related

- [[employee-profiles]]
- [[employee-wellbeing]]
- [[employee-feedback]]
- [[hr-analytics]]
- [[dei-metrics]]

## Implementation Notes

### Anonymity Architecture
When a survey is configured as fully anonymous, the system must never store `employee_id` on `hr_pulse_responses`. The scheduling job sends individualised email links via a one-time token (`hr_survey_tokens` table with `employee_id`, `survey_run_id`, `used_at`) — the token is consumed on submission and the `employee_id` is never written to the response record. This prevents backtracking from response to employee even with direct database access.

For opt-in anonymous mode, the form presents a checkbox "Submit anonymously" — if checked, `employee_id` is null; if unchecked, it is stored. The UI makes the anonymous default clear so employees feel safe responding candidly.

### AI Processing
GPT-4o is called after the survey run closes (not in real time on submission). Use the batch embeddings endpoint to reduce cost. Each text answer is processed with a system prompt asking for: (1) sentiment classification (positive/neutral/negative), (2) up to 3 theme tags from a constrained taxonomy (20 predefined HR themes + "other"). Themes are stored as a separate `hr_pulse_answer_themes` table (answer_id FK, theme string) rather than in JSON to allow efficient aggregation queries.

The theme taxonomy must be versioned — if FlowFlex updates the theme list, historical answers retain their original theme tags.

### Minimum Anonymity Threshold
The 5-response threshold for group breakdowns must be enforced at the query layer, not just the UI layer, to prevent direct API access from revealing individual data. Any query that groups responses by department, manager, or team must have a `HAVING COUNT(*) >= {threshold}` clause applied automatically via a query scope on `PulseSurveyResponse`.
