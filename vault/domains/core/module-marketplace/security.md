---
domain: core
module: module-marketplace
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Module Marketplace — Security

Parent: [[_module]]

## Permissions

`core.marketplace.view` · activation actions gated by `core.billing.activate-module` / `core.billing.deactivate-module` (owner/admin).

## Authorization

`canAccess() = Auth::user()->can('core.marketplace.view-any') && BillingService::hasModule('core.marketplace')` per [[../../../architecture/filament-patterns]] #1.

**Owner-only** in practice — permission alone is insufficient (see [[decisions]]). See [[../../../security/authn-authz]].

## Rate Limiting

The activate / deactivate card actions delegate to `BillingServiceInterface` (which calls external Stripe subscription APIs). They carry the `panel-action` named limiter at the marketplace surface; the billing mutation itself is rate-limited by [[../billing-engine/security|core.billing]].

## Tenancy

All reads are company-scoped; activation state is per-company via `company_module_subscriptions`. See [[../../../security/tenancy-isolation]].

No own PII / encrypted fields.
