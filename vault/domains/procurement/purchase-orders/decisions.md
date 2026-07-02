---
domain: procurement
module: purchase-orders
type: decisions
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Procurement PO Layer — Local Decisions

- **Layer over Operations POs, not a second PO model.** Operations PO is a hard dep; the v1 "standalone lightweight PO" fallback is dropped. *(assumed — avoids duplicate PO models)*
- **`procurement_approved_at` is added to `ops_purchase_orders` by this module.**

  > [!warning] UNVERIFIED
  > This is a **cross-boundary schema extension**: the column lives on an Operations-owned table but is written by procurement. It's a deliberate, documented exception to the strict "write only your own tables" rule (the column is procurement-owned, the table is not). The clean alternatives — a separate `proc_po_approval` table keyed by `po_id`, or an event that Operations' own listener applies — should be weighed at build. Flagged for an ADR before implementation.

- **Send gate via hook** on `PurchaseOrderService::send` rather than duplicating send logic.
- **`PurchaseApproved` is the outward event**; finance/operations react with their own listeners — procurement never writes their tables.
- **Supplier swap only in draft**, and only to a non-blacklisted supplier. *(assumed)*

## Related

- [[_module]] · [[unknowns]] · [[../../../security/data-ownership]] · [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
