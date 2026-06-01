---
type: module
domain: Learning & Development
panel: lms
module-key: lms.mentoring
status: planned
color: "#4ADE80"
---

# Mentoring

Pair mentors with mentees, track mentoring relationships, schedule sessions, and log progress.

## Core Features

- Mentor/mentee matching: based on skills, goals, availability
- Mentoring relationship record: mentor, mentee, focus area, start date, status
- Session logging: date, notes, action items
- Goals per relationship with progress tracking
- Mentor directory: employees who volunteer as mentors with their expertise
- Feedback on sessions
- Relationship status: active / completed / paused

## Data Model

| Table | Key Columns |
|---|---|
| `lms_mentorships` | company_id, mentor_id, mentee_id, focus_area, status, started_at, ended_at |
| `lms_mentorship_sessions` | mentorship_id, company_id, session_date, notes, action_items |
| `lms_mentor_profiles` | company_id, employee_id, expertise (json), availability, is_accepting |

## Filament

**Nav group:** Mentoring

- `MentorshipResource` — create pairings, track sessions
- `MentorDirectoryPage` (custom page) — browse available mentors

## Related

- [[domains/lms/skills-matrix]]
- [[domains/hr/employee-profiles]]
