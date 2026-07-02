---
domain: crm
module: appointment-scheduling
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Appointment Scheduling — Decisions

## ADR: OAuth calendar sync deferred to v1.x

**Context:** Two-way Google/Outlook sync requires OAuth apps, consent screens, and token refresh — significant scope.

**Decision:** v1 ships working-hours-only availability. OAuth calendar connection (`crm_availability.calendar_connection`, encrypted) and two-way busy-time sync are a v1.x fast-follow *(assumed — OAuth scope creep)*.

**Consequences:** The encrypted column is created in v1 but unused until v1.x; slot computation initially considers only working hours minus existing FlowFlex bookings.

## ADR: Manual video link in v1

**Context:** Auto-generating Zoom/Meet links needs per-provider API integrations.

**Decision:** v1 uses a static `video_link` field on the meeting type. Automated per-booking link generation (Zoom/Meet API) is deferred *(assumed)*.

**Consequences:** All bookings for a video meeting type share the same link until the integration lands.

## ADR: Round-robin = least-loaded rep *(assumed)*

**Context:** Team meeting types need a fair assignment strategy.

**Decision:** Assign to the rep with the fewest bookings this week from the `team_user_ids` pool *(assumed)*.

**Consequences:** Simple and fair for even loads; may need weighting or true availability-awareness later. Flagged in [[unknowns]].

## ADR: Concurrency via in-transaction re-validation

**Context:** Two prospects could grab the same slot simultaneously.

**Decision:** `book()` re-validates slot freeness inside a DB transaction and throws `SlotTakenException` on collision, rather than relying solely on a unique constraint.

**Consequences:** The second concurrent booker gets a clean, catchable error and can pick another slot.
