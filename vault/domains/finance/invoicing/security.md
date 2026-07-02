---
domain: finance
module: invoicing
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Invoicing — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.invoicing.view-any')
            && BillingService::hasModule('finance.invoicing')
```

per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.invoicing.view-any` · `finance.invoicing.view` · `finance.invoicing.create` · `finance.invoicing.update` · `finance.invoicing.send` · `finance.invoicing.record-payment` · `finance.invoicing.void` · `finance.invoicing.manage-customers`

## Rate limiting

A per-user/per-company rate limiter (`RateLimiter::for`) is intended on the export action and the PDF-generation endpoint, per [[../../../architecture/api-design]] and [[../../../architecture/security]].

## Integrity controls

- Paid invoices cannot be voided (`CannotVoidPaidInvoiceException`); voiding a posted invoice writes a ledger reversal rather than mutating prior postings.
- Invoice numbers are assigned at first send, never reused, gap-free per company, and audited.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
