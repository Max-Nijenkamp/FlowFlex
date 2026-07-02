---
domain: operations
module: warehouses
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Warehouses — Decisions & ADR Notes

## Transfers Are Instant (No In-Transit State)

**Context:** Multi-location businesses often model stock moving between sites as "in transit" for a period.

**Decision:** v1 transfers are instant — `status` defaults to `completed`, and the transfer-out / transfer-in movement pair posts atomically in one transaction. An `in-transit` intermediate state is deferred.

**Consequences:** Simpler model, no reconciliation of goods-in-transit. Adding in-transit later means a second movement (out at ship, in at receive) and a status machine — a future ADR.

---

## Warehouses Build Before Inventory

**Context:** Inventory's `ops_stock_levels` has a FK to `warehouse_id`.

**Decision:** Warehouses is the first Operations module built. It is intentionally tiny (two tables, two resources, one action) so inventory has a warehouse to reference immediately.

**Consequences:** Transfer execution (`StockService::move`) is a *soft* dependency — the warehouse CRUD ships and works before inventory exists; the transfer resource simply has nothing to move until inventory lands.

---

## Stock Never Written Here

**Context:** A transfer changes stock levels.

**Decision:** This module owns the *transfer record* but never writes `ops_stock_levels` / `ops_stock_movements`. It calls inventory's `StockService::move` twice. This keeps the single-write-path invariant (see [[../inventory/decisions]]) and the data-ownership boundary intact.
