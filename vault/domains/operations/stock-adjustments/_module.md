---
domain: operations
module: stock-adjustments
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Stock Adjustments

Manual stock corrections for damage, loss, stocktake reconciliation, and write-offs. Every adjustment is logged with a reason; high-value ones need approval.

> Operations hosts the [[../../procurement/_index|Procurement]] panel. See [[../../../decisions/decision-2026-06-01-panel-consolidation]].

---

## Module-key

`operations.adjustments`

**Priority:** p3
**Panel:** operations (Orange)
**Permission prefix:** `operations.adjustments`
**Tables:** `ops_stock_adjustments`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../inventory/_module\|operations.inventory]] | adjustments post via `StockService::move(adjust)` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../finance/general-ledger/_module\|finance.ledger]] | GL write-off posting **deferred** — v1 produces a write-off report for finance to journal manually *(assumed)* |

---

## Core Features

- Adjustment record: item, warehouse, quantity delta (±), reason, date
- Reason codes: damage, loss, theft, stocktake correction, write-off, found
- Stocktake mode: bulk count entry; system computes deltas vs recorded levels
- Approval above a value threshold (company setting *(assumed €500)*) — pending until approved, stock untouched; approver ≠ adjuster
- Applied adjustment posts a stock movement + records value impact
- Audit trail + adjustment report by reason/period

See features: [[./features/adjustment-approval|Adjustment & Approval]] · [[./features/stocktake|Stocktake]] · [[./features/write-off-report|Write-Off Report]].

---

## Build Manifest

```
database/migrations/xxxx_create_ops_stock_adjustments_table.php
app/Models/Operations/StockAdjustment.php
app/Data/Operations/{CreateAdjustmentData,StocktakeData}.php
app/Services/Operations/AdjustmentService.php
app/Filament/Operations/Resources/StockAdjustmentResource.php
app/Filament/Operations/Pages/StocktakePage.php
database/factories/Operations/StockAdjustmentFactory.php
tests/Feature/Operations/{StockAdjustmentTest,StocktakeTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Above-threshold adjustment pending; stock unchanged until approval; approver ≠ adjuster
- [ ] Applied adjustment creates movement + value impact (brick/money)
- [ ] Negative beyond available rejected
- [ ] Stocktake computes deltas vs current levels correctly
- [ ] Report sums by reason/period

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads / calls | `StockService::move(adjust)` | operations.inventory (same domain) | applies the delta |
| Reads | write-off report | finance.ledger | **manual** — v1 exports a report; no automated GL posting |

**Data ownership:** `operations.adjustments` writes only `ops_stock_adjustments`. Stock deltas are applied via inventory's `StockService`; the GL write-off is **not** written here — v1 hands finance a report to journal manually (automated posting deferred). No cross-domain writes ([[../../../security/data-ownership]]).

---

## Related

- [[../inventory/_module|operations.inventory]]
- [[../../finance/general-ledger/_module|finance.ledger]]
- [[../_index|Operations MOC]]
