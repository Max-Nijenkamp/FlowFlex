---
domain: legal
module: matter-management
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Matter Management — Security

## Access contract

`canAccess() = Auth::user()->can('legal.matters.view-any') && BillingService::hasModule('legal.matters')` per [[../../../architecture/filament-patterns]] #1.

## Confidentiality — second gate

`view-any` does **not** bypass confidential matters. A confidential matter is visible only to its owner and users in `access_list`, enforced centrally in `MatterService::accessibleFor`. Legal spend inherits this scope.

## Permissions

`legal.matters.view-any` · `legal.matters.create` · `legal.matters.update` · `legal.matters.close`

## Upload hardening (medium — per [[../../../build/security-audit-2026-06-11]])

- Matter-document Media Library collection: allowed document types, max size, `companies/{id}/`-scoped path.

## Data ownership

Writes only `legal_matters`, `legal_matter_events`; all links read-only ([[../../../security/data-ownership]]).
