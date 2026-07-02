---
domain: communications
module: comms-analytics
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Comms Analytics — Decisions

## ADR: Read-only aggregator, no tables (source)

- **Context:** Reporting over inbox + broadcast data.
- **Decision:** This module owns **no tables** and writes nothing — it runs cached aggregate queries over the owning modules' data.
- **Consequences:** Zero data-ownership risk; the canonical read-only cross-domain pattern ([[../../../security/data-ownership]]).

## ADR: First-response definition (source)

- **Decision:** First-response time = the first **outbound** message after the first **inbound** message per conversation.
- **Consequences:** Deterministic + N+1-free query shape.

## ADR: Cache with short current-window TTL (source)

- **Decision:** Historical windows cached 1 h; the current window 15 min; TTL-only invalidation (no event-driven busting).
- **Consequences:** Slightly stale current numbers, but cheap + simple.

## ADR: Broadcast section is conditional (source)

- **Decision:** The broadcast-performance section renders only when `comms.broadcast` is active.

## Related

- [[_module]] · [[architecture]] · [[../../../architecture/caching]]
