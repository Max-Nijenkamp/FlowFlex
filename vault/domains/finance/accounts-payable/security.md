---
domain: finance
module: accounts-payable
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Payable — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.ap.view-any')
            && BillingService::hasModule('finance.ap')
```

per [[../../../architecture/filament-patterns]] #1 — custom pages (`ApAgingPage`, `PaymentRunPage`) state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.ap.view-any` · `finance.ap.create` · `finance.ap.approve` · `finance.ap.approve-large` · `finance.ap.schedule` · `finance.ap.execute-run` · `finance.ap.manage-suppliers` · `finance.ap.view-sensitive`

## Encrypted fields

`fin_suppliers.iban` is stored as `encrypted` (text column), surfaced only as `iban_last4` in the UI. Viewing the full value requires `finance.ap.view-sensitive`. See [[../../../architecture/patterns/encryption]].

## Upload contract

Bill attachments (medium-severity audit item, [[../../../security/security-audit-2026-06-11]]): restrict to a PDF MIME whitelist, enforce a max size, and store under `companies/{company_id}/ap-bills/`.

## Integrity controls

- Approval routes above the threshold to the `finance.ap.approve-large` permission *(assumed: single threshold)*.
- 3-way match blocks payment on PO/GRN mismatch when procurement is active.
- Payment runs execute atomically; bill-line sums are validated against the bill amount.
- State transitions on `fin_bills.status` are audited.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].
