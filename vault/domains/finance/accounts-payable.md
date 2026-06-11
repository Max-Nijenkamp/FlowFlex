---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.ap
status: complete
priority: v1
depends-on: [finance.ledger, core.billing, core.rbac, core.files]
soft-depends: [operations.purchase-orders, procurement.goods-receipt, finance.expenses]
fires-events: []
consumes-events: [GoodsReceived]
patterns: [states, money, custom-pages, events]
tables: [fin_suppliers, fin_bills, fin_bill_lines, fin_payment_runs]
permission-prefix: finance.ap
encrypted-fields: ["fin_suppliers.iban"]
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Accounts Payable

Supplier bill management, payment scheduling, AP aging, and approval workflow. Receives bills from Operations/Procurement; pays suppliers.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/general-ledger\|finance.ledger]] | bills + payments post to GL |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, bill attachments |
| Soft | [[domains/operations/purchase-orders\|operations.purchase-orders]] + [[domains/operations/goods-receipt\|operations.goods-receipt]] | consumes `GoodsReceived` → draft bill + 3-way match (contract in [[architecture/event-bus]]); manual bills until operations builds |
| Soft | [[domains/finance/expenses\|finance.expenses]] | non-employee reimbursements as bills |

---

## Core Features

- Bill record: supplier, bill number, amount, due date, status, linked PO
- Status machine: `draft → approved → scheduled → paid` (spatie/laravel-model-states)
- Bill approval workflow (by amount threshold — config in company settings *(assumed: single threshold)*)
- 3-way match gate: bill matched to PO + goods receipt before payment (when Procurement active; bypassed otherwise)
- Payment scheduling: batch payments by due date
- AP aging report: current, 30, 60, 90+ days
- Payment run: select bills, generate payment batch (SEPA export file *(assumed: pain.001 CSV/XML deferred — v1 = batch list export)*)
- Early-payment discount handling
- Posts to General Ledger on approval (liability) and payment (cash)

---

## Data Model

### fin_suppliers *(new vs v1 spec)*

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| email | string nullable | |
| vat_number | string nullable | |
| 🔐 iban | text nullable | encrypted, `iban_last4` display |
| payment_terms_days | int default 30 | |
| deleted_at | timestamp nullable | |

### fin_bills

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed), supplier_id FK | ulid | | |
| bill_number | string | unique `(company_id, supplier_id, bill_number)` | supplier's number |
| po_id | ulid | nullable | operations link |
| amount_cents | bigint | > 0 | |
| currency | string(3) | | |
| bill_date / due_date | date | due ≥ bill | |
| status | string default `draft` | state machine | |
| early_discount_percent / early_discount_until | decimal / date nullable | | |
| approved_by | ulid nullable | | |
| paid_at | timestamp nullable | | |
| payment_run_id | ulid nullable FK | | |
| deleted_at | timestamp nullable | kept 7y | |

**Indexes:** `(company_id, status, due_date)`

### fin_bill_lines

| Column | Type | Notes |
|---|---|---|
| id, bill_id FK, company_id | ulid | |
| description | string | |
| account_id | ulid FK fin_accounts | expense account |
| amount_cents | bigint | sum(lines) == bill amount (cross-check) |

### fin_payment_runs

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| run_date | date | |
| total_cents | bigint | |
| status | string default `draft` | draft / executed |

---

## State Machine

Column: `fin_bills.status` — `BillState`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `approved` | `finance.ap.approve` (above threshold: `finance.ap.approve-large` *(assumed)*) | 3-way match gate when procurement active; GL liability posted |
| `approved` | `scheduled` | added to payment run | |
| `scheduled` | `paid` | payment run executed | GL cash entry; `paid_at`; early discount applied if within window |
| `draft` / `approved` | `voided` *(assumed)* | `finance.ap.approve` | reversal if posted |

Audited.

---

## DTOs

### CreateBillData — supplier_id, bill_number, bill_date/due_date (due ≥ bill), lines[{description, account_id, amount_cents}] min:1, attachment (pdf optional), po_id (nullable)
Cross-field: sum(lines) defines bill amount; duplicate `(supplier, bill_number)` rejected — "This supplier bill number already exists."
### CreatePaymentRunData — run_date, bill_ids[] (each `approved`, same currency *(assumed)*)

## Services & Actions

Interface→Service: `ApServiceInterface` → `ApService`.

- `createBill(CreateBillData $data): BillData`
- `approveBill(string $billId): BillData` — 3-way match check (`MatchFailedException` when PO/GRN mismatch and procurement active); GL post
- `createPaymentRun(CreatePaymentRunData $data): PaymentRunData`
- `executeRun(string $runId): PaymentRunData` — marks bills paid, GL cash entries, applies early discounts
- `aging(): ApAgingData`

---

## Filament

**Nav group:** Expenses

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SupplierResource` | #1 CRUD resource | masked IBAN |
| `BillResource` | #1 CRUD resource | approve action, match status badge |
| `ApAgingPage` | #9 report custom page | |
| `PaymentRunPage` | #7 custom page | bill selection by due date, totals, execute |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('finance.ap.view-any') && BillingService::hasModule('finance.ap')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Upload contract** (medium): Specify pdf MIME whitelist, max size, and companies/{company_id}/ap-bills/ storage path for bill attachments.

---

## Permissions

`finance.ap.view-any` · `finance.ap.create` · `finance.ap.approve` · `finance.ap.approve-large` · `finance.ap.schedule` · `finance.ap.execute-run` · `finance.ap.manage-suppliers` · `finance.ap.view-sensitive`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Duplicate supplier bill number rejected
- [ ] Approval posts balanced GL liability entry; payment posts cash entry
- [ ] Threshold routes to approve-large permission
- [ ] 3-way match blocks mismatched bill when procurement active; bypassed when inactive
- [ ] Early discount applied only within window (brick/money)
- [ ] Payment run executes all bills atomically; lines sum check enforced
- [ ] Supplier IBAN encrypted + masked

---

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

---

## Related

- [[domains/finance/general-ledger]]
- [[domains/operations/purchase-orders]]
- [[domains/procurement/goods-receipt]]
- [[architecture/patterns/encryption]]
