---
type: module
domain: Legal & Compliance
domain-key: legal
panel: legal
module-key: legal.spend
status: planned
priority: p3
depends-on: [legal.matters, core.billing, core.rbac]
soft-depends: [finance.ap]
fires-events: []
consumes-events: []
patterns: [money, custom-pages]
tables: [legal_expenses, legal_budgets]
permission-prefix: legal.spend
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Legal Spend

Track legal costs: external counsel invoices, spend per matter, budget vs actual. Control legal department costs.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/legal/matter-management\|legal.matters]] | spend rolls up per matter |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/finance/accounts-payable\|finance.ap]] | approved legal expenses can be created as AP bills (manual link v1 *(assumed)*) |

---

## Core Features

- Legal expense record: matter, vendor (law firm), amount, date, category, invoice reference
- Spend per matter: roll up all costs against a matter
- Budget per matter or per period
- Budget vs actual variance with over-budget flag
- Vendor spend breakdown (which firms cost most)
- Invoice approval workflow (approve before counting; approver ≠ submitter *(assumed)*)
- Spend reports by matter, vendor, category, period

---

## Data Model

### legal_expenses

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), matter_id FK | ulid | |
| vendor | string | law firm |
| amount_cents | bigint > 0 | |
| currency | string(3) | |
| expense_date | date | |
| category | string | counsel / court / filing / other *(assumed)* |
| invoice_reference | string nullable | unique `(company_id, vendor, invoice_reference)` |
| status | string default `pending` | pending / approved / rejected |
| approved_by | ulid nullable | |
| fin_bill_id | ulid nullable | AP link |
| deleted_at | timestamp nullable | |

### legal_budgets — id, company_id (indexed), matter_id nullable (null = period budget), period (string), budget_cents; unique `(company_id, matter_id, period)`

---

## DTOs

### CreateLegalExpenseData — matter_id (accessible), vendor, amount_cents (min:1), expense_date (≤ today), category (in set), invoice_reference? ("This vendor invoice is already recorded.")
### SetBudgetData — matter_id?, period, budget_cents

## Services & Actions

- `LegalSpendService::approve/reject` — approver ≠ submitter
- `LegalSpendService::matterSpend(string $matterId): Money` (approved only)
- `LegalSpendService::variance(?string $matterId, string $period): VarianceData`

---

## Filament

**Nav group:** Spend

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `LegalExpenseResource` | #1 CRUD resource | approve action, matter/vendor filters |
| `LegalSpendDashboardPage` | #6 dashboard page | budget vs actual + vendor breakdown (apex) |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('legal.spend.view-any') && BillingService::hasModule('legal.spend')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`legal.spend.view-any` · `legal.spend.create` · `legal.spend.approve` · `legal.spend.manage-budgets`

---

## Test Checklist

- [ ] Tenant isolation + module gating + matter-confidentiality inheritance
- [ ] Duplicate vendor invoice rejected
- [ ] Only approved expenses count in spend/variance (brick/money)
- [ ] Approver ≠ submitter
- [ ] Variance flags over-budget

---

## Build Manifest

```
database/migrations/xxxx_create_legal_expenses_table.php
database/migrations/xxxx_create_legal_budgets_table.php
app/Models/Legal/{LegalExpense,LegalBudget}.php
app/Data/Legal/{CreateLegalExpenseData,SetBudgetData}.php
app/Services/Legal/LegalSpendService.php
app/Filament/Legal/Resources/LegalExpenseResource.php
app/Filament/Legal/Pages/LegalSpendDashboardPage.php
database/factories/Legal/{LegalExpenseFactory,LegalBudgetFactory}.php
tests/Feature/Legal/LegalSpendTest.php
```

---

## Related

- [[domains/legal/matter-management]]
- [[domains/finance/accounts-payable]]
