---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.goods-receipt
status: planned
priority: p3
depends-on: [operations.goods-receipt, finance.ap, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [money]
tables: [proc_three_way_matches]
permission-prefix: procurement.goods-receipt
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Goods Receipt Notes (3-Way Match layer)

The GRN entity is owned by [[domains/operations/goods-receipt|operations.goods-receipt]] — this module adds the **3-way match approval gate**: compare PO ↔ GRN ↔ supplier bill, flag mismatches, and block payment until matched.

*(v2 design simplification: operations GRN is a hard dep — the standalone-GRN fallback from the v1 spec is dropped* *(assumed — single GRN model))*

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/operations/goods-receipt\|operations.goods-receipt]] | the GRN entity |
| Hard | [[domains/finance/accounts-payable\|finance.ap]] | match gates bill payment (`MatchFailedException` path in `ApService::approveBill`) |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- 3-way match: compare PO, GRN, and supplier bill — quantity + amount tolerance check (±2% or €10 *(assumed defaults, configurable)*)
- Match approval gate: bill cannot be approved/paid until matched (or override)
- Match record per (PO, GRN, bill) triple, auto-created when all three exist
- Discrepancy resolution workflow: note + manual override with `procurement.goods-receipt.override` permission, audited
- Service confirmation for non-physical purchases (GRN without stock items — service lines *(assumed: handled as ops GRN with service flag)*)
- Receipt + match history

---

## Data Model

### proc_three_way_matches

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| po_id / grn_id / bill_id | ulid | unique triple |
| match_status | string | matched / quantity-discrepancy / amount-discrepancy / overridden |
| approved_for_payment | boolean default false | |
| variance_cents | bigint | bill − (GRN accepted × PO price) |
| notes | text nullable | required on override |
| matched_at | timestamp | |

---

## DTOs

### ResolveMatchData — match_id, action (in:override-approve,reject-bill), notes (required)

## Services & Actions

- `ThreeWayMatchService::evaluate(string $billId): MatchData` — finds PO/GRN via bill links, computes variances, sets status; auto-approves within tolerance
- `ThreeWayMatchService::resolve(ResolveMatchData)` — override path, audited
- Hook into `ApService::approveBill`: when procurement active and PO-linked, require `approved_for_payment`

---

## Filament

**Nav group:** Purchase Orders (Procurement)

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ThreeWayMatchResource` | #1 CRUD resource | match queue, variance columns, resolve action |

---

## Permissions

`procurement.goods-receipt.view-matches` · `procurement.goods-receipt.resolve` · `procurement.goods-receipt.override`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Within-tolerance match auto-approves
- [ ] Quantity + amount discrepancies flagged with variance (brick/money)
- [ ] Bill approval blocked until matched/overridden when module active
- [ ] Override requires notes + permission, audited
- [ ] Non-PO bills unaffected

---

## Build Manifest

```
database/migrations/xxxx_create_proc_three_way_matches_table.php
app/Models/Procurement/ThreeWayMatch.php
app/Data/Procurement/{ResolveMatchData,MatchData}.php
app/Services/Procurement/ThreeWayMatchService.php
app/Filament/Operations/Resources/ThreeWayMatchResource.php
database/factories/Procurement/ThreeWayMatchFactory.php
tests/Feature/Procurement/ThreeWayMatchTest.php
```

---

## Related

- [[domains/operations/goods-receipt]]
- [[domains/finance/accounts-payable]]
