---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: planned
migration_range: 725000–729999
last_updated: 2026-05-09
---

# Mentoring & Coaching

Structured mentoring programme management — mentor matching, session scheduling, goal setting, progress tracking, and programme effectiveness reporting.

**Panel:** `lms`  
**Phase:** 7  
**Migration range:** `725000–729999`

---

## Features

### Core (MVP)

- Mentor profiles: skills, availability, experience, capacity (max mentees)
- Mentee sign-up: development goals, preferred mentor attributes
- Matching algorithm: suggest mentors based on goals + skills + availability
- Session scheduling: calendar integration, recurring session reminders
- Session notes: structured per-session notes (goals discussed, actions agreed)
- Goal tracking: mentee development goals with progress check-ins
- Programme dashboard: active pairs, session frequency, completion rate

### Advanced

- Flash mentoring: single-session expert conversations (no long-term commitment)
- Group coaching circles: one coach → group of 4–6 mentees
- 360 feedback mid-programme: mentee and mentor rate each other
- Programme cohorts: organised by department, role level, or new hire status

### AI-Powered

- Smart matching: ML-based mentor recommendation beyond simple tag matching
- Session summary AI: summarise session notes, extract action items

---

## Data Model

```erDiagram
    mentor_profiles {
        ulid id PK
        ulid employee_id FK
        json expertise_tags
        integer max_mentees
        boolean is_available
    }

    mentoring_pairs {
        ulid id PK
        ulid mentor_id FK
        ulid mentee_id FK
        string status
        date start_date
        date end_date
        text goals
    }

    mentoring_sessions {
        ulid id PK
        ulid pair_id FK
        date session_date
        integer duration_minutes
        text notes
        text actions
    }

    mentor_profiles ||--o{ mentoring_pairs : "mentors"
    mentoring_pairs ||--o{ mentoring_sessions : "has"
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `MentoringPairCreated` | Match made | Notifications (mentor + mentee intro) |
| `MentoringSessionCompleted` | Session logged | LMS (log development activity) |

### Consumed

| Event | From | Action |
|---|---|---|
| `EmployeeHired` | HR | Add to new-hire mentoring programme if configured |
| `SkillGapIdentified` | LMS | Suggest mentors with that skill |

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

## Related

- [[MOC_LMS]]
- [[skills-matrix]] — gaps trigger mentor suggestions
- [[succession-planning]] — mentoring as development activity for candidates
- [[entity-employee]]
