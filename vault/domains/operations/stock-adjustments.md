---
type: module
domain: Operations
domain-key: operations
panel: operations
module-key: operations.adjustments
status: planned
priority: p3
depends-on: [operations.inventory, core.billing, core.rbac]
soft-depends: [finance.ledger]
fires-events: []
consumes-events: []
patterns: [money, custom-pages]
tables: [ops_stock_adjustments]
permission-prefix: operations.adjustments
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Stock Adjustments

Manual stock corrections for damage, loss, stocktake reconciliation, and write-offs. Every adjustment is logged with a reason.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/operations/inventory\|operations.inventory]] | adjustments execute via `StockService::move(adjust)` |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/finance/general-ledger\|finance.ledger]] | GL write-off posting **deferred — v1 produces a write-off report for finance to journal manually** *(assumed)* |

---

## Core Features

- Adjustment record: item, warehouse, quantity delta (+/-), reason, date
- Reason codes: damage, loss, theft, stocktake correction, write-off, found
- Stocktake mode: bulk count entry, system calculates adjustments vs recorded levels
- Approval required for adjustments above a threshold value (company setting *(assumed: default €500)*) — pending until approved, stock untouched
- Adjustment creates a stock movement record on approval/immediate
- Audit trail: who adjusted, when, why, value impact
- Adjustment report by reason/period

---

## Data Model

### ops_stock_adjustments

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| item_id / warehouse_id | ulid FK | |
| quantity_delta | decimal(12,2) | ≠ 0, signed |
| reason_code | string | in set |
| notes | text nullable | |
| value_impact_cents | bigint | delta × item cost at time |
| status | string default `applied` | pending-approval / applied *(assumed)* |
| adjusted_by / approved_by | ulid / nullable | approver ≠ adjuster |
| created_at | timestamp | |

---

## DTOs

### CreateAdjustmentData — item_id, warehouse_id, quantity_delta (≠ 0; negative ≤ available), reason_code (in set), notes (required for theft/write-off *(assumed)*)
### StocktakeData — warehouse_id, counts[{item_id, counted_quantity ≥ 0}] — deltas computed vs levels

## Services & Actions

- `AdjustmentService::adjust(CreateAdjustmentData)` — over threshold → pending; else apply (`StockService::move`)
- `AdjustmentService::approve(string $adjustmentId)` — applies movement; approver ≠ adjuster
- `AdjustmentService::stocktake(StocktakeData): StocktakeResult` — bulk adjustments with reason `stocktake correction`

---

## Filament

**Nav group:** Inventory

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `StockAdjustmentResource` | #1 CRUD resource | approve action, pending tab, reason/period report filters |
| `StocktakePage` | #7 custom page | warehouse pick → count grid → preview deltas → confirm |

---

## Permissions

`operations.adjustments.view-any` · `operations.adjustments.create` · `operations.adjustments.approve`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Above-threshold adjustment pending; stock unchanged until approval; approver ≠ adjuster
- [ ] Applied adjustment creates movement + value impact (brick/money)
- [ ] Negative beyond available rejected
- [ ] Stocktake computes deltas vs current levels correctly
- [ ] Report sums by reason/period

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

## Related

- [[domains/operations/inventory]]
- [[domains/finance/general-ledger]]
