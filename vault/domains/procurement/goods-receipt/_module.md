---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.goods-receipt
status: planned
build-status: planned
priority: p3
depends-on: [operations.goods-receipt, finance.ap, core.billing, core.rbac]
soft-depends: []
fires-events: [ThreeWayMatchResolved]
consumes-events: []
patterns: [money]
tables: [proc_three_way_matches]
permission-prefix: procurement.goods-receipt
encrypted-fields: []
last-reviewed: 2026-07-02
color: "#4ADE80"
---

# Goods Receipt Notes (3-Way Match layer)

The GRN entity is owned by [[../../operations/goods-receipt/_module|operations.goods-receipt]] — this module adds the **3-way match approval gate**: compare PO ↔ GRN ↔ supplier bill, flag mismatches, and block payment until matched.

*(v2 simplification: operations GRN is a hard dep — the standalone-GRN v1 fallback is dropped. *(assumed — single GRN model))*

Hosted in **/operations** (Procurement nav → Purchase Orders). See [[../_index|Procurement MOC]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../operations/goods-receipt/_module\|operations.goods-receipt]] | the GRN entity |
| Hard | [[../../finance/accounts-payable/_module\|finance.ap]] | match gates bill payment (`MatchFailedException` path in `ApService::approveBill`) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |

---

## Core Features

- [[features/match-evaluation\|3-way match evaluation]] — PO ↔ GRN ↔ bill quantity/amount tolerance check, auto-approve within tolerance.
- [[features/discrepancy-resolution\|Discrepancy resolution]] — override/reject with notes + permission, audited.
- [[features/payment-gate\|Payment gate]] — bill cannot be paid until matched or overridden.

---

## Data Model

Full model + ERD: [[data-model]]. Owns `proc_three_way_matches`.

## DTOs

`ResolveMatchData`, `MatchData` (output) — [[api]].

## Services & Actions

`ThreeWayMatchService::evaluate` / `resolve`; `ApService::approveBill` hook. See [[architecture]] + [[api]].

---

## Filament

**Nav group:** Purchase Orders (Procurement)

| Artifact | UI kind | Feature |
|---|---|---|
| `ThreeWayMatchResource` | simple-resource | list/queue of matches |
| 3-way match / discrepancy board | custom-page | [[features/match-evaluation]] + [[features/discrepancy-resolution]] (side-by-side PO/GRN/bill compare) |

**Access contract:** `canAccess() = Auth::user()->can('procurement.goods-receipt.view-any') && BillingService::hasModule('procurement.goods-receipt')` — [[../../../architecture/filament-patterns]] #1. See [[security]].

---

## Permissions

`procurement.goods-receipt.view-any` · `procurement.goods-receipt.view-matches` · `procurement.goods-receipt.resolve` · `procurement.goods-receipt.override`

---

## Cross-Domain Edges

- **Consumes (read):** PO + line data (`operations.purchase-orders`), GRN receipts (`operations.goods-receipt`), supplier bills (`finance.ap`) — all read-only to compute variances.
- **Fires:** `ThreeWayMatchResolved` (company_id scalar + bill_id + approved_for_payment) → finance AP reacts (unblock/hold payment) via its own listener. The AP `approveBill` gate is a **read** hook (raises `MatchFailedException`), not a write into AP tables.
- **Data ownership:** writes **only** `proc_three_way_matches`. It does **not** write `ops_grn*`, `ops_purchase_orders`, or `finance_ap_*` — the payment gate is enforced by Finance's own service consulting this module's match state / event. See [[../../../security/data-ownership]].

Detail: [[decisions]] · [[unknowns]].

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Within-tolerance match auto-approves
- [ ] Quantity + amount discrepancies flagged with variance (brick/money)
- [ ] Bill approval blocked until matched/overridden when module active
- [ ] Override requires notes + permission, audited
- [ ] Non-PO bills unaffected

## Build Manifest

```
database/migrations/xxxx_create_proc_three_way_matches_table.php
app/Models/Procurement/ThreeWayMatch.php
app/Data/Procurement/{ResolveMatchData,MatchData}.php
app/Services/Procurement/ThreeWayMatchService.php
app/Events/Procurement/ThreeWayMatchResolved.php
app/Filament/Operations/Resources/ThreeWayMatchResource.php
app/Filament/Operations/Pages/ThreeWayMatchBoard.php
database/factories/Procurement/ThreeWayMatchFactory.php
tests/Feature/Procurement/ThreeWayMatchTest.php
```

## Related

- [[../../operations/goods-receipt/_module]] · [[../../finance/accounts-payable/_module]] · [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
