---
domain: finance
module: general-ledger
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# General Ledger — Unknowns & Assumptions

Items carried from the spec as `*(assumed)*` — authoritative defaults at build time, overridable via ADR.

- **Default CoA** — a standard SME chart is seeded on module activation. *(assumed)*
- **Account soft-delete** — soft-delete is blocked once an account has posted lines (undeletable-when-posted-to). *(assumed)*

No build-blocking unknowns identified. UNVERIFIED: the exact default chart-of-accounts contents are not enumerated in the spec.
