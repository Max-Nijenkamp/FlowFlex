---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.purchase-orders
status: planned
priority: p3
depends-on: [operations.purchase-orders, procurement.requisitions, procurement.approvals, core.billing, core.rbac]
soft-depends: [procurement.catalogue]
fires-events: []
consumes-events: []
patterns: [money]
tables: [proc_po_sourcing]
permission-prefix: procurement.purchase-orders
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Purchase Orders (Procurement layer)

Procurement-managed purchase orders created from approved requisitions. The PO entity is owned by [[domains/operations/purchase-orders|operations.purchase-orders]] — this module adds sourcing (quote comparison), final PO approval, and spend commitment tracking on top.

*(v2 design simplification: Operations PO module is a hard dep — the "lightweight standalone PO" fallback from the v1 spec is dropped; activating procurement requires operations PO* *(assumed — avoids duplicate PO models))*

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/operations/purchase-orders\|operations.purchase-orders]] | the PO entity |
| Hard | [[domains/procurement/requisitions\|procurement.requisitions]] + [[domains/procurement/approvals\|procurement.approvals]] | POs originate from requisitions; final sign-off via matrix |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/procurement/supplier-catalogue\|procurement.catalogue]] | sourcing candidates |

---

## Core Features

- Create PO from approved requisition
- Supplier selection / sourcing: compare quotes from suppliers per PO, select one
- PO approval (separate from requisition approval — final sign-off via `ApprovalMatrix::chainFor('po', ...)` before send)
- Send PO to supplier (delegates to operations PO send)
- Track PO status to receipt
- Spend commitment tracking (committed = sent unreceived PO totals vs actual = received)

---

## Data Model

### proc_po_sourcing

| Column | Type | Notes |
|---|---|---|
| id, po_id FK, company_id (indexed) | ulid | |
| supplier_id | ulid FK ops_suppliers | |
| quote_amount_cents | bigint | |
| quote_reference | string nullable | |
| selected | boolean default false | max one selected per PO |

Uses `ops_purchase_orders` + `ops_po_lines` (shared). PO gains `procurement_approved_at` column *(assumed: migration shipped here)* — send blocked until set when procurement active.

---

## DTOs

### AddQuoteData — po_id (draft), supplier_id, quote_amount_cents (min:0), quote_reference?
### SelectQuoteData — sourcing_id — sets PO supplier + reprices *(assumed: supplier swap allowed in draft only)*

## Services & Actions

- `SourcingService::addQuote` / `selectQuote` — selection updates PO supplier (draft only)
- `ProcurementPoApproval::submit(poId)` / `act(...)` — matrix chain for `po` type; on final approval sets `procurement_approved_at`
- Send gate: `PurchaseOrderService::send` checks `procurement_approved_at` when procurement active *(hook registered by this module)*
- `CommitmentReport::for(period): array{committed: Money, actual: Money}`

---

## Filament

**Nav group:** Purchase Orders (Procurement)

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ProcurementPoResource` | #1 CRUD resource (over ops POs) | sourcing relation (quote comparison table), approval actions, commitment columns |

---

## Permissions

`procurement.purchase-orders.source` · `procurement.purchase-orders.approve` · `procurement.purchase-orders.view-commitments`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] One selected quote per PO; selection swaps supplier in draft only
- [ ] Send blocked until procurement approval when module active; unaffected when inactive
- [ ] PO approval chain per matrix (`po` rules)
- [ ] Commitment math: committed excludes received (brick/money)

---

## Build Manifest

```
database/migrations/xxxx_create_proc_po_sourcing_table.php
database/migrations/xxxx_add_procurement_approved_at_to_ops_purchase_orders.php
app/Models/Procurement/PoSourcing.php
app/Data/Procurement/{AddQuoteData,SelectQuoteData}.php
app/Services/Procurement/{SourcingService,ProcurementPoApproval}.php
app/Support/Procurement/CommitmentReport.php
app/Filament/Operations/Resources/ProcurementPoResource.php
database/factories/Procurement/PoSourcingFactory.php
tests/Feature/Procurement/{SourcingTest,PoApprovalGateTest}.php
```

---

## Related

- [[domains/operations/purchase-orders]]
- [[domains/procurement/requisitions]]
- [[domains/procurement/supplier-catalogue]]
