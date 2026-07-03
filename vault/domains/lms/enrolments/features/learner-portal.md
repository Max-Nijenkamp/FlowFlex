---
domain: lms
module: enrolments
feature: learner-portal
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Learner Portal

The `/learn` learner-facing surface: my courses, the lesson player, and progress — for both employees and external learners.

## Behaviour

- Learner lands on a dashboard of their own enrolments with progress + due dates.
- Opening a course shows its module → lesson structure; opening a lesson renders the player (video/text/file/quiz).
- Completing a lesson calls `LessonProgressService::complete`; quiz submit calls `QuizService::grade`.
- A learner sees **only their own** enrolments — employees via user link, externals via scoped `portal_token`.
- Progress and completion status update as lessons complete; 100% may issue a certificate (if the course has a template).

## UI

- **Kind**: public-vue  <!-- Vue + Inertia portal, scoped learner guard -->
- **Page**: "My Learning" (`/learn`, `/learn/courses/{course}`, `/learn/lessons/{lesson}`) — ui-strategy row #15.
- **Layout**: dashboard grid of enrolment cards (progress ring, due badge); course page = lesson list with completion ticks; lesson page = type-specific player + "mark complete" / quiz form; left nav = course outline.
- **Key interactions**: open lesson → auto/explicit mark complete → progress ring updates; quiz submit → server grade → pass/fail feedback (no answer keys); certificate download link on completion.
- **States**: empty (no enrolments → "You have no assigned courses yet") · loading (skeleton cards / player shimmer) · error (locked prerequisite → "Complete X first"; quiz below pass → retry) · selected (active lesson highlighted in outline).
- **Gating**: scoped learner guard; own-data scope only. No Filament permission — portal guard governs.

## Data

- Owns / writes: `lms_enrolments` (status/progress via services), `lms_lesson_progress` (via lessons service).
- Reads: courses + lessons structure; certificate PDF link (certifications).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: lesson completion recomputes enrolment progress; completion triggers certificate/skill/path side effects (same-domain).
- Shared entity: certificate (certifications), course/lesson content (courses/lessons) — read-only.

> [!warning] UNVERIFIED
> External-learner login UX (magic link vs token URL) is *(assumed)* — see [[../unknowns]]. Own-data isolation is the headline security test.

## Test Checklist

### Unit
- [ ] Progress % math: completed lessons / total lessons per enrolment

### Feature (Pest)
- [ ] `/learn` shows only the learner's own enrolments; another learner's enrolment id -> 403/404
- [ ] Tenant isolation: external learners scoped to their company context

### Livewire
- [ ] Portal pages gate on the learner guard; lesson player renders per lesson type; progress updates after complete

## Related

- [[../_module|Enrolments module]] · [[../security]] · [[../../lessons/features/quizzes|Quizzes]] · [[../../../../frontend/_index|Frontend]]
