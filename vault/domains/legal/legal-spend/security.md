---
domain: legal
module: legal-spend
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Legal Spend — Security

## Access contract

`canAccess() = Auth::user()->can('legal.spend.view-any') && BillingService::hasModule('legal.spend')` per [[../../../architecture/filament-patterns]] #1.

## Confidentiality inheritance

Expenses are scoped to matters via `MatterService::accessibleFor` — spend on a confidential matter is only visible to that matter's owner + access-list users.

## Separation of duties

`approved_by` must differ from the expense submitter — enforced in `LegalSpendService::approve`.

## Permissions

| Permission | Grants |
|---|---|
| `legal.spend.view-any` | List + dashboard (still filtered by matter confidentiality) |
| `legal.spend.view` | View a single expense |
| `legal.spend.create` | Record an expense |
| `legal.spend.update` | Edit a pending expense |
| `legal.spend.delete` | Soft-delete an expense |
| `legal.spend.approve` | Approve a pending expense (`approver ≠ submitter`) |
| `legal.spend.reject` | Reject a pending expense |
| `legal.spend.manage-budgets` | Set / edit budgets |

Verb-per-command: `approve` and `reject` are the two `pending →` transitions in [[./architecture]]; each has its
own permission. Seeded in `PermissionSeeder`.

## Rate Limiting

- `approve` / `reject` are money-affecting panel actions → named `panel-action` rate limiter.
- Dashboard report export (file generation) → named export/`panel-action` rate limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

## Data ownership

Writes only `legal_expenses`, `legal_budgets`. Never writes finance.ap — `fin_bill_id` is a stored reference to a bill finance created ([[../../../security/data-ownership]]).
