---
tags: [flowflex, domain/hr, performance, reviews, okr, phase/8]
domain: HR & People
panel: hr
color: "#7C3AED"
status: planned
last_updated: 2026-05-06
---

# Performance & Reviews

Structured performance management. OKRs, review cycles, 360 feedback, and development planning — all in one place.

**Who uses it:** All employees, managers, HR team
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]]
**Phase:** 8
**Build complexity:** High — 3 resources, 2 pages, 8 tables

## Events Consumed

- `CourseCompleted` (from LMS) → logs development activity on performance record

## Features

### Goal Setting

- Goal framework: OKR (Objective + Key Results) or simple KPI targets
- Goal hierarchy: Company goals → Department goals → Individual goals
- Goal check-in updates (weekly / monthly progress updates)
- Goal weighting and scoring
- Goal visibility controls (public / team-only / private)

### Review Cycles

- Review cycle builder (define: who reviews whom, what form, what cadence)
- Cadence options: annual, quarterly, bi-annual, continuous
- Self-assessment form (employee rates themselves before manager review)
- Manager review form (structured rating + free text)
- 360-degree feedback (peer nominations, anonymous optional)

### 360 Feedback

- Peer nominations (employee nominates colleagues)
- Anonymous feedback toggle (reviewers can be kept anonymous)
- Panel interview coordination (multiple interviewers, aggregate scores)
- Calibration session workspace (managers align ratings across team before sharing)

### Development Plans

- Actions, training recommendations, target roles
- Link to LMS courses (if active)
- Progress tracking on development goals

### Review History

- All past reviews stored on employee profile
- Score trends over time
- Comparison against team benchmarks (anonymised)

## Database Tables (8)

1. `goals` — goal definitions (company, department, individual)
2. `goal_check_ins` — progress updates per goal
3. `review_cycles` — configured review cycles
4. `review_instances` — per-employee review records per cycle
5. `review_forms` — form definitions (questions, rating scales)
6. `review_responses` — responses per reviewer per form
7. `peer_nominations` — 360 peer selection
8. `development_plans` — individual development action plans

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Course Builder & LMS]]
- [[Skills Matrix & Gap Analysis]]
- [[Succession Planning]]
