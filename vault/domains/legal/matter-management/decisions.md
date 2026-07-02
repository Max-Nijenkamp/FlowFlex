---
domain: legal
module: matter-management
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Matter Management — Decisions

- **Confidentiality is a second gate, not a policy shortcut.** A single `accessibleFor` API is the only read path so confidential scope can never be bypassed by `view-any` or by legal.spend. *(assumed)* default.
- **Spend + document links are read-only.** Matter never writes into legal.spend / dms tables; it reads summaries.

## Related

- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
