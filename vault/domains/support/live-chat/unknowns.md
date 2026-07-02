---
domain: support
module: live-chat
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Live Chat — Unknowns & Assumed Items

## Assumed Items

- **Least-active-chats assignment** *(assumed)* — vs round-robin or skills-based routing.
- **IP-geo deferred** *(assumed: privacy + effort)*.
- **Canned inserts in chat** timing (v1 vs P3) *(soft-dep)*.

## Open Questions

- Widget theming / white-label per company: in scope? Assumed basic colour config via `manage-widget`.
- Presence scale: Reverb is the heaviest consumer here — concurrency ceiling per company plan tier unconfirmed.
- Message retention / transcript pruning policy not defined.

## Related

- [[./decisions]] · [[../_index|Support MOC]]
