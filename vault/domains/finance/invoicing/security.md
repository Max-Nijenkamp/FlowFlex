---
domain: finance
module: invoicing
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

Named limiters per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]:

- **Send** (`finance.invoicing.send`) sends customer email and generates a PDF → `panel-action` limiter.
- **Record payment** (`finance.invoicing.record-payment`) mutates money and posts a GL entry → `panel-action` limiter.
- **Void** (`finance.invoicing.void`) can post a ledger reversal → `panel-action` limiter.
- **Invoice list Excel export** (`pxlrbt/filament-excel`) → `exports` limiter.
- **Payment reminder mail** is dispatched by the scheduled `SendPaymentReminderCommand` (queued, not a panel action).

See [[../../../architecture/api-design]] and [[../../../architecture/security]].

## Integrity controls

- Paid invoices cannot be voided (`CannotVoidPaidInvoiceException`); voiding a posted invoice writes a ledger reversal rather than mutating prior postings.
- Invoice numbers are assigned at first send, never reused, gap-free per company, and audited.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
