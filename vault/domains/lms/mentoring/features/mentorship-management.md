---
domain: lms
module: mentoring
feature: mentorship-management
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Mentorship Management

Manage the mentoring relationship: accept/pause/complete, focus area, and goals.

## Behaviour

- Mentor accepts a requested pairing; the relationship becomes `active`.
- Status: `active → paused → active`, and either → `completed`.
- Goals (`goals` jsonb `[{title, done}]`) tracked with progress toggles.
- Only participants see their own relationships; HR (`view-pairings`) sees the pairing existence, not goals detail or notes.

## UI

- **Kind**: simple-resource
- **Page**: "Mentorships" (`MentorshipResource`, `/lms/mentoring`)
- **Layout**: table of the user's own mentorships (mentor/mentee, focus area, status badge, goal progress) + form (focus area, status, goals repeater) + sessions relation (pair-scoped, see [[session-logging]]).
- **Key interactions**: accept/pause/complete actions; add/toggle goals; open sessions relation.
- **States**: empty (no mentorships → "Request or accept a mentorship to begin") · loading (skeleton) · error (self/duplicate → validation) · selected (row → detail).
- **Gating**: `lms.mentoring.participate` (own); HR `lms.mentoring.view-pairings` (pairings only).

## Data

- Owns / writes: `lms_mentorships`.
- Reads: HR employees; core.notifications (accept/complete notices).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: session logging attaches to the relationship.
- Shared entity: employees (HR).

## Unknowns

- Group mentoring; completion feeding HR development record — see [[../unknowns]].

## Related

- [[../_module|Mentoring module]] · [[mentor-directory]] · [[session-logging]]
