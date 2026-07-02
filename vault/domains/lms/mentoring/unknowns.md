---
domain: lms
module: mentoring
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Mentoring — Unknowns

## Assumed Items

- Manual matching in v1 *(assumed)* — no algorithmic mentor/mentee recommendation.
- Optional per-session rating *(assumed)*.
- Session privacy via participant-scoping, not encryption *(assumed)*.
- Relationship `status` is a plain string, not a state machine *(assumed)*.

## Open Questions

- Should mentoring sessions integrate with scheduling (`.ics` invites via `spatie/icalendar-generator`) like CRM appointments?
- Algorithmic matching on expertise + goals — when, and does it need skills to be active?
- Should completed mentorships feed an HR development/succession record (cross-domain)?
- Are session notes ever discoverable in a legal/HR investigation, overriding pair-privacy? (Policy question.)
- Group mentoring (one mentor, many mentees) vs strictly 1:1?
