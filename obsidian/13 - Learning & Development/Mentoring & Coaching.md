---
tags: [flowflex, domain/lms, mentoring, coaching, phase/7]
domain: Learning & Development
panel: lms
color: "#EA580C"
status: planned
last_updated: 2026-05-07
---

# Mentoring & Coaching

Structured mentoring programme. Match mentors and mentees, schedule sessions, track progress and goals — all within FlowFlex.

**Who uses it:** L&D team (programme management), all employees (mentors and mentees)
**Filament Panel:** `lms`
**Depends on:** [[HR — Employee Profiles]], [[Booking & Appointment Scheduling]]
**Phase:** 7
**Build complexity:** Medium — 3 resources, 1 page, 3 tables

---

## Features

- **Mentor profile registration** — tenants opt in as mentors; set bio, areas of expertise (JSON), maximum mentee count, and whether they are currently accepting mentees
- **Coaching network directory** — browsable directory of all registered mentors; filterable by expertise area; visible to all tenants
- **Mentor matching** — L&D team or employees request a match; matching considers expertise alignment and current capacity; stored in `mentor_matches`
- **`MentorMatchCreated` event** — fires when a match is created; sends notification to both mentor and mentee with programme goals and first-session booking prompt
- **Match status workflow** — pending → active → completed/cancelled; transitions tracked with timestamps
- **Goal setting** — each match has a `goals` text field capturing the development goals for the relationship; referenced in session notes
- **Session scheduling** — mentoring sessions linked to [[Booking & Appointment Scheduling]] booking pages; session records in `mentoring_sessions`
- **Session notes and action items** — after each session, mentor or mentee records notes and action items stored in `mentoring_sessions.action_items` JSON
- **Session completion tracking** — `mentoring_sessions.status` workflow: scheduled → completed/cancelled; completed count shown on match record
- **Match review** — at the end of a mentoring relationship, both parties complete a brief review; feedback stored for L&D programme improvement
- **Programme management** — L&D team views all active matches, session frequency, completion rates; identify stalled matches (no session in 30 days)

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `mentor_profiles`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | → tenants |
| `bio` | text nullable | |
| `areas_of_expertise` | json | array of skill/topic strings |
| `max_mentees` | integer default 2 | |
| `is_accepting` | boolean default true | |

### `mentor_matches`
| Column | Type | Notes |
|---|---|---|
| `mentor_id` | ulid FK | → tenants |
| `mentee_id` | ulid FK | → tenants |
| `status` | enum | `pending`, `active`, `completed`, `cancelled` |
| `matched_at` | timestamp | |
| `ended_at` | timestamp nullable | |
| `goals` | text nullable | development goals |
| `sessions_completed` | integer default 0 | |
| `mentor_rating` | integer nullable | 1-5 mentee rating of mentor |
| `mentee_rating` | integer nullable | 1-5 mentor rating of mentee |

### `mentoring_sessions`
| Column | Type | Notes |
|---|---|---|
| `mentor_match_id` | ulid FK | → mentor_matches |
| `scheduled_at` | timestamp | |
| `duration_minutes` | integer nullable | |
| `status` | enum | `scheduled`, `completed`, `cancelled` |
| `notes` | text nullable | session summary |
| `action_items` | json nullable | array of {description, owner, due_date} |
| `booking_appointment_id` | ulid FK nullable | → booking_appointments |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `MentorMatchCreated` | `mentor_match_id`, `mentor_id`, `mentee_id` | Notification to both mentor and mentee |

---

## Events Consumed

None — mentoring sessions are scheduled via [[Booking & Appointment Scheduling]].

---

## Permissions

```
lms.mentor-profiles.view
lms.mentor-profiles.create
lms.mentor-profiles.edit
lms.mentor-profiles.delete
lms.mentor-matches.view
lms.mentor-matches.create
lms.mentor-matches.edit
lms.mentor-matches.complete
lms.mentor-matches.cancel
lms.mentoring-sessions.view
lms.mentoring-sessions.create
lms.mentoring-sessions.complete
```

---

## Related

- [[LMS Overview]]
- [[Skills Matrix & Gap Analysis]]
- [[Booking & Appointment Scheduling]]
- [[HR — Employee Profiles]]
