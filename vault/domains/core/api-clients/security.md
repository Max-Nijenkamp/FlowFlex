---
domain: core
module: api-clients
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# API Clients — Security

Parent: [[_module]] · See also [[architecture]] · [[api]]

## Permissions

`core.api.view-any` · `core.api.create` · `core.api.rotate` · `core.api.revoke`

One permission per token command: `create`, `rotate` (per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]), and `revoke` (covers single + revoke-all). Only admins manage tokens.

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

## Token expiry, rotation & company binding

Per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]:

- New tokens default to **90-day expiry** *(assumed — tunable)*; an expiry-warning notification fires 14 days out via `core.notifications`.
- `POST /api/v1/auth/tokens/{id}/rotate` issues a replacement with the same abilities + company binding and revokes the original (7-day grace overlap). Gated by `core.api.rotate`.
- A token is **explicitly bound to the issuing user's `company_id`** at creation; middleware sets the permission team context from the token's company, not the current user's, and tokens are revoked on company detach/offboarding. See [[../../../architecture/multi-tenancy]].

## Rate limiting

- **REST edge:** per-token throttles via the `api` / `api-write` limiters return 429 + `Retry-After` on exhaustion.
- **Per-company quota:** the `api-company` limiter (1000 req/min per `company_id` *(assumed)*) layers on top of per-token limits — one tenant's tokens cannot starve others; quota state in `X-RateLimit-Company-*` headers.
- **Panel actions:** the credential-issuing header actions (create / rotate) name the `panel-action` limiter (30/min per user) since they mint secrets — per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

See [[../../../architecture/security]] and [[../../../architecture/api-design]].
