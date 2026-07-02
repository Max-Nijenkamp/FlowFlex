---
domain: operations
module: purchase-orders
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Purchase Orders — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers)

- **PO number format** — `PO-2026-001` (year-scoped sequence) is *(assumed)*. Confirm exact format + whether the sequence resets per year.
- **`expected_delivery` validation** — `after_or_equal:today` is *(assumed)*; may need to allow back-dating on import.
- **Meilisearch fields** — PO index (`po_number`, supplier name) is *(assumed)*.
- **Approval before send** — the flat spec has no PO approval step (draft→sent is a single permission). Confirm whether an approval threshold (like adjustments) is wanted for high-value POs. Currently none. *(assumed)*
- **Currency source** — PO `currency` defaults from supplier; multi-currency FX handling is out of scope for v1 (see [[../../_opportunities]]).

## Open Questions

- **Supplier PO acknowledgement** — v1 emails a PDF one-way. Should there be a supplier portal to acknowledge / confirm delivery date (public-vue)? Deferred *(assumed)*.
- **Editing a `sent` PO** — can lines be edited after send but before any receipt? Currently *(assumed)* no (edit only in draft); a change would need a revision/version concept.
