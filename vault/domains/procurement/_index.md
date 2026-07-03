---
type: domain-index
domain: Procurement
domain-key: procurement
panel: operations
phase: 3
module-count: 6
status: active
build-status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Procurement — Map of Content

Purchase requisitions, sourcing POs, supplier catalogue, 3-way match, spend analytics, and approval workflows.

**Panel:** hosted in **/operations** (no panel of its own — see [[../../decisions/decision-2026-06-01-panel-consolidation]]). Its resources appear in the `/operations` panel under the **Procurement** nav group. Procurement layers on Operations' PO/GRN/supplier entities (hard deps) and hooks Finance AP for 3-way match. Phase 3, priority p3.

Opportunities / competitive gaps: [[_opportunities]].

---

## Modules

| Module | Key | Status | Intra-domain deps | Owns tables | Kind highlights |
|---|---|---|---|---|---|
| [[approvals/_module\|Procurement Approvals]] | `procurement.approvals` | planned | — (build first) | `proc_approval_rules`, `proc_approval_delegations` | 2 resources + #8 page |
| [[requisitions/_module\|Purchase Requisitions]] | `procurement.requisitions` | planned | approvals | `proc_requisitions`, `proc_requisition_items`, `proc_requisition_approvals` | resource |
| [[supplier-catalogue/_module\|Supplier Catalogue]] | `procurement.catalogue` | planned | — | `proc_catalogue_items`, `proc_supplier_status` | 2 resources |
| [[purchase-orders/_module\|Purchase Orders (layer)]] | `procurement.purchase-orders` | planned | requisitions, approvals | `proc_po_sourcing` | resource + #9 page |
| [[goods-receipt/_module\|3-Way Match]] | `procurement.goods-receipt` | planned | — (ops GRN + finance.ap) | `proc_three_way_matches` | resource + #9 page |
| [[spend-analytics/_module\|Spend Analytics]] | `procurement.spend` | planned | requisitions | — (read-only) | #6 page |

---

## Dependency & flow graph

```mermaid
graph TD
    subgraph Procurement
      APP[approvals]
      CAT[supplier-catalogue]
      REQ[requisitions]
      PO[purchase-orders layer]
      TWM[3-way match]
      SPEND[spend-analytics]
    end

    APP -->|chainFor| REQ
    APP -->|chainFor| PO
    CAT -->|picker + SupplierGate| REQ
    CAT -->|sourcing + SupplierGate| PO
    REQ -->|convertToPo| PO
    REQ -->|RequisitionApproved| SPEND
    PO -->|committed/actual| SPEND
    CAT -->|savings/maverick| SPEND

    %% cross-domain (dashed = read or event, never a write into another domain)
    REQ -.read remaining.-> BUD`finance.budgets`
    PO -.createFromRequisition.-> OPO`operations.purchase-orders`
    PO ==>|PurchaseApproved event| FIN`finance.ap / operations`
    TWM -.reads PO+GRN+bill.-> OPO
    TWM -.reads.-> OGRN`operations.goods-receipt`
    TWM ==>|ThreeWayMatchResolved / gate| AP`finance.ap`

    classDef ext fill:#1f2937,stroke:#94a3b8,color:#e5e7eb;
    class BUD,OPO,OGRN,FIN,AP ext;
```

Dashed = read-only query; double-line = domain event. **No procurement module writes another domain's tables** — [[../../security/data-ownership]].

---

## Navigation Groups (within /operations)

- **Requisitions** — Purchase Requisitions
- **Purchase Orders** — POs (procurement layer), Sourcing, 3-Way Match
- **Suppliers** — Supplier Catalogue, Supplier Status, onboarding portal (public)
- **Reporting** — Spend Analytics
- **Settings** — Approval Rules, Delegations

---

## Cross-Domain Edges (summary)

| From (procurement) | To | Mechanism | Direction |
|---|---|---|---|
| approvals | core.rbac, hr.org | read roles/depts | read |
| requisitions | finance.budgets | `BudgetService::remaining()` | read (soft) |
| requisitions | operations.purchase-orders | `createFromRequisition` | call (Ops writes PO) |
| requisitions | spend / finance | `RequisitionApproved` | event |
| purchase-orders | approvals, catalogue | chainFor / SupplierGate | read |
| purchase-orders | finance.ap, operations | `PurchaseApproved` | event |
| goods-receipt | ops PO/GRN, finance.ap | read docs; `MatchFailedException` gate | read + event (`ThreeWayMatchResolved`) |
| spend-analytics | requisitions, ops POs, catalogue, budgets | aggregate | read-only (owns no tables) |

**Data ownership:** every procurement table is owned + written by exactly one module above; all cross-domain effects are read-only or event-driven — [[../../security/data-ownership]].

---

## Status Board (Dataview)

```dataview
TABLE module-key AS "Key", status AS "Status", priority AS "Priority"
FROM "domains/procurement"
WHERE type = "module"
SORT module-key ASC
```

---

## Key Patterns

- `spatie/laravel-model-states` — requisition status.
- `ApprovalMatrix::chainFor(type, amount, category)` — single routing API for requisitions + POs.
- `SupplierGate::isBlocked` — blacklisted suppliers blocked everywhere.
- All money integer cents (brick/money).

## Related

- [[_opportunities]] · [[../../security/data-ownership]] · [[../operations/_index]] · [[../finance/_index]] · [[../../architecture/patterns/feature-ui-spec]]
