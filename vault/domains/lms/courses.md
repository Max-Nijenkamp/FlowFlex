---
type: module
domain: Learning & Development
panel: lms
module-key: lms.courses
status: planned
color: "#4ADE80"
---

# Courses

> Block-based course editor with video hosting, quizzes, branching paths, and completion tracking.

**Panel:** `lms`
**Module key:** `lms.courses`

---

## What It Does

Courses is the primary authoring and delivery engine of the LMS panel. Administrators and L&D teams build structured courses from sections and lessons containing rich content blocks — text, embedded video, file attachments, and inline quizzes. Learners enrol manually or automatically based on HR attributes such as job title or department, and progress is tracked per lesson through to course completion. Pass/fail thresholds and certificate generation are configurable per course.

---

## Features

### Core
- Block editor: text, video (hosted or YouTube/Vimeo embed), image, file attachment, quiz block
- Course structure: Sections → Lessons → Blocks
- Draft/publish workflow with published-at timestamp
- Pass/fail threshold configuration (percentage score required)
- Learner enrollment: manual, auto-by-rule (job title, department, team), or bulk import
- Completion tracking per lesson and per course with progress percentage
- Certificate PDF generation on completion (branded, downloadable)
- Mobile-responsive learner view

### Advanced
- Branching paths: conditional next-lesson routing based on quiz score or learner selection
- Prerequisite courses: enforce completion of Course A before Course B unlocks
- Cohort-based learning: groups of learners progress together on a shared schedule
- Peer-review assignments: learner submits work; another learner reviews and scores
- Discussion threads per lesson for async Q&A

### AI-Powered
- AI course outline generator: input a topic, receive a structured section/lesson plan
- Auto-subtitle generation for uploaded video content
- Quiz question generator derived from lesson text content
- Smart enrollment suggestions based on skill gap analysis from [[skills]]

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
        timestamp deleted_at
        timestamps created_at_updated_at
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
    courses ||--o{ course_enrollments : "tracks"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `courses` | Course container | `id`, `company_id`, `title`, `is_published`, `pass_threshold_percent` |
| `course_sections` | Chapter grouping | `id`, `course_id`, `title`, `sort_order` |
| `course_lessons` | Individual lessons | `id`, `section_id`, `blocks` (JSON), `sort_order` |
| `course_enrollments` | Learner progress | `id`, `course_id`, `learner_id`, `status`, `progress_percent`, `passed` |

---

## Permissions

```
lms.courses.view-any
lms.courses.create
lms.courses.update
lms.courses.delete
lms.courses.publish
```

---

## Filament

- **Resource:** `App\Filament\Lms\Resources\CourseResource`
- **Pages:** `ListCourses`, `CreateCourse`, `EditCourse`, `ViewCourse`
- **Custom pages:** `CourseBuilderPage` (block editor experience), `LearnerViewPage`
- **Widgets:** `CourseCompletionRateWidget`, `EnrollmentStatsWidget`
- **Nav group:** Catalog

---

## Displaces

| Feature | FlowFlex | Docebo | TalentLMS | Cornerstone |
|---|---|---|---|---|
| Block-based editor | Yes | Yes | No | No |
| AI outline generator | Yes | No | No | No |
| Native HR enrollment rules | Yes | No | No | Partial |
| Certificate generation | Yes | Yes | Yes | Yes |
| Included in platform price | Yes | No | No | No |

---

## Implementation Notes

**Filament:** `CourseBuilderPage` is a full custom `Page` — the block editor experience cannot be built with standard Filament form fields. The block editor renders an ordered list of blocks stored in `course_lessons.blocks` (JSON). Each block type (text, video, quiz, image, file) is a Livewire component. The builder uses SortableJS for drag-to-reorder blocks within a lesson. `LearnerViewPage` is a separate Vue 3 + Inertia page (not a Filament page) — the tech-stack decision table specifies "Learner portal (LMS) — Vue 3 + Inertia". The Filament `lms` panel is for admin/L&D authoring only.

**Video hosting — external dependency (must be decided before build):** The spec says "video (hosted or YouTube/Vimeo embed)". Two distinct approaches:
1. **External embed only (YouTube/Vimeo links):** No file upload, no storage cost. Store as a URL in the block JSON. Rendered with an `<iframe>` in the learner view. No CDN cost. Downside: content is publicly accessible via the YouTube/Vimeo URL.
2. **Native video hosting via Mux:** Upload video files to Mux via their upload API. Store the `mux_asset_id` in the block JSON. Render with the Mux Player (web component). Mux handles encoding, CDN delivery, and signed playback URLs. This is the recommended approach for enterprise LMS use. Requires `STRIPE_MUX_TOKEN_ID` + `MUX_TOKEN_SECRET` in `.env` and a background job (`ProcessMuxUploadJob`) to poll for encoding completion.
3. **Self-hosted via S3/R2 + HLS:** Upload to S3, transcode with AWS Elemental MediaConvert or ffmpeg on a worker. High infrastructure complexity — not recommended.

**Decision required:** Choose Mux for hosted video or embed-only. Store in ADR before building `CourseBuilderPage`.

**Certificate PDF generation:** `certificate_url` in `issued_certifications` is a PDF stored in S3/R2. Generation uses `barryvdh/laravel-dompdf` or `spatie/browsershot` (Puppeteer). Add the chosen package to `composer.json`. The certificate template is a Blade view at `resources/views/certificates/template.blade.php` — branded with company logo and learner name. Generation is dispatched as a queued job (`GenerateCertificateJob`) triggered by the `CourseCompleted` event.

**Auto-subtitle generation:** Calls OpenAI Whisper API (`app/Services/AI/SubtitleService.php`) with the video file URL. Returns a WebVTT string stored in S3 and referenced in the Mux player track. Only available if Mux hosting is chosen (Mux also has its own auto-caption feature).

**AI features — course outline generator and quiz question generator:** Both call OpenAI GPT-4o via `app/Services/AI/CourseAiService.php`. Prompt templates at `resources/prompts/course-outline.txt` and `resources/prompts/quiz-generator.txt`. Responses returned as JSON and inserted into the block structure.

**Meilisearch:** `Course` model should implement `Laravel\Scout\Searchable`. Index fields: `title`, `description`, section titles, lesson titles. Learner search of the course catalogue goes through Scout.

**Missing from data model:** `course_lessons.blocks` is a JSON column — document the block schema. Each block should be `{type: string, content: object, sort_order: int}`. A `course_lesson_completions` pivot table is needed to track per-learner per-lesson completion: `{ulid id, ulid enrollment_id, ulid lesson_id, timestamp completed_at}`. Without this table, granular progress tracking (progress_percent on enrollment) cannot be computed correctly.

## Related

- [[learning-paths]] — sequences that include this course
- [[assessments]] — quiz question banks used in lessons
- [[compliance-training]] — mandatory enrollment driven from here
- [[certifications]] — certificates issued on completion
- [[content-library]] — shared media assets embedded in lessons
- [[skills]] — course completion can update skill ratings
