---
domain: procurement
module: spend-analytics
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Spend Analytics — Data Model

**Owns no tables.** This module is a pure read/aggregation surface — it never persists anything. It reads (read-only) from the tables owned by other modules and returns an output DTO.

## Read sources (owned elsewhere)

```mermaid
flowchart TD
    subgraph procurement
      R[(proc_requisitions)]
      C[(proc_catalogue_items)]
    end
    subgraph operations
      PO[(ops_purchase_orders)]
      POL[(ops_po_lines)]
    end
    subgraph finance
      B[(finance budgets - soft)]
    end
    R & PO & POL & C & B --> M[SpendMetricsData - output only]
```

| Source table | Owner | Used for |
|---|---|---|
| `proc_requisitions` | procurement.requisitions | requested spend |
| `ops_purchase_orders`, `ops_po_lines` | operations.purchase-orders | committed/actual spend, supplier/category breakdown |
| `proc_catalogue_items` | procurement.catalogue (soft) | savings (agreed vs actual), maverick detection |
| finance budgets | finance.budgets (soft) | budget vs actual |

No migrations, no factories — see [[../../../security/data-ownership]] (a module with zero write tables cannot violate ownership).

## Related

- [[_module]] · [[architecture]] · [[api]]
