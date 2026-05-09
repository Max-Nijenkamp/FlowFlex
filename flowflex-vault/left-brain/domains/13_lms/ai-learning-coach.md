---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: planned
migration_range: 735000–739999
last_updated: 2026-05-09
---

# AI Learning Coach

Personalised, adaptive learning assistant that uses spaced repetition, retrieval practice, and learner performance data to maximise knowledge retention and close skills gaps faster.

**Panel:** `lms`  
**Phase:** 7  
**Migration range:** `735000–739999`

---

## Features

### Core (MVP)

- Spaced repetition engine: surface knowledge checks at optimal intervals (SM-2 algorithm)
- Daily learning digest: 5–10 min personalised review of content the learner is forgetting
- Knowledge check generation: AI generates questions from course content
- Weak area detection: identify topics with consistently low quiz scores
- Learning streaks and nudges: notification reminders for consistency

### Advanced

- Adaptive learning paths: reorder or recommend content based on performance
- Conversational AI tutor: chat interface — ask questions about course content
- Concept explanation variants: re-explain a concept in a different way if learner struggles
- Pre-assessment: test knowledge before a course to skip mastered content

### AI-Powered

- Claude-powered tutor chat: `claude-opus-4-7` with course content as context window
- Question generation: extract key facts from lesson content → generate MCQ/short answer
- Gap prediction: forecast which learners are at risk of forgetting critical compliance content

---

## Data Model

```erDiagram
    learning_coach_sessions {
        ulid id PK
        ulid employee_id FK
        string session_type
        json content_reviewed
        decimal performance_score
        timestamp session_at
    }

    spaced_repetition_items {
        ulid id PK
        ulid employee_id FK
        ulid lesson_id FK
        float ease_factor
        integer interval_days
        integer repetition_count
        timestamp next_review_at
        timestamp last_reviewed_at
    }
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `LearnerRetentionRisk` | Predicted knowledge decay | Notifications (learner nudge) |
| `WeakAreaIdentified` | Low score pattern detected | LMS (suggest remedial content) |

### Consumed

| Event | From | Action |
|---|---|---|
| `CourseCompleted` | LMS | Start spaced repetition schedule for that course |
| `QuizFailed` | LMS | Trigger immediate remedial review session |

---

## Permissions

```
lms.ai-coach.use
lms.ai-coach.configure
lms.ai-coach.view-analytics
```

---

## Related

- [[MOC_LMS]]
- [[course-builder-lms]] — source of content for retrieval practice
- [[skills-matrix]] — coach targets skill gaps
- [[entity-employee]]
