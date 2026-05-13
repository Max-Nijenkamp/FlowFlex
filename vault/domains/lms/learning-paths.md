---
type: module
domain: Learning & Development
panel: lms
module-key: lms.learning-paths
status: planned
color: "#4ADE80"
---

# Learning Paths

> Curated sequences of courses that guide learners toward a specific skill or certification outcome.

**Panel:** `lms`
**Module key:** `lms.learning-paths`

---

## What It Does

Learning Paths allow L&D administrators to compose ordered sequences of courses into structured programmes — for example a "New Manager" path covering leadership fundamentals, conflict resolution, and performance review skills. Learners enrol in the path and progress through each course in sequence, with prerequisites enforced automatically. The module tracks overall path completion separately from individual course progress and can issue a path-level certificate on completion.

---

## Features

### Core
- Learning path creation: name, description, cover image, estimated total hours
- Ordered course sequence: drag-and-drop course ordering within the path
- Prerequisite enforcement: next course unlocks only after previous is completed
- Path enrollment: manual, auto-rule-based (same rules as courses), or admin-bulk
- Path progress tracking: percentage complete across all courses
- Path-level certificate issuance on full completion

### Advanced
- Optional vs required courses within a path (some steps may be elective)
- Path versioning: publish a new version while active enrollees continue on the old one
- Path categories and tags for browsing (role-based, skill-based, compliance)
- Scheduled cohort paths: all learners start on the same date and progress together
- Expiry: paths can have a validity window; stale completions require re-enrolment

### AI-Powered
- AI path recommendation: suggest a relevant learning path based on skill gap analysis
- Adaptive sequencing: reorder optional courses based on learner's quiz performance
- Completion likelihood scoring: flag learners at risk of abandoning mid-path

---

## Data Model

```erDiagram
    learning_paths {
        ulid id PK
        ulid company_id FK
        string title
        string slug
        text description
        string cover_image_url
        integer estimated_minutes
        boolean is_published
        boolean generates_certificate
        timestamp published_at
        timestamp deleted_at
        timestamps created_at_updated_at
    }

    learning_path_steps {
        ulid id PK
        ulid path_id FK
        ulid course_id FK
        integer sort_order
        boolean is_required
    }

    learning_path_enrollments {
        ulid id PK
        ulid path_id FK
        ulid learner_id FK
        string status
        integer progress_percent
        timestamp enrolled_at
        timestamp completed_at
    }

    learning_paths ||--o{ learning_path_steps : "contains"
    learning_paths ||--o{ learning_path_enrollments : "tracks"
    learning_path_steps }o--|| courses : "references"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `learning_paths` | Path container | `id`, `company_id`, `title`, `is_published` |
| `learning_path_steps` | Ordered course list | `id`, `path_id`, `course_id`, `sort_order`, `is_required` |
| `learning_path_enrollments` | Learner path progress | `id`, `path_id`, `learner_id`, `status`, `progress_percent` |

---

## Permissions

```
lms.learning-paths.view-any
lms.learning-paths.create
lms.learning-paths.update
lms.learning-paths.delete
lms.learning-paths.enroll-learners
```

---

## Filament

- **Resource:** `App\Filament\Lms\Resources\LearningPathResource`
- **Pages:** `ListLearningPaths`, `CreateLearningPath`, `EditLearningPath`, `ViewLearningPath`
- **Custom pages:** `LearnerPathViewPage` (learner-facing path progress UI)
- **Widgets:** `PathCompletionWidget`, `ActiveEnrollmentsWidget`
- **Nav group:** Catalog

---

## Displaces

| Feature | FlowFlex | Cornerstone | Docebo | TalentLMS |
|---|---|---|---|---|
| Ordered path sequences | Yes | Yes | Yes | Yes |
| AI path recommendations | Yes | No | No | No |
| Path-level certificates | Yes | Yes | Partial | No |
| Adaptive sequencing | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[courses]] — courses are the building blocks of a path
- [[certifications]] — paths can issue certifications on completion
- [[skills]] — paths are mapped to target skills
- [[compliance-training]] — compliance programmes can be structured as paths
- [[analytics]] — path completion rates and time-to-completion
