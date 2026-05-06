---
tags: [flowflex, domain/lms, overview, phase/5]
domain: Learning & Development (LMS)
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-06
---

# LMS Overview

Learning and development platform. Courses, skills matrix, succession planning, mentoring, and external training.

**Filament Panel:** `lms`
**Domain Colour:** Orange `#EA580C` / Light: `#FFEDD5`
**Domain Icon:** `academic-cap` (Heroicons)
**Phase:** 5

## Modules in This Domain

| Module | Description |
|---|---|
| [[Course Builder & LMS]] | Course builder, SCORM, gamification, certificates |
| [[Skills Matrix & Gap Analysis]] | Skills taxonomy, gap analysis, training recs |
| [[Succession Planning]] | 9-box grid, succession readiness, key role risks |
| [[Mentoring & Coaching]] | Mentor matching, session tracking |
| [[External Training Requests]] | Employee training request + approval |

## Key Events from This Domain

| Event | Source | Consumed By |
|---|---|---|
| `CourseCompleted` | [[Course Builder & LMS]] | [[HR Compliance]] (mark cert fulfilled), [[Performance & Reviews]] (log dev activity) |

## Key Events Consumed

| Event | From | What LMS Does |
|---|---|---|
| `EmployeeHired` | [[Recruitment & ATS]] | Assigns induction course |
| `CertificationExpired` | [[HR Compliance]] | Triggers renewal course assignment |
| `OnboardingCompleted` | [[Onboarding]] | Triggers first compliance cert assignments |

## Related

- [[Course Builder & LMS]]
- [[Skills Matrix & Gap Analysis]]
- [[HR Compliance]]
- [[Performance & Reviews]]
- [[Panel Map]]
