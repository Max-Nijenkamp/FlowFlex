---
type: domain-index
domain: Procurement
panel: operations
color: "#4ADE80"
---

# Procurement

Purchase requisitions, sourcing POs, supplier catalogue, goods receipt, spend analytics, and approval workflows. **Panel:** `/operations` (hosted in the Operations panel — see [[build/decisions/decision-2026-06-01-panel-consolidation]]) — Phase 3.

Procurement does NOT have its own panel. Its resources appear in the `/operations` panel under the **Procurement** nav group. Procurement and Operations share the PO/GRN/supplier entities. Integrates with Finance AP for 3-way match (PO → GRN → Supplier Invoice).

---

## Navigation Groups

- **Requisitions** — Purchase Requisitions
- **Purchase Orders** — POs, Sourcing, 3-Way Match
- **Suppliers** — Supplier Catalogue
- **Reporting** — Spend Analytics
- **Settings** — Approval Rules, Delegations

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/procurement/requisitions\|Purchase Requisitions]] | `procurement.requisitions` | planned | **P3 core** |
| [[domains/procurement/purchase-orders\|Purchase Orders]] | `procurement.purchase-orders` | planned | **P3 core** |
| [[domains/procurement/supplier-catalogue\|Supplier Catalogue]] | `procurement.catalogue` | planned | P3 |
| [[domains/procurement/goods-receipt\|Goods Receipt Notes]] | `procurement.goods-receipt` | planned | P3 |
| [[domains/procurement/spend-analytics\|Spend Analytics]] | `procurement.spend` | planned | P3 |
| [[domains/procurement/approvals\|Procurement Approvals]] | `procurement.approvals` | planned | P3 |

---

## Key Patterns

- `spatie/laravel-model-states` — requisition status, match status
- Shares PO/GRN entities with [[domains/operations/_index]] when both active
- Approval matrix drives requisition + PO routing
- Cross-domain: budget check (Finance), 3-way match → Finance AP payment release
- `spatie/laravel-pdf` — PO PDFs
