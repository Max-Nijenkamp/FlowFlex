---
type: module
domain: Learning & Development
domain-key: lms
panel: lms
module-key: lms.mentoring
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [lms.skills]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [lms_mentorships, lms_mentorship_sessions, lms_mentor_profiles]
permission-prefix: lms.mentoring
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Mentoring

Pair mentors with mentees, track mentoring relationships, schedule sessions, and log progress.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | mentors/mentees are employees |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, pairing notifications |
| Soft | [[domains/lms/skills-matrix\|lms.skills]] | expertise from skills; manual expertise tags otherwise |

---

## Core Features

- Mentor directory: employees who volunteer as mentors with expertise + availability
- Mentor/mentee matching: browse directory, request pairing (manual matching v1 *(assumed)*)
- Mentoring relationship record: mentor, mentee, focus area, start date, status (active/paused/completed)
- Session logging: date, notes, action items (visible to the pair only)
- Goals per relationship with progress tracking
- Feedback on sessions *(assumed: optional rating per session)*
- **Privacy**: session notes visible to mentor + mentee only — not HR `view-any`

---

## Data Model

### lms_mentor_profiles — id, company_id (indexed), employee_id FK unique, expertise (jsonb tags), availability (string), is_accepting (bool)
### lms_mentorships — id, company_id (indexed), mentor_id FK, mentee_id FK (≠), focus_area, goals (jsonb [{title, done}]), status (active/paused/completed), started_at, ended_at nullable; unique active `(mentor_id, mentee_id)`
### lms_mentorship_sessions — id, mentorship_id FK, company_id, session_date, notes (pair-only), action_items (jsonb), rating int nullable

---

## DTOs

### RequestMentorshipData — mentor_id (accepting, ≠ self), focus_area (required)
### LogSessionData — mentorship_id (participant), session_date (≤ today), notes?, action_items[]

## Services & Actions

- `MentoringService::request/accept/complete`
- Session visibility scope: participants only (query-level)
- `MentorDirectoryQuery` — accepting mentors with expertise (skills-fed when active)

---

## Filament

**Nav group:** Mentoring

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `MentorshipResource` | #1 CRUD resource | own relationships; sessions relation (pair-scoped) |
| `MentorDirectoryPage` | #9 gallery custom page | browse + request |

---

## Permissions

`lms.mentoring.participate` (all) · `lms.mentoring.view-pairings` (HR — pairings only, never session notes)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Session notes invisible to non-participants incl. HR
- [ ] Self-mentorship + duplicate active pairing rejected
- [ ] Non-accepting mentor not requestable
- [ ] Goals progress toggles persist

---

## Build Manifest

```
database/migrations/xxxx_create_lms_mentor_profiles_table.php
database/migrations/xxxx_create_lms_mentorships_table.php
database/migrations/xxxx_create_lms_mentorship_sessions_table.php
app/Models/LMS/{MentorProfile,Mentorship,MentorshipSession}.php
app/Data/LMS/{RequestMentorshipData,LogSessionData}.php
app/Services/LMS/MentoringService.php
app/Filament/LMS/Resources/MentorshipResource.php
app/Filament/LMS/Pages/MentorDirectoryPage.php
database/factories/LMS/MentorshipFactory.php
tests/Feature/LMS/{MentoringTest,SessionPrivacyTest}.php
```

---

## Related

- [[domains/lms/skills-matrix]]
- [[domains/hr/employee-profiles]]
