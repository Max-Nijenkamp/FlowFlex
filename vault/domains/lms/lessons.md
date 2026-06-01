---
type: module
domain: Learning & Development
panel: lms
module-key: lms.lessons
status: planned
color: "#4ADE80"
---

# Lessons & Content

Individual lessons within a course module: video, text, file attachments, and quizzes.

## Core Features

- Lesson record: title, module, type, content, order, duration
- Lesson types: video, text/article (Tiptap), file/download, quiz, embedded (Scorm later)
- Video: upload or embed (YouTube/Vimeo)
- Rich text content via Tiptap
- Quiz: questions (multiple choice, true/false), passing score
- Completion criteria: viewed, quiz passed, time spent
- Lesson progress tracking per learner
- Downloadable resources per lesson

## Data Model

| Table | Key Columns |
|---|---|
| `lms_lessons` | company_id, module_id, course_id, title, type, content (json), order, duration_minutes |
| `lms_quizzes` | lesson_id, company_id, questions (json), passing_score |
| `lms_lesson_progress` | company_id, lesson_id, enrolment_id, status, completed_at, quiz_score |

## Filament

**Nav group:** Courses

- Lesson management as relation manager on Course modules
- `awcodes/filament-tiptap-editor` for text lessons
- Quiz builder via Filament repeater

## Related

- [[domains/lms/courses]]
- [[domains/lms/enrolments]]
