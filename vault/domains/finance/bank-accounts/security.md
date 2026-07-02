---
domain: finance
module: bank-accounts
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Bank Accounts — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.bank.view-any')
            && BillingService::hasModule('finance.bank')
```

per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.bank.view-any` · `finance.bank.manage-accounts` · `finance.bank.import` · `finance.bank.reconcile` · `finance.bank.view-sensitive` (full IBAN)

## Encryption

`fin_bank_accounts.iban` and `fin_bank_accounts.account_number` are encrypted at rest (`encrypted` cast, `text` column). Display is masked to last-4 (`iban_last4` *(assumed)*); the full value is gated behind `finance.bank.view-sensitive`. See [[../../../security/encryption]] and [[../../../architecture/patterns/encryption]].

## Upload contract

CSV statement uploads enforce a MIME whitelist (`text/csv`) and store under `companies/{company_id}/bank-imports/` so files are tenant-isolated. Reference the Security upload rules in [[../../../architecture/security]].

## Rate limiting

A rate limiter is intended on the import action (e.g. N imports per company per minute) in addition to the queued chunked job, per [[../../../architecture/api-design]] and [[../../../architecture/security]].

## Integrity controls

- Reconcile rejects mismatched amounts (`AmountMismatchException`).
- Re-importing the same statement creates zero duplicates via the `import_hash` unique constraint.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].
