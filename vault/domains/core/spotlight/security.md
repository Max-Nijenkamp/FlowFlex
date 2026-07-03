---
domain: core
module: spotlight
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Spotlight — Security

Parent: [[_module]]

Spotlight surfaces navigation and records fast, so its filtering is a security boundary, not a convenience.

## Permissions

None — Spotlight defines no permission strings. It has no commands, state transitions, or writes; authorization is entirely delegated to each panel's own `canAccess()` boundary (below). `core.spotlight` is platform chrome (always active), so there is no module-gating verb either.

## Every result is `canAccess()`-filtered

All three sources ([[architecture]]) pass through authorization:

- Resource nav / quick-create only appear when the user `canAccess()` the resource (and `canCreate()` for quick-create).
- Page nav only appears when the page is accessible.
- Global-search results come from the panel's own provider, which honours per-record authorization.

The palette can never link a user to something their `canAccess()` would deny on arrival.

## Authenticated-only render

The `BODY_END` hook renders the component **only when `Filament::auth()->check()`**. Login pages — which have no panel user and no tenant context — never mount Spotlight, so there is no pre-auth search surface. Covered by `SpotlightTest` (renders on authenticated page, NOT on login).

## Per-panel scoping — no cross-tenant leak

The component is bound to a single `panelId` and restores that panel's context before querying. Results are drawn from that one panel's Resources/Pages/search provider, each already company-scoped via `CompanyScope`. There is no cross-panel or cross-tenant aggregation — a user in company A's panel sees only company A's records. See [[../../../security/authn-authz]].

## Related

- [[_module]] · [[architecture]]
- [[../../../security/authn-authz]] · [[../../../architecture/filament-patterns]]
