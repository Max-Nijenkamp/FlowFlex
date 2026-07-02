---
domain: support
module: automations
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Automations — Unknowns & Assumed Items

## Assumed Items

- **AND-only conditions v1** *(assumed)* — OR / nested groups deferred.
- **Loop guard via system-actor flag** *(assumed)* — exact mechanism (flag on the update vs a suppressed-events context) unconfirmed.
- **Time-based idempotency once per rule per ticket** *(assumed)*.
- **Log retention 90 days** *(assumed)*; **test-run preview** *(assumed)*.

## Open Questions

- Should "assign to team" resolve via a round-robin over team members, and does that live here or in Tickets? Assumed the engine calls `TicketService` with a resolved agent.
- SLA-warning trigger requires the SLA module active; degrade gracefully (hide that trigger) when it isn't.

## Related

- [[./decisions]] · [[../_index|Support MOC]]
