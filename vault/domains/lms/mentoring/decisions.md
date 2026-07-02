---
domain: lms
module: mentoring
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Mentoring — Decisions

## ADR: Session notes are pair-private, enforced at the query layer

- **Context:** Mentoring only works if mentees trust that notes stay between the pair.
- **Decision:** `lms_mentorship_sessions` is scoped to participants at the query level. `lms.mentoring.view-pairings` (HR) sees pairings but never notes.
- **Consequences:** Query-level scoping (not UI-hiding) makes the privacy guarantee testable (`SessionPrivacyTest`). HR gets programme oversight without surveillance.

## ADR: Manual matching in v1

- **Context:** Auto-matching mentors to mentees is complex.
- **Decision:** v1 is browse-directory + request-pairing (manual) *(assumed)*; the mentor accepts.
- **Consequences:** Simpler build; algorithmic matching is a later enhancement (see [[unknowns]]).

## ADR: Expertise from skills when active, manual tags otherwise

- **Context:** Directory search needs expertise tags.
- **Decision:** When [[../skills-matrix/_module|skills]] is active, expertise can be fed from a mentor's skill profile; otherwise `expertise` is manual jsonb tags.
- **Consequences:** Soft dependency; the directory degrades gracefully without skills.

## ADR: One active pairing per mentor-mentee pair

- **Decision:** Unique on active `(mentor_id, mentee_id)`; self-mentorship rejected.
- **Consequences:** No duplicate concurrent relationships; a completed pairing can be re-opened as a new one.
