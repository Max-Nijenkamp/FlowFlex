---
domain: lms
module: lessons
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Lessons & Content

Individual lessons within a course module: video, text, file attachments, and quizzes.

## Module-key

| Field | Value |
|---|---|
| key | `lms.lessons` |
| priority | p3 |
| panel | lms |
| permission-prefix | `lms.lessons` |
| tables | `lms_lessons`, `lms_quizzes`, `lms_lesson_progress` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../courses/_module\|Courses]] | Lessons live in course modules |
| Hard | [[../../core/billing/_module\|Billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|RBAC]] | Permissions |
| Hard | [[../../core/file-storage/_module\|File Storage]] | Video / file uploads |

## Core Features

- **Lesson record** — title, module, type, content, order, duration.
- **Lesson types** — video (upload or whitelisted embed), text/article (Tiptap, purified), file/download, quiz (SCORM later *(assumed)*).
- **Completion criteria** — per lesson: viewed or quiz-passed.
- **Quizzes** — multiple choice / true-false, passing score, max attempts *(assumed: unlimited default)*; graded server-side.
- **Progress tracking** — per enrolment per lesson.
- **Downloadable resources** — per lesson.

## See features/

- [[features/lesson-content|Lesson Content]] — authoring lessons by type (relation manager on courses).
- [[features/quizzes|Quizzes]] — quiz authoring + server-side grading (custom-page).

## Build Manifest

```
database/migrations/xxxx_create_lms_lessons_table.php
database/migrations/xxxx_create_lms_quizzes_table.php
database/migrations/xxxx_create_lms_lesson_progress_table.php
app/Models/LMS/{Lesson,Quiz,LessonProgress}.php
app/Data/LMS/{CreateLessonData,SubmitQuizData,QuizResult}.php
app/Services/LMS/{LessonProgressService,QuizService}.php
database/factories/LMS/{LessonFactory,QuizFactory}.php
tests/Feature/LMS/{LessonTest,QuizGradingTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's lessons data
- [ ] Module gating: artifacts hidden when `lms.lessons` inactive
- [ ] Content schema per type enforced; non-whitelisted embed rejected.
- [ ] Quiz graded server-side; correct answers never serialized to client.
- [ ] Pass below `passing_score` = lesson not complete (quiz-passed criteria).
- [ ] Progress unique per `(lesson, enrolment)`; best score kept.
- [ ] Video served via signed URL.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none)* | — | Progress → completion is a same-domain call into enrolments |
| Reads | file-storage signed URLs | core.files | Video/file upload + streamed serve |
| Commands | `EnrolmentService::recomputeProgress` | lms.enrolments | On lesson complete, recompute enrolment progress (same-domain direct call) |

**Data ownership:** `lms.lessons` writes only `lms_lessons`, `lms_quizzes`, `lms_lesson_progress`. On completion it calls `EnrolmentService::recomputeProgress` (enrolments' own service writes `lms_enrolments`), never writing another table directly ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../courses/_module|Courses]] · [[../enrolments/_module|Enrolments]]
- [[../_index|LMS index]]
