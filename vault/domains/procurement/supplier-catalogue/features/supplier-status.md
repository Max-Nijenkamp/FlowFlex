---
domain: procurement
module: supplier-catalogue
feature: supplier-status
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Supplier Status (Approval & Blacklist)

Per-supplier approval state: approved / pending / blacklisted. Blacklisted suppliers are blocked across all of procurement via `SupplierGate`.

## Behaviour

- One status row per supplier per company.
- `blacklisted` requires notes (why).
- `SupplierGate::isBlocked` returns true for blacklisted suppliers; consulted by the requisition picker, PO sourcing, and PO supplier selection.
- Approving sets `approved_at`.

## UI

- **Kind**: simple-resource
- **Page**: "Supplier Status" (`/operations/procurement/supplier-status`)
- **Layout**: table — supplier, status badge (green/amber/red), approved date, notes.
- **Key interactions**: approve / set-pending / blacklist row actions; blacklist requires a notes field before save.
- **States**: empty ("No suppliers rated yet") · loading (skeleton) · error (toast) · selected (row → status history/notes).
- **Gating**: view `procurement.catalogue.view-any`; change status `procurement.catalogue.manage-supplier-status`.

## Data

- Owns / writes: `proc_supplier_status`.
- Reads: `operations.suppliers` (soft) for the supplier list.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Feeds: `SupplierGate::isBlocked` → [[../../requisitions/features/catalogue-picker|requisition picker]], [[../../purchase-orders/features/sourcing|PO sourcing]]; status → spend "non-approved supplier" maverick detection.
- Shared entity: suppliers (operations).

## Test Checklist

### Unit
- [ ] `SupplierGate::isBlocked` true only for blacklisted; status enum approved/pending/blacklisted

### Feature (Pest)
- [ ] Blacklisting blocks requisition picker, sourcing, and PO supplier selection through the single gate chokepoint
- [ ] Status flip audited + locked (no half-flipped reads); tenant isolation enforced

### Livewire
- [ ] `SupplierStatusResource` status actions gated + confirm; badge renders state

## Unknowns

- Auto-blacklist on repeated 3-way-match failures? `*(assumed: manual only v1)*`

## Related

- [[../_module|Supplier Catalogue]] · [[catalogue-items]] · [[../../spend-analytics/features/maverick-spend]]
