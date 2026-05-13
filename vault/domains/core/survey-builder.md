---
type: module
domain: Core Platform
panel: app
module-key: core.surveys
status: planned
color: "#4ADE80"
---

# Survey Builder

> A reusable drag-and-drop survey engine with logic branching, multi-channel distribution, NPS/CSAT scoring, and AI theme extraction — one builder for every domain that needs structured feedback.

**Panel:** `/app`
**Module key:** `core.surveys`

## What It Does

Survey Builder is a shared Core Platform service that any other FlowFlex domain can invoke to collect structured feedback. Rather than each domain building its own survey mechanism (HR for pulse surveys, CS for NPS, Marketing for campaign feedback), all survey functionality is centralised here. The builder supports all common question types including logic branching, distributes surveys via shareable link, email embed, website popup, or post-ticket automation, and gives every survey a dedicated analytics view with completion rates, score distributions, and AI-extracted themes from open text responses.

## Features

### Core
- Survey creation: create a named survey with a type tag (NPS / CSAT / custom), an optional close date, and an intro and outro message
- Question types: NPS scale (0–10 with Promoter/Passive/Detractor auto-classification), Likert rating (1–5 stars), multiple choice (single or multi-select), checkbox list, short text, long text, date picker, and file upload (PDF/image, max 10MB)
- Logic branching: for any question, define visibility conditions — "show this question only if answer to question X equals Y" — supports up to 3 logic rules per question; evaluated client-side in real time as respondent progresses
- Question position ordering: drag-and-drop question ordering in the builder; questions are stored with integer `position` field
- Required vs optional: each question can be marked required — the survey cannot be submitted with required questions unanswered
- Shareable link distribution: every survey gets a public URL (`/survey/{ulid}`) — the respondent form is a Vue 3 + Inertia page requiring no login; responses are attributed to a contact if an `email` parameter is appended to the URL (e.g. from an email campaign link)
- Email embed distribution: generate an inline HTML snippet showing the first NPS or rating question directly in the email body — clicking a score on the email opens the full survey pre-populated with that score; compatible with major email clients including Outlook

### Advanced
- Website popup trigger: embed a script on any page that shows the survey as a popup modal after a configurable delay or scroll depth — same proactive trigger logic as Live Chat Widget; one activation per visitor per 30 days
- Post-support-ticket automation: configure a survey to automatically send to a contact 30 minutes after their support ticket is marked resolved — uses the notification/email system; CSAT surveys are the primary use case
- Response analytics: per survey, show completion rate (started vs completed), average NPS/CSAT score, score distribution histogram, per-question response breakdown, and median completion time
- Respondent list: a paginated list of all respondents with their score, completion status, and submission timestamp — clickable to see individual responses
- Segment filtering: filter analytics by respondent attributes (plan tier, industry, account age) if the respondent was identified via contact email link
- CSV/JSON export: export all responses (anonymised or with contact details) for external analysis
- Multi-language support: survey questions and intro/outro messages can be translated into multiple languages — the respondent sees the survey in their browser language if a translation exists, falling back to the default language

### AI-Powered
- Open text sentiment analysis: all long-text and short-text answers are processed by GPT-4o — classified as positive / neutral / negative and tagged with up to 3 themes from the domain taxonomy; sentiment and themes are stored on `survey_answers.ai_sentiment` and in a `survey_answer_themes` table
- Theme word cloud: on the survey analytics page, a tag cloud displays extracted themes coloured by sentiment dominance (green = mostly positive mentions, red = mostly negative, grey = neutral) with frequency count — clicking a theme filters the respondent list to show only responses containing that theme
- NPS driver analysis: for surveys with both an NPS question and open text, AI identifies the themes most correlated with Promoters (score 9–10) and Detractors (score 0–6) — surfaces as "What Promoters say" vs "What Detractors say" panels in analytics

## Data Model

