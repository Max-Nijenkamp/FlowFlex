---
domain: lms
module: mentoring
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Mentoring

Pair mentors with mentees, track mentoring relationships, schedule sessions, and log progress.

## Module-key

| Field | Value |
|---|---|
| key | `lms.mentoring` |
| priority | p3 |
| panel | lms |
| permission-prefix | `lms.mentoring` |
| tables | `lms_mentor_profiles`, `lms_mentorships`, `lms_mentorship_sessions` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|HR Profiles]] | Mentors/mentees are employees |
| Hard | [[../../core/billing/_module\|Billing]] + [[../../core/rbac/_module\|RBAC]] + [[../../core/notifications/_module\|Notifications]] | Gating, permissions, pairing notifications |
| Soft | [[../skills-matrix/_module\|Skills]] | Expertise from skills; manual tags otherwise |

## Core Features

- **Mentor directory** — volunteers with expertise + availability.
- **Matching** — browse directory, request pairing (manual v1 *(assumed)*).
- **Relationship record** — mentor, mentee, focus area, start date, status (active/paused/completed).
- **Session logging** — date, notes, action items (**pair-only** visibility).
- **Goals** per relationship with progress.
- **Feedback** on sessions *(assumed: optional rating)*.
- **Privacy** — session notes visible to mentor + mentee only, never HR `view-any`.

## See features/

- [[features/mentor-directory|Mentor Directory]] — browse + request mentors (custom-page).
- [[features/mentorship-management|Mentorship Management]] — relationships + goals (simple-resource).
- [[features/session-logging|Session Logging]] — pair-private session log (simple-resource).

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Session notes invisible to non-participants incl. HR.
- [ ] Self-mentorship + duplicate active pairing rejected.
- [ ] Non-accepting mentor not requestable.
- [ ] Goals progress toggles persist.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | employees | hr.profiles | Mentors/mentees are employees |
| Reads | expertise | lms.skills | Directory expertise fed from skills when active; manual tags otherwise |
| Reads | notify | core.notifications | Pairing request/accept notifications |

**Data ownership:** `lms.mentoring` writes only its three tables. It **reads** HR employees (never writes hr tables) and reads skills for expertise. Session notes are query-scoped to participants ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../skills-matrix/_module|Skills Matrix]] · [[../../hr/employee-profiles/_module|HR Profiles]] · [[../_index|LMS index]]
