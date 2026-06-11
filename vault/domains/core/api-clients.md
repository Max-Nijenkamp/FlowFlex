---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.api
status: complete
priority: v1
depends-on: [core.rbac, core.billing]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [api]
tables: [personal_access_tokens]
permission-prefix: core.api
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# API Clients

API key management for the FlowFlex REST API. Company admins create Sanctum tokens with specific ability scopes, rotate or revoke them, and monitor last-used timestamps.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/rbac\|core.rbac]] | only admins manage tokens; abilities mirror permission domains |
| Hard | [[domains/core/billing-engine\|core.billing]] | API endpoints additionally module-gated (`EnforceModuleAccess`) |

---

## Core Features

- Create API tokens with named scopes (abilities): `hr:read`, `hr:write`, `finance:read`, etc.
- Token shown once at creation — stored as hash, never re-revealed
- Token listing: name, scopes, last used, created by
- Revoke individual tokens or all tokens
- Rate limits enforced per token (`api` / `api-write` limiters — [[architecture/security]])
- API base URL: `/api/v1/` — all endpoints require `Authorization: Bearer {token}`
- Tokens belong to a dedicated service-user per company *(assumed)* — not personal user tokens, so people leaving doesn't break integrations

---

## Data Model

Uses Sanctum's `personal_access_tokens` table — no custom tables.

| Column | Notes |
|---|---|
| name | human-readable token name |
| token | SHA-256 hash |
| abilities | JSON array of scope strings |
| last_used_at | timestamp |
| expires_at | optional expiry |
| created_by | ulid — added column *(assumed)* |

---

## DTOs

### CreateApiTokenData (input)
| Field | Type | Validation |
|---|---|---|
| name | string | required, max:100, unique per company |
| abilities | array<string> | required, each in registry (`{domain}:{read\|write}`), domain module active |
| expires_at | ?CarbonImmutable | nullable, after:now |

## Services & Actions

- `CreateApiTokenAction::run(CreateApiTokenData $data): string` — returns plain token once
- `RevokeApiTokenAction::run(string $tokenId): void` / `RevokeAllApiTokensAction::run(): void`
- Ability check middleware on API routes: `ability:{domain}:{verb}` + `module:{module-key}` (`EnforceModuleAccess`)

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ApiClientResource` | #1 CRUD resource | create modal shows token once with copy button; revoke actions; last-used column |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('core.api.view-any') && BillingService::hasModule('core.api')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`core.api.view-any` · `core.api.create` · `core.api.revoke`

---

## Test Checklist

- [ ] Tenant isolation: token of company A cannot read company B data (context from token's user)
- [ ] Module gating: API call to inactive module's endpoint → 403
- [ ] Ability scoping: `hr:read` token cannot POST to hr endpoints
- [ ] Plain token never retrievable after creation
- [ ] Revoked token → 401 immediately
- [ ] Expired token → 401
- [ ] Per-token rate limit returns 429 + Retry-After

---

## Build Manifest

```
database/migrations/xxxx_add_created_by_to_personal_access_tokens.php
app/Data/Core/CreateApiTokenData.php
app/Actions/Core/{CreateApiTokenAction,RevokeApiTokenAction,RevokeAllApiTokensAction}.php
app/Http/Middleware/EnforceModuleAccess.php
app/Filament/App/Resources/ApiClientResource.php
routes/api.php (v1 group: auth:sanctum + throttle + module middleware)
tests/Feature/Core/{ApiTokenTest,ApiScopingTest}.php
```

---

## Related

- [[architecture/api-design]]
- [[architecture/auth-rbac]] — Sanctum guard setup
- [[domains/core/webhooks]]
