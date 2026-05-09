---
tags: [flowflex, domain/lms, overview, phase/7]
domain: Learning & Development (LMS)
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-07
---

# LMS Overview

Learning and development platform — courses, skills matrix, succession planning, mentoring, external training, compliance, AI coaching, live classroom, and external learner portal. All 9 modules built in Phase 7.

**Filament Panel:** `lms`
**Domain Colour:** Orange `#EA580C` / Light: `#FFEDD5`
**Domain Icon:** `heroicon-o-academic-cap`
**Phase:** 7 — complete domain, all modules

## Modules

| Module | Description |
|---|---|
| [[Course Builder & LMS]] | Course builder, modules, lessons (video/quiz/SCORM/PDF), enrollments, certificates |
| [[Skills Matrix & Gap Analysis]] | Skills taxonomy, employee skill levels, role requirements, gap scoring, training recs |
| [[Succession Planning]] | Key roles, 9-box grid, succession candidates, readiness levels, periodic reviews |
| [[Mentoring & Coaching]] | Mentor profiles, mentor-mentee matching, session tracking, goals |
| [[External Training Requests]] | Training request + approval flow, completion tracking, certificate uploads |
| [[AI Learning Coach]] | Personalised AI tutor: spaced repetition, adaptive paths, daily nudges, tutor chat |
| [[Certification & Compliance Training]] | Mandatory training recurrence, certification tracking, GDPR/ARBO compliance dashboards |
| [[External Learner Portal]] | White-label portal for selling/sharing training externally; B2B seats, paid tiers |
| [[Live Virtual Classroom]] | Instructor-led sessions in-browser: breakout rooms, polls, recording, AI transcript |

## Filament Panel Structure

**Navigation Groups:**
- `Learning` — Courses, Course Modules, Lessons, Enrollments, Certificates
- `Skills` — Skills, Employee Skills, Skill Requirements, Skill Gaps
- `Succession` — Succession Plans, Succession Candidates, Reviews
- `Mentoring` — Mentor Profiles, Mentor Matches, Sessions
- `Training` — Training Requests, Training Completions

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `CourseCompleted` | LMS | HR Compliance (mark cert fulfilled), Performance & Reviews (log dev activity) |
| `CertificateExpiring` | LMS | Reminder to employee + manager |
| `CertificateIssued` | LMS | Notifications (notify employee) |
| `SkillGapIdentified` | Skills Matrix | LMS (recommend relevant course) |
| `MentorMatchCreated` | Mentoring | Notifications (notify mentor + mentee) |
| `TrainingRequestApproved` | External Training | Notifications (notify requesting employee) |
| `TrainingRequestRejected` | External Training | Notifications (notify requesting employee) |
| `EmployeeHired` | HR (Phase 2) | LMS (auto-assign induction course) |
| `OnboardingCompleted` | HR (Phase 2) | LMS (assign first compliance certifications) |
| `CertificationExpired` | HR Compliance (Phase 8) | LMS (trigger renewal course assignment) |

## Permissions Prefix

`lms.courses.*` · `lms.skills.*` · `lms.succession.*`  
`lms.mentoring.*` · `lms.training.*`

## Database Migration Range

`950000–999999`

## Related

- [[Course Builder & LMS]]
- [[Skills Matrix & Gap Analysis]]
- [[Succession Planning]]
- [[Mentoring & Coaching]]
- [[External Training Requests]]
- [[HR Compliance]] (Phase 8 — consumes LMS events)
- [[Performance & Reviews]] (Phase 8 — consumes CourseCompleted)
- [[Panel Map]]
- [[Build Order (Phases)]]
