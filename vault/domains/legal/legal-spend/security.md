---
domain: legal
module: legal-spend
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Legal Spend — Security

## Access contract

`canAccess() = Auth::user()->can('legal.spend.view-any') && BillingService::hasModule('legal.spend')` per [[../../../architecture/filament-patterns]] #1.

## Confidentiality inheritance

Expenses are scoped to matters via `MatterService::accessibleFor` — spend on a confidential matter is only visible to that matter's owner + access-list users.

## Separation of duties

`approved_by` must differ from the expense submitter — enforced in `LegalSpendService::approve`.

## Permissions

`legal.spend.view-any` · `legal.spend.create` · `legal.spend.approve` · `legal.spend.manage-budgets`

## Data ownership

Writes only `legal_expenses`, `legal_budgets`. Never writes finance.ap — `fin_bill_id` is a stored reference to a bill finance created ([[../../../security/data-ownership]]).
