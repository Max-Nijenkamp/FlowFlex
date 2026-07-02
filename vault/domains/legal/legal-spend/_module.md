---
domain: legal
module: legal-spend
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Legal Spend

Track legal costs: external counsel invoices, spend per matter, budget vs actual. Control legal department costs.

---

## Module-key

`legal.spend`

**Priority:** p3
**Panel:** legal
**Permission prefix:** `legal.spend`
**Tables:** `legal_expenses`, `legal_budgets`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../matter-management/_module\|legal.matters]] | spend rolls up per matter |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../../finance/accounts-payable/_module\|finance.ap]] | approved legal expenses can become AP bills (manual link v1 *(assumed)*) |

---

## Core Features

- [[./features/expense-records|Expense records]] — matter, vendor, amount, category, invoice reference (dedup)
- [[./features/invoice-approval|Invoice approval]] — approve before counting; approver ≠ submitter
- [[./features/budget-vs-actual|Budget vs actual]] — per matter / period variance, vendor breakdown, over-budget flag

Full data model + service math: [[./data-model]] · [[./architecture]].

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

## Test Checklist

- [ ] Tenant isolation: company A cannot see/create/approve company B expenses or budgets
- [ ] Module gating: artifacts hidden when `legal.spend` inactive
- [ ] Matter-confidentiality inheritance: confidential-matter spend hidden from non-listed users
- [ ] Duplicate vendor invoice rejected
- [ ] Only approved expenses count in spend/variance (brick/money)
- [ ] Approver ≠ submitter
- [ ] Variance flags over-budget

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `MatterService::accessibleFor` | legal.matters | expenses attach to matters + inherit confidentiality scope |
| Reads/Feeds | manual AP-bill link | finance.ap | approved expense → AP bill created manually v1; `fin_bill_id` stored here as reference |

**Data ownership:** `legal.spend` writes only `legal_expenses`, `legal_budgets`. It never writes finance.ap tables — an AP bill is created by finance's own flow and its id is stored here as a read reference ([[../../../security/data-ownership]]).

---

## Related

- [[../matter-management/_module|legal.matters]]
- [[../../finance/accounts-payable/_module|finance.ap]]
- [[./decisions]] · [[./unknowns]] · [[./security]] · [[./api]]
