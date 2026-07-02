---
domain: support
module: support-analytics
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Support Analytics — Unknowns & Assumed Items

## Assumed Items

- **Marketing P3 CSAT supersedes this mail** *(assumed)* — handoff/coexistence between the two CSAT paths undefined.
- **CSAT comment unencrypted** *(assumed)* — free-text could contain PII; revisit under data-lifecycle.

## Open Questions

- Should CSAT surveys send on `resolved` or on `closed`? Assumed on `TicketResolved` (resolved).
- Agent-performance "CSAT per agent" attribution when a ticket is reassigned mid-life — which agent gets credit? Assumed the resolver.
- Historical vs current cache TTL boundary (what counts as "current"?) not precisely defined.

## Related

- [[./decisions]] · [[../_index|Support MOC]]
