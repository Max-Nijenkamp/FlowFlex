---
domain: operations
module: suppliers
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Suppliers — Decisions & ADR Notes

## Operational vs Financial Supplier Split

**Context:** A supplier has both operational attributes (which items, lead times, delivery performance) and financial attributes (IBAN, bills, payment history).

**Decision:** Operations owns the **operational** supplier record (`ops_suppliers`). Finance AP owns the **financial** supplier (IBAN, bills). They are linked by a nullable `fin_supplier_id` reference. Neither writes the other's table.

**Consequences:** IBAN/bank data is encrypted and blast-walled inside Finance; Operations can run supplier catalogues + performance standalone (finance.ap is a soft dep). Cost: two supplier records that must be reconciled by the `fin_supplier_id` link.

---

## Performance Is Derived, Not Stored

**Context:** On-time delivery rate could be a stored column updated on each GRN.

**Decision:** Performance (`on_time_rate`, `order_count`) is computed on read from PO `expected_delivery` vs GRN `received_at`. No stored aggregate in v1.

**Consequences:** Always current, no denormalisation to keep in sync. If it gets slow at scale, a cached projection is a future step — but it must be a copy Operations owns, refreshed from its own reads.

---

## One Preferred Supplier Per Item

**Decision:** Exactly one `ops_supplier_items` row per item may have `is_preferred = true`; setting a new preferred unsets the previous in the same transaction. The preferred supplier's `cost_cents` is the default for PO lines.

**Consequences:** Deterministic PO cost defaulting; enforced by a partial unique index *(assumed)* + save-path unset.