```erDiagram
    surveys {
        ulid id PK
        ulid company_id FK
        string name
        enum type
        enum status
        timestamp closes_at "nullable"
        text intro_message
        text outro_message
        boolean allow_anonymous
        timestamps created_at/updated_at
    }

    survey_questions {
        ulid id PK
        ulid survey_id FK
        enum type
        string text
        json options "nullable"
        boolean is_required
        json logic_conditions "nullable"
        integer position
        timestamps created_at/updated_at
    }

    survey_responses {
        ulid id PK
        ulid survey_id FK
        ulid respondent_contact_id FK "nullable"
        string respondent_email "nullable"
        boolean is_complete
        timestamp started_at
        timestamp completed_at "nullable"
        integer score "nullable — for NPS/CSAT top-level score"
        string source
        timestamps created_at/updated_at
    }

    survey_answers {
        ulid id PK
        ulid response_id FK
        ulid question_id FK
        json value
        enum ai_sentiment "nullable"
        timestamps created_at/updated_at
    }

    survey_answer_themes {
        ulid id PK
        ulid answer_id FK
        string theme
        timestamps created_at/updated_at
    }

    survey_distributions {
        ulid id PK
        ulid survey_id FK
        enum channel
        json config
        enum trigger_type
        json trigger_config "nullable"
        boolean is_active
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `surveys.type` | enum: `nps` / `csat` / `pulse` / `product_feedback` / `event_registration` / `custom` |
| `surveys.status` | enum: `draft` / `active` / `paused` / `closed` |
| `survey_questions.type` | enum: `nps` / `rating` / `multiple_choice` / `checkbox` / `short_text` / `long_text` / `date` / `file_upload` |
| `survey_questions.logic_conditions` | JSON: `[{source_question_id, operator, value}]` — operator: `equals` / `not_equals` / `contains` / `greater_than` / `less_than` |
| `survey_responses.score` | For NPS surveys: the NPS question score (0–10) — denormalised for fast NPS calculation without joining answers |
| `survey_responses.source` | String: `link` / `email_embed` / `popup` / `post_ticket` / `api` |
| `survey_distributions.channel` | enum: `link` / `email_embed` / `website_popup` / `post_ticket` / `api` |
| `survey_answers.value` | JSON — format varies by question type: `{"score": 8}` for NPS, `{"selected": ["A","B"]}` for checkbox, `{"text": "..."}` for open text |

## Permissions

```
core.surveys.view-surveys
core.surveys.manage-surveys
core.surveys.view-responses
core.surveys.export-responses
core.surveys.manage-distributions
```

## Filament

- **Custom page:** `SurveyBuilderPage` — the primary creation and editing interface; a custom Filament page with a live preview panel on the right and the question builder on the left; questions are added from a type picker, each rendered as an editable card; drag-and-drop reordering via Livewire sort handles; logic rule builder is a modal triggered from the question card overflow menu
- **Resource:** `SurveyResource` — list of all surveys with columns: name, type badge, status, response count, completion rate, created at; row actions: Edit (opens SurveyBuilderPage), View Results (opens SurveyAnalyticsPage), Duplicate, Archive
- **Resource:** `SurveyResponseResource` — read-only list of individual responses for a selected survey; filterable by NPS score band (Promoters/Passives/Detractors), completion status, date range, and theme tag; clicking a response opens a modal showing all answers
- **Custom page:** `SurveyAnalyticsPage` — scoped to a survey; sections: KPI row (response count, completion rate, NPS score gauge or CSAT score), score distribution bar chart, per-question breakdown, AI theme tag cloud, NPS driver analysis panels (Promoters say / Detractors say)
- **Nav group:** Core (app panel)
- **Cross-domain access:** Other domains (HR Pulse Surveys, CS, Marketing) render their survey-specific UIs but call the core `Survey` and `SurveyResponse` models — they do not maintain their own survey data tables

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Typeform | Survey builder with logic branching and beautiful forms |
| SurveyMonkey | Survey creation, distribution, and response analytics |
| Google Forms | Simple survey builder for internal use |
| Delighted | NPS tracking with driver analysis |
| Qualtrics (SMB) | Multi-question survey engine with branching |
| Jotform | Form and survey builder with file upload support |

## Related

- [[notifications]]
- [[../hr/pulse-surveys]]
- [[../marketing/email-marketing]]
- [[../support/INDEX]]
- [[../crm/contacts]]

## Implementation Notes

### Shared Core Service Architecture
Survey Builder is a Core Platform module — its `Survey`, `SurveyQuestion`, `SurveyResponse`, and `SurveyAnswer` models are in `App\Models\Core\`. Other domains do not duplicate these models. The HR Pulse Surveys module (hr.pulse-surveys) wraps Survey Builder — it creates `surveys` records with `type = 'pulse'` and adds its own scheduling, anonymity, and action plan layer on top. The Survey Builder is not aware of pulse-specific concepts — that business logic lives in the HR domain.

The `SurveyBuilderPage` is a Core Platform page. Domain-specific survey UIs (e.g. the Pulse Survey builder in HR) are either custom pages in their own domain that instantiate the builder component, or they use the core page directly with a domain-scoped filter.

### Email Embed Technical Implementation
The email embed snippet is a table-based HTML block (for Outlook compatibility) containing the NPS or CSAT question options as anchor tags pointing to `/survey/{ulid}?score={n}&email={encoded_email}`. When the respondent clicks a score in the email, the landing page pre-populates that answer and shows the remaining questions. The email body never contains JavaScript — only standard HTML links.

The email embed is generated for the first question only (the score question). This is intentional: getting the score in-email dramatically improves response rates because it requires one click before the respondent even reaches the survey page.

### Logic Branching Evaluation
Logic conditions are evaluated client-side in the Vue 3 survey form, not server-side. The `logic_conditions` JSON is included in the survey definition served by the public API. On each answer change, the client re-evaluates all downstream question conditions and shows/hides questions accordingly. Hidden questions are not submitted with the response — only visible, completed questions produce `survey_answers` records.

Server-side validation on submission checks that all required questions (after applying the same logic branch evaluation server-side) have answers — preventing form manipulation from submitting incomplete responses.

### AI Processing Queue
Open-text answer AI processing is queued as a `ProcessSurveyAnswersAiJob` dispatched after each completed response. The job processes all open-text answers in a single GPT-4o call (batch prompt) to minimise API cost. The system prompt defines the theme taxonomy (30 predefined themes + "other"). If the response has no open-text answers, the job is skipped. Target processing latency: completed within 60 seconds of response submission so themes appear on the analytics page within a minute for live monitoring.
