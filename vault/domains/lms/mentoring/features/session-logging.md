---
domain: lms
module: mentoring
feature: session-logging
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Session Logging

Log mentoring sessions — date, notes, action items — visible to the mentor and mentee only.

## Behaviour

- A participant logs a session (`LogSessionData`): `session_date` (≤ today), notes, action items, optional rating.
- Sessions are **pair-private**: scoped at the query layer to the two participants; HR never sees notes.
- Action items carry forward as a lightweight to-do list per relationship.

## UI

- **Kind**: simple-resource  <!-- sessions relation manager on MentorshipResource, pair-scoped -->
- **Page**: Sessions relation on `MentorshipResource` (`/lms/mentoring/{mentorship}` → sessions).
- **Layout**: reverse-chronological session list (date, note excerpt, action-item count, rating) + form (date picker, notes, action-items repeater, rating).
- **Key interactions**: add session; check off action items; edit own logs. Non-participants get no rows (query-scoped).
- **States**: empty (no sessions → "Log your first session") · loading (skeleton) · error (future date / non-participant → rejected) · selected (session expanded).
- **Gating**: `lms.mentoring.participate` **and** query-level participant scope. Even HR `view-pairings` sees zero session rows.

## Data

- Owns / writes: `lms_mentorship_sessions`.
- Reads: parent `lms_mentorships` (participant check).
- Cross-domain writes: NONE.

> [!warning] UNVERIFIED
> Pair-privacy is a security-critical contract enforced at the query layer, not just UI. Designed here; `SessionPrivacyTest` is the evidence gate. Whether notes are ever discoverable in an HR/legal investigation is an open policy question ([[../unknowns]]).

## Relations

- Consumes: nothing.
- Feeds: nothing (private log).
- Shared entity: none.

## Related

- [[../_module|Mentoring module]] · [[mentorship-management]] · [[../security]]
