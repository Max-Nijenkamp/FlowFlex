---
domain: core
module: api-clients
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# API Clients — Architecture

Parent: [[_module]] · See also [[api]] · [[data-model]] · [[security]]

## Components

Token management is action-based (no multi-method service): stateless single-step operations over Sanctum's token store.

| Action | Signature | Behavior |
|---|---|---|
| `CreateApiTokenAction` | `run(CreateApiTokenData $data): string` | creates a Sanctum token with the requested abilities; **returns the plain token once** |
| `RevokeApiTokenAction` | `run(string $tokenId): void` | deletes a single token row |
| `RevokeAllApiTokensAction` | `run(): void` | revokes all tokens for the company's service user |

## Request-time middleware

API routes in `routes/api.php` (v1 group) stack:

| Layer | Purpose |
|---|---|
| `auth:sanctum` | resolves the bearer token to its user + abilities |
| `ability:{domain}:{verb}` | Sanctum ability check — e.g. `ability:hr:read` |
| `module:{module-key}` via `EnforceModuleAccess` | rejects calls to a module the company hasn't activated (`BillingService::hasModule`) → 403 |
| `throttle:api` / `throttle:api-write` | per-token rate limits → 429 + Retry-After |

`EnforceModuleAccess` is the API analogue of the `canAccess()` module gate used by Filament resources.

## Flow

```mermaid
flowchart TD
    Admin[Admin @ ApiClientResource] -->|CreateApiTokenData| Create[CreateApiTokenAction]
    Create --> Store[(personal_access_tokens hash)]
    Create -->|plain token, once| Copy[Copy-once modal]
    Client[API client] -->|Bearer token| Auth[auth:sanctum]
    Auth --> Ability[ability:domain:verb]
    Ability --> Mod[EnforceModuleAccess module:key]
    Mod -->|active| Throttle[throttle:api]
    Mod -->|inactive| F403[403]
    Throttle --> Route[/api/v1 endpoint/]
    Admin -->|revoke| Revoke[RevokeApiTokenAction] --> Store
```
