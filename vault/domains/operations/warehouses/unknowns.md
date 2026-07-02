---
domain: operations
module: warehouses
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Warehouses — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers)

- **Bins / sub-locations** — subdivision of a warehouse into bins/aisles is *(assumed)* deferred. Confirm whether v1 needs bin-level stock or warehouse-level is enough.
- **Capacity tracking** — max capacity / utilisation per warehouse is *(assumed)* deferred.
- **Delete guard** — soft-delete blocked while stock > 0 is *(assumed)*. Confirm the exact rule (block, or allow with a warning + forced transfer-out).
- **Default-warehouse enforcement** — the one-default-per-company invariant is *(assumed)* enforced via a partial unique index + save-path unset. Confirm implementation.
- **In-transit transfers** — *(assumed)* deferred; v1 is instant (see [[./decisions]]).
- **`SetDefaultWarehouseAction`** — *(assumed)* existence of a dedicated action vs inline resource logic.

## Open Questions

- Should `virtual` warehouses (e.g. "damaged", "quarantine", "in-transit holding") be a first-class type used by stock-adjustments write-offs, or just a label? Currently a plain enum value. *(assumed)*
