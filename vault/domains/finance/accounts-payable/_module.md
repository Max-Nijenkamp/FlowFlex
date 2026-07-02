---
domain: finance
module: accounts-payable
type: module
module-key: finance.ap
priority: v1
build-status: planned
status: wip
depends-on: [finance.ledger, core.billing, core.rbac, core.files]
soft-depends: [operations.purchase-orders, procurement.goods-receipt, finance.expenses]
fires-events: []
consumes-events: [GoodsReceived]
patterns: [states, money, custom-pages, events]
tables: [fin_suppliers, fin_bills, fin_bill_lines, fin_payment_runs]
permission-prefix: finance.ap
encrypted-fields: ["fin_suppliers.iban"]
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Payable

Supplier bill management, payment scheduling, AP aging, and an approval workflow. Receives bills from Operations/Procurement and pays suppliers. Posts to the General Ledger on approval (liability) and on payment (cash).

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Purpose

AP is the outbound counterpart to AR: capture supplier bills, route them through approval by amount threshold, gate payment on a 3-way match against PO + goods receipt (when Procurement is active), batch them into payment runs, and post the corresponding liability and cash entries to the ledger. It owns supplier records (with encrypted IBAN), bills, bill lines, and payment runs.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../general-ledger/_module\|finance.ledger]] | bills + payments post to GL |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/file-storage/_module\|core.files]] | gating, permissions, bill attachments |
| Soft | [[../../operations/purchase-orders/_module\|operations.purchase-orders]] + [[../../operations/goods-receipt/_module\|operations.goods-receipt]] | consumes `GoodsReceived` → draft bill + 3-way match ([[../../../architecture/event-bus]]); manual bills until operations builds |
| Soft | [[../expenses/_module\|finance.expenses]] | non-employee reimbursements as bills |

## Core Features

- Bill record: supplier, bill number, amount, due date, status, linked PO.
- Status machine: `draft → approved → scheduled → paid` (spatie/laravel-model-states).
- Bill approval workflow by amount threshold (config in company settings) *(assumed: single threshold)*.
- 3-way match gate: bill matched to PO + goods receipt before payment (when Procurement active; bypassed otherwise).
- Payment scheduling: batch payments by due date.
- AP aging report: current, 30, 60, 90+ days.
- Payment run: select bills, generate a payment batch (SEPA export) *(assumed: pain.001 CSV/XML deferred — v1 = batch list export)*.
- Early-payment discount handling.
- Posts to General Ledger on approval (liability) and on payment (cash).

## Permissions

`finance.ap.view-any` · `finance.ap.create` · `finance.ap.approve` · `finance.ap.approve-large` · `finance.ap.schedule` · `finance.ap.execute-run` · `finance.ap.manage-suppliers` · `finance.ap.view-sensitive`

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Duplicate supplier bill number rejected
- [ ] Approval posts a balanced GL liability entry; payment posts a cash entry
- [ ] Threshold routes to the `approve-large` permission
- [ ] 3-way match blocks a mismatched bill when procurement active; bypassed when inactive
- [ ] Early discount applied only within the window (brick/money)
- [ ] Payment run executes all bills atomically; line-sum check enforced
- [ ] Supplier IBAN encrypted + masked

## Build Manifest

```
database/migrations/xxxx_create_fin_suppliers_table.php
database/migrations/xxxx_create_fin_bills_table.php
database/migrations/xxxx_create_fin_bill_lines_table.php
database/migrations/xxxx_create_fin_payment_runs_table.php
app/Models/Finance/{Supplier,Bill,BillLine,PaymentRun}.php
app/States/Finance/Bill/{BillState,Draft,Approved,Scheduled,Paid,Voided}.php
app/Data/Finance/{CreateBillData,CreatePaymentRunData,BillData,PaymentRunData,ApAgingData}.php
app/Contracts/Finance/ApServiceInterface.php
app/Services/Finance/ApService.php
app/Exceptions/Finance/MatchFailedException.php
app/Filament/Finance/Resources/{SupplierResource,BillResource}.php
app/Filament/Finance/Pages/{ApAgingPage,PaymentRunPage}.php
database/factories/Finance/{SupplierFactory,BillFactory}.php
tests/Feature/Finance/{BillApprovalTest,PaymentRunTest,ThreeWayMatchTest}.php
```

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_suppliers`, `fin_bills`, `fin_bill_lines`, `fin_payment_runs`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]). Supplier IBAN is encrypted at rest.

| Direction | Event / Call | Counterpart |
|---|---|---|
| Consumes | `GoodsReceived` → draft bill + 3-way match `> [!warning] UNVERIFIED` — procurement not built | [[../../procurement/goods-receipt/_module\|procurement.goods-receipt]] |
| Calls | `LedgerService::post` for liability + cash entries | [[../general-ledger/_module\|finance.ledger]] |
| Reads | tax rates | [[../tax-management/_module\|finance.tax]] |

## Entity Notes

- [[architecture]] — services, money handling, GL posting, event-driven draft bills
- [[data-model]] — tables + ERD (encrypted IBAN)
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, permissions, IBAN encryption, upload contract
- [[decisions]] — new-vs-v1 deviations (suppliers table, SEPA deferral, void state)
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/bill-approval]], [[features/three-way-match]], [[features/payment-runs]], [[features/ap-aging]]

## Related

- [[../general-ledger/_module]]
- [[../expenses/_module]]
- [[../../operations/purchase-orders/_module]]
- [[../../procurement/goods-receipt/_module]]
- [[../../../architecture/patterns/encryption]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
