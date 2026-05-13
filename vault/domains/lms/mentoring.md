---
type: module
domain: Learning & Development
panel: lms
module-key: lms.mentoring
status: planned
color: "#4ADE80"
---

# Mentoring

> Structured mentor–mentee matching, session scheduling, progress notes, and goal tracking within formal mentoring programmes.

**Panel:** `lms`
**Module key:** `lms.mentoring`

---

## What It Does

Mentoring enables organisations to run structured internal mentoring programmes without external tools. Mentors register with their skills and availability; mentees sign up with development goals. The system matches pairs based on skills, availability, and stated preferences, then supports the ongoing relationship with session scheduling, structured note-taking, and goal tracking. Programme administrators see aggregate dashboards covering active pairs, session frequency, and overall programme effectiveness.

---

## Features

### Core
- Mentor profiles: expertise tags, availability windows, maximum simultaneous mentees
- Mentee sign-up: development goals, preferred mentor attributes, availability
- Matching suggestions: system-generated mentor recommendations based on goals and skills
- Session scheduling: record planned sessions with date, time, and duration
- Session notes: structured per-session capture (topics covered, actions agreed)
- Goal tracking: mentee goals with milestone check-ins and status updates
- Programme dashboard: active pairs, session frequency, goal completion rate

### Advanced
- Flash mentoring: single-session expert conversations with no long-term commitment
- Group coaching circles: one mentor/coach working with a small group simultaneously
- Mid-programme feedback: mentor and mentee rate each other at the midpoint
- Programme cohorts: organise mentoring by department, seniority level, or new-hire status
- Expiry and closure: formal programme end with final feedback and outcome recording

### AI-Powered
- Smart matching: ML-based mentor recommendation accounting for subtle goal-skill alignment
- Session summary: AI summarises session notes and extracts action items
- Skill gap linkage: identify mentors whose strengths match a mentee's assessed skill gaps

---

## Data Model

```erDiagram
    mentor_profiles {
        ulid id PK
        ulid employee_id FK
        ulid company_id FK
        json expertise_tags
        integer max_mentees
        boolean is_available
        timestamps created_at_updated_at
    }

    mentoring_pairs {
        ulid id PK
        ulid mentor_id FK
        ulid mentee_id FK
        ulid company_id FK
        string status
        date start_date
        date end_date
        text goals
        timestamps created_at_updated_at
    }

    mentoring_sessions {
        ulid id PK
        ulid pair_id FK
        date session_date
        integer duration_minutes
        text notes
        text actions
        timestamps created_at_updated_at
    }

    mentor_profiles ||--o{ mentoring_pairs : "mentors in"
    mentoring_pairs ||--o{ mentoring_sessions : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `mentor_profiles` | Mentor registrations | `id`, `employee_id`, `expertise_tags`, `max_mentees`, `is_available` |
| `mentoring_pairs` | Active relationships | `id`, `mentor_id`, `mentee_id`, `status`, `start_date`, `goals` |
| `mentoring_sessions` | Session records | `id`, `pair_id`, `session_date`, `duration_minutes`, `notes`, `actions` |

---

## Permissions

```
lms.mentoring.view-any
lms.mentoring.manage-programmes
lms.mentoring.be-mentor
lms.mentoring.request-mentoring
lms.mentoring.log-sessions
```

---

## Filament

- **Resource:** `App\Filament\Lms\Resources\MentoringResource`
- **Pages:** `ListMentoringPairs`, `CreateMentoringPair`, `ViewMentoringPair`
- **Custom pages:** `MentorMatchingPage` (match wizard), `SessionLogPage`
- **Widgets:** `ActivePairsWidget`, `SessionFrequencyWidget`
- **Nav group:** Progress

---

## Displaces

| Feature | FlowFlex | Cornerstone | Docebo | TalentLMS |
|---|---|---|---|---|
| Mentor–mentee matching | Yes | Yes | No | No |
| Session note capture | Yes | No | No | No |
| AI smart matching | Yes | No | No | No |
| Group coaching circles | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[skills]] — skill gaps surface mentor recommendations
- [[learning-paths]] — mentoring can supplement a structured path
- [[courses]] — mentors can assign courses as development actions
- [[analytics]] — programme effectiveness metrics
