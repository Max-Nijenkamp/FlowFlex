---
domain: marketing
module: marketing-analytics
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Marketing Analytics — Decisions

Parent: [[_module]]

## ADR: Owns no tables — pure read aggregation

- **Decision:** Analytics computes everything on demand from the other marketing modules' tables; it stores nothing of its own.
- **Consequences:** Cleanest data-ownership posture; no duplicate/derived-state drift. Cost is query load, mitigated by caching ([[../../../architecture/caching]]).

## ADR: Soft-dep sections degrade to hidden

- **Decision:** When forms/landing/sequences/utm are inactive, their dashboard sections are hidden — no errors, no empty-but-broken widgets.
- **Consequences:** The dashboard works with only campaigns active and grows richer as modules activate.

## ADR: Cached with split TTL

- **Decision:** Metrics cache 1 h for historical ranges, 15 min for the current window.
- **Consequences:** Fresh-enough current data without hammering aggregate queries.

## Related

- [[_module]] · [[architecture]] · [[unknowns]]
