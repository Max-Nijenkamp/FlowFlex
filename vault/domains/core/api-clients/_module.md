---
domain: core
module: api-clients
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# API Clients

API key management for the FlowFlex REST API. Company admins create Sanctum tokens with specific ability scopes, rotate or revoke them, and monitor last-used timestamps. Tokens are shown once at creation and stored only as a hash.

## Module-key

`core.api`

**Priority:** v1  
**Panel:** app  
**Permission prefix:** `core.api`  
**Tables:** `personal_access_tokens` (Sanctum, extended with `created_by` — owns no dedicated table)  
**Events:** fires none · consumes none

## Sibling notes

- [[architecture]] — DTO, actions, module-gating middleware + flow diagram
- [[data-model]] — Sanctum `personal_access_tokens` (no own tables)
- [[api]] — `CreateApiTokenData` DTO + action surface
- [[security]] — permissions, ability scoping, rate limits, create-once secret
- [[unknowns]] — UNVERIFIED / `*(assumed)*` items
- Features: [[features/token-scopes]] · [[features/token-lifecycle]]

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../rbac/_module]] | only admins manage tokens; abilities mirror permission domains |
| Hard | [[../billing-engine/_module]] | API endpoints additionally module-gated (`EnforceModuleAccess`) |

## Core Features

- Create API tokens with named scopes (abilities): `hr:read`, `hr:write`, `finance:read`, etc.
- Token shown once at creation — stored as hash, never re-revealed
- Token listing: name, scopes, last used, created by
- Revoke individual tokens or all tokens
- Rate limits enforced per token (`api` / `api-write` limiters — see [[security]] and [[../../../architecture/security]])
- API base URL: `/api/v1/` — all endpoints require `Authorization: Bearer {token}`
- Tokens belong to a dedicated service-user per company *(assumed)* — not personal user tokens, so people leaving doesn't break integrations

## Test Checklist

- [ ] Tenant isolation: token of company A cannot read company B data (context from token's user)
- [ ] Module gating: API call to inactive module's endpoint → 403
- [ ] Ability scoping: `hr:read` token cannot POST to hr endpoints
- [ ] Plain token never retrievable after creation
- [ ] Revoked token → 401 immediately
- [ ] Expired token → 401
- [ ] Per-token rate limit returns 429 + Retry-After

## Build Manifest (corrected to flat paths)

```
database/migrations/xxxx_add_created_by_to_personal_access_tokens.php
app/Data/CreateApiTokenData.php
app/Actions/{CreateApiTokenAction,RevokeApiTokenAction,RevokeAllApiTokensAction}.php
app/Http/Middleware/EnforceModuleAccess.php
app/Filament/App/Resources/ApiClientResource.php
routes/api.php (v1 group: auth:sanctum + throttle + module middleware)
tests/Feature/Core/{ApiTokenTest,ApiScopingTest}.php
```

Spec listed `app/.../Core/...`; real layout is flat (no `Core/` subdir) — corrected above.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | none | — | issues credentials; emits no domain events |
| consumes | none | — | no event listeners |

Note: this module has no event edges, but it does have read-time coupling — `EnforceModuleAccess` reads [[../billing-engine/_module]]'s active-module set (via `BillingService::hasModule`) to gate the REST edge, and its ability registry mirrors [[../rbac/_module]] permission domains. Both are read-only.

Data ownership: api-clients owns no dedicated table — it is the sole writer of Sanctum's `personal_access_tokens` (+ the added `created_by` column) in this domain; it reads the active-module set and ability registry read-only, and effects other domains via no events ([[../../../security/data-ownership]]).

## Related

- [[../../../architecture/api-design]] · [[../../../security/authn-authz]] — Sanctum guard setup
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]] · [[../../../security/data-ownership]]
- [[../webhooks/_module]]
- [[../../../glossary]]
