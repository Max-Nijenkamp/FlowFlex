---
type: module
domain: Learning & Development
panel: lms
cssclasses: domain-lms
phase: 7
status: complete
migration_range: 480001–480003
last_updated: 2026-05-12
right_brain_log: "[[builder-log-lms-phase7]]"
---

# Course Builder & LMS

Block-based course editor with video hosting, quizzes, branching paths, and completion tracking. FlowFlex replaces standalone LMS tools for organisations that want HR, compliance training, and learning in one platform.

**Panel:** `lms`  
**Phase:** 7  
**Migration range:** `700000–709999`

---

## Features

### Core (MVP)

- Block editor: text, video (hosted + YouTube/Vimeo embed), image, file attachment, quiz
- Course structure: sections → lessons → blocks
- Draft/publish workflow with version history
- Learner enrollment: manual, auto (based on job title/team/department), rule-based
- Completion tracking: per-lesson and per-course progress
- Pass/fail threshold configuration per course
- Certificate generation on course completion (PDF, branded)
- Mobile-responsive learner view

### Advanced

- Branching paths: conditional next-lesson based on quiz score or selection
- Learning paths: ordered sequence of multiple courses
- Prerequisite courses (must complete A before B)
- Cohort-based learning: groups of learners progress together
- Peer review assignments: submit work, another learner reviews
- Discussion threads per lesson

### AI-Powered

- AI course outline generator: input topic → generates section and lesson structure
- Auto-subtitle generation for uploaded videos
- Quiz question generator from lesson content
- AI learning coach integration (see [[ai-learning-coach]])

---

## Data Model

```erDiagram
    courses {
        ulid id PK
        ulid company_id FK
        string title
        string slug
        text description
        string cover_image_url
        integer estimated_minutes
        boolean is_published
        integer pass_threshold_percent
        boolean generates_certificate
        timestamp published_at
        softDeletes deleted_at
        timestamps timestamps
    }

    course_sections {
        ulid id PK
        ulid course_id FK
        string title
        integer sort_order
    }

    course_lessons {
        ulid id PK
        ulid section_id FK
        string title
        json blocks
        integer sort_order
        integer estimated_minutes
    }

    course_enrollments {
        ulid id PK
        ulid course_id FK
        ulid learner_id FK
        string status
        integer progress_percent
        decimal score
        boolean passed
        timestamp enrolled_at
        timestamp completed_at
    }

    courses ||--o{ course_sections : "has"
    course_sections ||--o{ course_lessons : "has"
    courses ||--o{ course_enrollments : "has"
```

### Tables

| Table | Purpose | Key Columns |
|---|---|---|
| `courses` | Course container | `id`, `company_id`, `title`, `is_published` |
| `course_sections` | Chapter grouping | `id`, `course_id`, `title`, `sort_order` |
| `course_lessons` | Individual lessons | `id`, `section_id`, `blocks` (JSON) |
| `course_enrollments` | Learner progress | `id`, `course_id`, `learner_id`, `status`, `progress_percent` |

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `CourseCompleted` | Learner passes course | HR (update skills), Notifications (certificate) |
| `CourseEnrollmentCreated` | Learner enrolled | Notifications (welcome email) |
| `CoursePublished` | Admin publishes course | Notifications (enrolled learners) |

### Consumed

| Event | From | Action |
|---|---|---|
| `EmployeeHired` | HR | Auto-enrol in onboarding learning path |
| `CertificationExpired` | HR | Auto-enrol in renewal course |
| `JobTitleChanged` | HR | Trigger role-based re-enrollment check |

---

## Permissions

```
lms.courses.view-any
lms.courses.view
lms.courses.create
lms.courses.update
lms.courses.delete
lms.courses.publish
lms.courses.enroll-learners
lms.courses.view-completions
```

---

## Filament Resources

- `App\Filament\Lms\Resources\CourseResource`
- Pages: `ListCourses`, `CreateCourse`, `EditCourse`, `ViewCourse`
- Widgets: `CourseCompletionWidget`, `EnrollmentStatsWidget`

---

## Competitors Displaced

| Feature | FlowFlex | Docebo | TalentLMS |
|---|---|---|---|
| Block editor | ✅ | ✅ | ❌ |
| AI course builder | ✅ | ❌ | ❌ |
| Native HR integration | ✅ | ❌ | ❌ |
| Included in platform | ✅ | $$ | $$ |

---

## Related

- [[MOC_LMS]] — parent domain
- [[entity-employee]] — learner profile
- [[scorm-xapi-support]] — import existing SCORM content
- [[ai-learning-coach]] — AI-powered adaptive learning
- [[certification-compliance-training]] — mandatory training tracking
