---
domain: core
module: api-clients
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# API Clients — Security

Parent: [[_module]] · See also [[architecture]] · [[api]]

## Permissions

`core.api.view-any` · `core.api.create` · `core.api.revoke`

Only admins manage tokens.

## Authorization

The Filament resource gates on:
`canAccess() = Auth::user()->can('core.api.view-any') && BillingService::hasModule('core.api')`
per [[../../../architecture/filament-patterns]] #1. See [[../../../security/authn-authz]].

## Ability scoping

Every token carries an explicit `abilities` array. API routes enforce `ability:{domain}:{verb}`, so a `hr:read` token cannot POST to hr endpoints. Abilities may only reference domains whose module is active for the company.

## Module gating

`EnforceModuleAccess` (`module:{module-key}`) rejects API calls to any module the company has not activated → 403. This mirrors the Filament `canAccess()` module gate on the REST edge.

## Create-once secret

The plain token is returned exactly once at creation (copy-once modal) and stored only as a SHA-256 hash. It is never retrievable afterward; a lost token must be revoked and recreated.

## Tenancy

Requests execute in the context of the token's owning user, so company A's token cannot read company B's data. Tokens are intended to belong to a dedicated per-company service user *(assumed)* so integrations survive staff turnover. See [[../../../security/tenancy-isolation]].

## Rate limiting

Per-token throttles via the `api` / `api-write` limiters return 429 + `Retry-After` on exhaustion (see [[../../../architecture/security]]).
