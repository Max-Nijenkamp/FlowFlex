---
tags: [flowflex, domain/lms, courses, learning, phase/5]
domain: Learning & Development (LMS)
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-06
---

# Course Builder & LMS

Build and deliver courses. Track completion, award certificates, gamify learning.

**Who uses it:** L&D team (build), all employees (take)
**Filament Panel:** `lms`
**Depends on:** Core
**Phase:** 5
**Build complexity:** High — 3 resources, 2 pages, 7 tables

## Events Fired

- `CourseCompleted` → consumed by [[HR Compliance]] (mark certification fulfilled), [[Performance & Reviews]] (log development activity)
- `CourseAssigned`
- `CertificateGenerated`

## Events Consumed

- `EmployeeHired` (from [[Recruitment & ATS]]) → auto-assigns induction course
- `CertificationExpired` (from [[HR Compliance]]) → triggers renewal course assignment
- `OnboardingStarted` (from [[Onboarding]]) → assigns onboarding learning path

## Database Tables (7)

1. `courses` — course definitions
2. `course_modules` — modules/sections within a course
3. `course_lessons` — individual lessons within a module
4. `course_enrollments` — employee ↔ course assignments with progress %
5. `quiz_questions` — quiz content per lesson
6. `quiz_attempts` — employee quiz attempt records
7. `certificates` — issued certificate records with PDF path

## Sub-modules

### Course Builder

- **Content types:** video, document, quiz, embed, SCORM import
- **Course structure:** modules → lessons → content blocks
- **Quiz builder** — multiple choice, true/false, short answer
- **AI-assisted quiz generation** from course content

### Delivery & Tracking

- **Course assignment** by role or department
- **Completion tracking** with progress %
- **Certificate generation** — PDF, branded per workspace
- **SCORM import** — for existing e-learning content

### Gamification

- **Leaderboards** — top learners by points
- **Badges** — award on course completion
- **Points system** — earn points for completing modules

### External Customer Training

- **External training portal** — branded portal for customer onboarding/training
- No FlowFlex branding — white-labelled

## Related

- [[LMS Overview]]
- [[Skills Matrix & Gap Analysis]]
- [[HR Compliance]]
- [[Onboarding]]
