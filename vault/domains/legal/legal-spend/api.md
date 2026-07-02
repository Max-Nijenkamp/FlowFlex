---
domain: legal
module: legal-spend
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Legal Spend — Service API

## DTOs

- `CreateLegalExpenseData` — matter_id (accessible), vendor, amount_cents (min:1), expense_date (≤ today), category (in set), invoice_reference? ("This vendor invoice is already recorded.").
- `SetBudgetData` — matter_id?, period, budget_cents.

## Methods

| Method | Purpose | Writes |
|---|---|---|
| `LegalSpendService::record(CreateLegalExpenseData)` | new pending expense | `legal_expenses` |
| `LegalSpendService::approve(id, approver)` | approver ≠ submitter | `legal_expenses` |
| `LegalSpendService::reject(id, reason)` | reject | `legal_expenses` |
| `LegalSpendService::matterSpend(matterId): Money` | approved total | (read) |
| `LegalSpendService::variance(?matterId, period): VarianceData` | budget vs actual | (read) |
| `LegalSpendService::setBudget(SetBudgetData)` | set/replace budget | `legal_budgets` |

## Read surface (consumed by others)

- `legal.matters` reads `matterSpend` for the matter spend summary.

No events in v1. AP bill link is a manually-stored `fin_bill_id` reference.
