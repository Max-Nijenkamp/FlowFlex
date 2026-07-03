---
domain: finance
module: accounts-receivable
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Accounts Receivable — Security

## Access contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('finance.ar.view-any')
            && BillingService::hasModule('finance.ar')
```

per [[../../../architecture/filament-patterns]] #1 — custom pages (`ArAgingPage`, `CustomerStatementPage`) state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

## Permissions

`finance.ar.view-any` · `finance.ar.view` · `finance.ar.manage-dunning` · `finance.ar.write-off` · `finance.ar.allocate-payment`

Verb-per-command: `manage-dunning` (dunning-rule CRUD), `allocate-payment` (split a payment across invoices — money), `write-off` (post a bad-debt GL entry — money). AR has no user-driven state machine; dunning escalation is an automated scheduled command, not a permissioned transition.

## Rate Limiting

Comms-sending, money-mutating, and file-generating paths carry a **named** rate limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]:

- `ProcessDunningCommand` → `DunningMail` **sends comms** — the send is throttled per company (`panel-action` category / a dedicated dunning limiter) so a rule misconfiguration cannot flood a customer; the `last_dunning_level` guard already caps one send per escalation level.
- `allocatePayment` and `writeOff` **mutate money** → `panel-action` limiter on the triggering action.

See [[../../../architecture/security]], [[../../../architecture/email]], and [[../../../architecture/api-design]].

## Integrity controls

- Write-off is permission-gated (`finance.ar.write-off`) and records the approving user on `fin_ar_writeoffs.approved_by`.
- Payment allocation validates that the allocations sum to the payment amount and that no allocation exceeds an invoice's open balance.
- Tenant isolation enforced on every table via `company_id` — see [[../../../security/tenancy-isolation]] and [[../../../security/authn-authz]].

No encrypted fields in this module.
