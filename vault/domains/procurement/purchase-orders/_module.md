---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.purchase-orders
status: planned
build-status: planned
priority: p3
depends-on: [operations.purchase-orders, procurement.requisitions, procurement.approvals, core.billing, core.rbac]
soft-depends: [procurement.catalogue]
fires-events: [PurchaseApproved]
consumes-events: []
patterns: [money]
tables: [proc_po_sourcing]
permission-prefix: procurement.purchase-orders
encrypted-fields: []
last-reviewed: 2026-07-02
color: "#4ADE80"
---

# Purchase Orders (Procurement layer)

Procurement-managed purchase orders created from approved requisitions. The PO entity is owned by [[../../operations/purchase-orders/_module|operations.purchase-orders]] — this module adds **sourcing** (quote comparison), **final PO approval**, and **spend commitment** tracking on top.

*(v2 simplification: Operations PO is a hard dep — the "lightweight standalone PO" v1 fallback is dropped; activating procurement requires operations PO. *(assumed — avoids duplicate PO models))*

Hosted in **/operations** (Procurement nav → Purchase Orders). See [[../_index|Procurement MOC]].

---

## Module-key

**Priority:** p3
**Panel:** /operations
**Permission prefix:** `procurement.purchase-orders`
**Tables:** proc_po_sourcing

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../operations/purchase-orders/_module\|operations.purchase-orders]] | the PO entity (`ops_purchase_orders`, `ops_po_lines`) |
| Hard | [[../requisitions/_module\|procurement.requisitions]] + [[../approvals/_module\|procurement.approvals]] | POs originate from requisitions; final sign-off via matrix |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../supplier-catalogue/_module\|procurement.catalogue]] | sourcing candidates + blacklist gate |

---

## Core Features

- [[features/create-from-requisition\|Create PO from requisition]] — the entry point (called by requisitions' convert).
- [[features/sourcing\|Sourcing / quote comparison]] — compare supplier quotes per PO, select one.
- [[features/po-approval\|PO approval]] — final matrix sign-off before send; sets `procurement_approved_at`.
- [[features/spend-commitment\|Spend commitment tracking]] — committed (sent, unreceived) vs actual (received).

---

## Data Model

Full model + ERD: [[data-model]]. Owns `proc_po_sourcing`. Adds a `procurement_approved_at` column to `ops_purchase_orders` *(assumed: migration shipped here — a schema extension, see [[decisions]])*.

## DTOs

`AddQuoteData`, `SelectQuoteData` — [[api]].

## Services & Actions

`SourcingService`, `ProcurementPoApproval`, `CommitmentReport`; PO send gate hook. See [[architecture]] + [[api]].

---

## Filament

**Nav group:** Purchase Orders (Procurement)

| Artifact | UI kind | Feature |
|---|---|---|
| `ProcurementPoResource` | simple-resource (over ops POs) | [[features/create-from-requisition]] + [[features/po-approval]] + [[features/spend-commitment]] columns |
| Sourcing / quote-comparison board | custom-page | [[features/sourcing]] |

**Access contract:** `canAccess() = Auth::user()->can('procurement.purchase-orders.view-any') && BillingService::hasModule('procurement.purchase-orders')` — [[../../../architecture/filament-patterns]] #1. See [[security]].

---

## Permissions

`procurement.purchase-orders.view-any` · `procurement.purchase-orders.source` · `procurement.purchase-orders.approve` · `procurement.purchase-orders.view-commitments`

---

## Cross-Domain Edges

- **Consumes (read):** approval chains from [[../approvals/_module|approvals]] (`chainFor('po', ...)`); catalogue + `SupplierGate` (soft) for sourcing candidates; the PO/line entities from Operations (read for display).
- **Fires:** `PurchaseApproved` (company_id scalar + po_id + total) → **finance** (AP commitment / expected bill) and **operations** (fulfilment) via their own listeners. Send delegates to `operations.purchase-orders` `PurchaseOrderService::send`.
- **Data ownership:** writes **only** `proc_po_sourcing` (+ the `procurement_approved_at` column it adds to the PO — a documented schema extension it then owns writes to via the send-gate hook). It does **not** create or mutate PO/line business data — Operations owns those. It never writes `finance_*`. See [[../../../security/data-ownership]] + [[decisions]].

Detail: [[decisions]] · [[unknowns]].

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's purchase orders data
- [ ] Module gating: artifacts hidden when `procurement.purchase-orders` inactive
- [ ] One selected quote per PO; selection swaps supplier in draft only
- [ ] Send blocked until procurement approval when module active; unaffected when inactive
- [ ] PO approval chain per matrix (`po` rules)
- [ ] Commitment math: committed excludes received (brick/money)

## Build Manifest

```
database/migrations/xxxx_create_proc_po_sourcing_table.php
database/migrations/xxxx_add_procurement_approved_at_to_ops_purchase_orders.php
app/Models/Procurement/PoSourcing.php
app/Data/Procurement/{AddQuoteData,SelectQuoteData}.php
app/Services/Procurement/{SourcingService,ProcurementPoApproval}.php
app/Support/Procurement/CommitmentReport.php
app/Events/Procurement/PurchaseApproved.php
app/Filament/Operations/Resources/ProcurementPoResource.php
app/Filament/Operations/Pages/SourcingBoard.php
database/factories/Procurement/PoSourcingFactory.php
tests/Feature/Procurement/{SourcingTest,PoApprovalGateTest}.php
```

## Related

- [[../../operations/purchase-orders/_module]] · [[../requisitions/_module]] · [[../supplier-catalogue/_module]] · [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
