---
domain: lms
module: lessons
feature: quizzes
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Quizzes

Quiz-type lessons: authoring the question bank + passing score, and server-side grading of learner submissions.

## Behaviour

- A quiz lesson holds a `lms_quizzes` row: `questions[]` (multiple-choice / true-false) + `passing_score` (0–100).
- Learner submits answers (`SubmitQuizData`); `QuizService::grade` scores server-side and returns `QuizResult {score, passed, best}`.
- Correct-answer flags are **never** sent to the learner client.
- Lesson completes only when the learner's score ≥ `passing_score` (criterion `quiz-passed`).
- Best score is retained; `attempts` counts tries (unlimited default *(assumed)*).

## UI

- **Kind**: custom-page  <!-- quiz builder is a bespoke question editor; grading is server-side, no learner-facing admin page -->
- **Page**: "Quiz Builder" (`QuizBuilderPage` / repeater within the lesson form, `/lms/courses/{course}/quiz/{lesson}`).
- **Layout**: ordered question list; each question = prompt + type toggle + options repeater + correct-answer marker; header = passing score.
- **Key interactions**: add/reorder questions; mark correct option(s); set passing score; preview (admin-only, shows keys). Learner submission happens on the enrolments portal, not here.
- **States**: empty (no questions → "Add a question") · loading (save) · error (no correct answer marked → validation) · selected (question expanded).
- **Gating**: authoring `lms.lessons.manage`; learner submit gated by enrolment + portal guard.

## Data

- Owns / writes: `lms_quizzes`; grading writes `lms_lesson_progress` (`quiz_score`, `attempts`, `status`).
- Reads: `lms_lessons` (parent lesson).
- Cross-domain writes: NONE — on pass, calls `EnrolmentService::recomputeProgress` (enrolments' own table).

## Relations

- Consumes: nothing.
- Feeds: a passing grade recomputes enrolment progress via `EnrolmentService` (same-domain call).
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Server-side scoring against `questions[].correct`; pass = score >= passing_score; correct flags never serialized to client

### Feature (Pest)
- [ ] Attempts increment and best score retained under raced submissions (locked progress row)
- [ ] Passing grade marks the lesson complete and triggers progress recompute; failing does not
- [ ] Tenant isolation: learner must be enrolled to grade

### Livewire
- [ ] Quiz authoring validates question bank + passing score; learner submission returns result without correct answers

## Unknowns

- Extra question types, configurable max-attempts, and cooldown-on-exhaustion are open — see [[../unknowns]].

> [!warning] UNVERIFIED
> Quiz answer confidentiality is a security-critical contract (`questions[].correct` never leaves the server). It is designed here but has no test evidence yet.

## Related

- [[../_module|Lessons module]] · [[lesson-content]] · [[../security]] · [[../api]]
