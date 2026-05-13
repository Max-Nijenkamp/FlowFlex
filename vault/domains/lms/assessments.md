---
type: module
domain: Learning & Development
panel: lms
module-key: lms.assessments
status: planned
color: "#4ADE80"
---

# Assessments

> Standalone quizzes and tests with question banks, configurable scoring, and pass/fail thresholds decoupled from specific courses.

**Panel:** `lms`
**Module key:** `lms.assessments`

---

## What It Does

Assessments provides a reusable question bank and test engine that can be used both inside course lessons and as standalone evaluations. L&D teams build question banks categorised by topic or skill area, then compose assessments by drawing questions manually or at random. Assessments support multiple question types, time limits, multiple attempts, and automatic scoring. Results feed back into skill ratings and compliance records, giving a clear picture of learner knowledge without requiring course enrolment.

---

## Features

### Core
- Question bank: create and categorise questions by topic, skill, or domain
- Question types: multiple choice, true/false, multi-select, short text answer
- Assessment composition: select questions manually or draw randomly from a bank
- Time limits: optional countdown timer per assessment
- Pass/fail threshold: configurable minimum score to pass
- Multiple attempts: configure allowed retries and delay between attempts
- Automatic scoring: instant results for objective question types
- Attempt history: full record of each learner's attempts and scores

### Advanced
- Adaptive assessments: question difficulty adjusts based on running score
- Question weighting: assign different point values to different questions
- Randomised question order to reduce cheating in unproctored settings
- Assessment pools: maintain multiple variants of an assessment for different cohorts
- Manual grading workflow: short-text answers routed to a reviewer for scoring

### AI-Powered
- AI question generation: input a topic or paste lesson text to generate question suggestions
- Distractor generation: AI creates plausible wrong answers for multiple-choice questions
- Item analysis: flag questions where most learners answered incorrectly, suggesting unclear wording

---

## Data Model

```erDiagram
    assessments {
        ulid id PK
        ulid company_id FK
        string title
        text description
        integer time_limit_minutes
        integer pass_threshold_percent
        integer max_attempts
        boolean randomise_order
        timestamp deleted_at
        timestamps created_at_updated_at
    }

    questions {
        ulid id PK
        ulid company_id FK
        string topic
        string type
        text stem
        json options
        json correct_answers
        integer points
        timestamps created_at_updated_at
    }

    assessment_questions {
        ulid id PK
        ulid assessment_id FK
        ulid question_id FK
        integer sort_order
    }

    assessment_attempts {
        ulid id PK
        ulid assessment_id FK
        ulid learner_id FK
        json responses
        decimal score
        boolean passed
        integer attempt_number
        timestamp started_at
        timestamp submitted_at
    }

    assessments ||--o{ assessment_questions : "contains"
    questions ||--o{ assessment_questions : "used in"
    assessments ||--o{ assessment_attempts : "attempted"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `assessments` | Assessment definitions | `id`, `company_id`, `title`, `pass_threshold_percent`, `max_attempts` |
| `questions` | Question bank | `id`, `company_id`, `type`, `stem`, `options`, `correct_answers` |
| `assessment_questions` | Questions in assessment | `id`, `assessment_id`, `question_id`, `sort_order` |
| `assessment_attempts` | Learner attempts | `id`, `assessment_id`, `learner_id`, `score`, `passed`, `attempt_number` |

---

## Permissions

```
lms.assessments.view-any
lms.assessments.create
lms.assessments.update
lms.assessments.delete
lms.assessments.view-results
```

---

## Filament

- **Resource:** `App\Filament\Lms\Resources\AssessmentResource`
- **Pages:** `ListAssessments`, `CreateAssessment`, `EditAssessment`, `ViewAssessment`
- **Custom pages:** `AssessmentTakerPage` (learner-facing exam UI), `ResultsReviewPage`
- **Widgets:** `PassRateWidget`, `RecentAttemptsWidget`
- **Nav group:** Assessment

---

## Displaces

| Feature | FlowFlex | Cornerstone | Docebo | TalentLMS |
|---|---|---|---|---|
| Standalone question banks | Yes | Yes | Yes | Yes |
| AI question generation | Yes | No | No | No |
| Adaptive assessments | Yes | No | No | No |
| Item analysis | Yes | Yes | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[courses]] — assessments are embedded in course lessons
- [[skills]] — assessment results can update skill proficiency ratings
- [[compliance-training]] — compliance tests use assessment engine
- [[certifications]] — assessments can gate certification issuance
- [[analytics]] — pass rates and question performance data
