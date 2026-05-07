---
tags: [flowflex, domain/lms, courses, learning, phase/7]
domain: Learning & Development
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-07
---

# Course Builder & LMS

Build and deliver courses. Track completion, award certificates, and gamify learning — all without leaving FlowFlex.

**Who uses it:** L&D team (build courses), all employees (take courses)
**Filament Panel:** `lms`
**Depends on:** [[HR — Employee Profiles]], [[File Storage]], Core
**Phase:** 7
**Build complexity:** High — 5 resources, 2 pages, 7 tables

---

## Features

- **Course builder** — create courses with thumbnail, description, category, level (beginner/intermediate/advanced), and an estimated duration; mark mandatory courses that all employees must complete
- **Module and lesson structure** — courses divided into modules; modules divided into lessons; each lesson has a type: video, text/rich content, quiz, SCORM, or PDF
- **Content types** — video (URL or S3-stored file), text (rich text editor), quiz (question bank), SCORM 1.2/2004 import for existing e-learning packages, PDF viewer
- **Quiz builder** — multiple choice, true/false, and short answer question types; configurable correct answers and point values; supports partial credit
- **Pass mark** — configurable per course; quiz attempts compared against pass mark; failed attempts can be retried up to a configurable limit
- **Course enrolment** — enrol employees manually, by role, or by department; `CourseAssigned` event fires on enrolment
- **Progress tracking** — lesson-level completion tracked; progress % computed; shown on employee's learning dashboard
- **Certificate generation** — on course completion, generate a PDF certificate using `certificate_template_file_id` branded template; `CertificateExpiring` alert if certificate has an `expiry_date`
- **`CourseCompleted` event** — fires on full course completion; consumed by [[HR Compliance]] to mark certification fulfilled and by [[Performance & Reviews]] to log development activity
- **Gamification** — points per lesson completion; leaderboard; badges on course completion; shown on employee intranet profile
- **SCORM import** — drag-and-drop SCORM package upload; content delivered inline via SCORM API; completion signals captured
- **Auto-enrolment triggers** — `EmployeeHired` → assign induction course; `CertificationExpired` → assign renewal course; `OnboardingStarted` → assign onboarding learning path

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `courses`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `description` | text nullable | |
| `category` | string nullable | |
| `level` | enum | `beginner`, `intermediate`, `advanced` |
| `status` | enum | `draft`, `published`, `archived` |
| `thumbnail_file_id` | ulid FK nullable | → files |
| `estimated_hours` | decimal(5,2) nullable | |
| `pass_mark` | integer default 80 | % |
| `is_mandatory` | boolean default false | |
| `certificate_template_file_id` | ulid FK nullable | → files |

### `course_modules`
| Column | Type | Notes |
|---|---|---|
| `course_id` | ulid FK | → courses |
| `title` | string | |
| `sort_order` | integer | |
| `is_required` | boolean default true | |

### `lessons`
| Column | Type | Notes |
|---|---|---|
| `course_module_id` | ulid FK | → course_modules |
| `title` | string | |
| `type` | enum | `video`, `text`, `quiz`, `scorm`, `pdf` |
| `content` | json nullable | video URL or rich text blocks |
| `file_id` | ulid FK nullable | → files (for PDF or video file) |
| `duration_minutes` | integer nullable | |
| `sort_order` | integer | |
| `is_required` | boolean default true | |

### `course_enrollments`
| Column | Type | Notes |
|---|---|---|
| `course_id` | ulid FK | → courses |
| `tenant_id` | ulid FK | → tenants |
| `status` | enum | `not_started`, `in_progress`, `completed`, `failed` |
| `enrolled_at` | timestamp | |
| `completed_at` | timestamp nullable | |
| `score` | integer nullable | final quiz score % |
| `progress_pct` | integer default 0 | |
| `certificate_issued_at` | timestamp nullable | |

### `quiz_questions`
| Column | Type | Notes |
|---|---|---|
| `lesson_id` | ulid FK | → lessons |
| `question` | text | |
| `type` | enum | `multiple_choice`, `true_false`, `short_answer` |
| `options` | json nullable | array of {label, is_correct} |
| `correct_answer` | string nullable | for short_answer |
| `points` | integer default 1 | |
| `sort_order` | integer | |

### `quiz_attempts`
| Column | Type | Notes |
|---|---|---|
| `course_enrollment_id` | ulid FK | → course_enrollments |
| `lesson_id` | ulid FK | → lessons |
| `answers` | json | array of {question_id, answer} |
| `score` | integer nullable | % |
| `passed` | boolean | |
| `attempted_at` | timestamp | |

### `certificates`
| Column | Type | Notes |
|---|---|---|
| `course_enrollment_id` | ulid FK | → course_enrollments |
| `tenant_id` | ulid FK | → tenants |
| `course_id` | ulid FK | → courses |
| `issued_at` | timestamp | |
| `file_id` | ulid FK nullable | → files (PDF) |
| `expiry_date` | date nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `CourseCompleted` | `enrollment_id`, `tenant_id`, `course_id` | [[HR Compliance]] (mark cert fulfilled), [[Performance & Reviews]] (log dev activity) |
| `CourseAssigned` | `enrollment_id`, `tenant_id` | Notification to employee |
| `CertificateExpiring` | `certificate_id`, `expiry_date` | Reminder notification to employee |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `EmployeeHired` | [[HR — Employee Profiles]] | Auto-enrol in mandatory induction course |
| `CertificationExpired` | [[HR Compliance]] | Auto-enrol in renewal course |
| `OnboardingStarted` | [[Onboarding]] | Assign onboarding learning path |

---

## Permissions

```
lms.courses.view
lms.courses.create
lms.courses.edit
lms.courses.delete
lms.courses.publish
lms.course-enrollments.view
lms.course-enrollments.create
lms.course-enrollments.delete
lms.quiz-questions.view
lms.quiz-questions.create
lms.quiz-questions.edit
lms.certificates.view
lms.certificates.issue
```

---

## Related

- [[LMS Overview]]
- [[Skills Matrix & Gap Analysis]]
- [[HR Compliance]]
- [[Onboarding]]
- [[External Training Requests]]
