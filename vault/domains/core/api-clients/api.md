---
domain: core
module: api-clients
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# API Clients — API (DTO + Action Surface)

Parent: [[_module]] · See also [[architecture]]

This module fires no events and consumes none. Its cross-cutting surface is the token-creation DTO and the REST edge it protects.

## DTOs

### CreateApiTokenData (input)

| Field | Type | Validation |
|---|---|---|
| name | string | required, max:100, unique per company |
| abilities | array\<string\> | required, each in registry (`{domain}:{read\|write}`), domain module active |
| expires_at | ?CarbonImmutable | nullable, after:now |

Ability strings follow `{domain}:{verb}` where verb is `read` or `write`, and the domain's module must be active for the ability to be assignable.

## Action surface

- `CreateApiTokenAction::run(CreateApiTokenData $data): string` — returns the plain token **once**
- `RevokeApiTokenAction::run(string $tokenId): void`
- `RevokeAllApiTokensAction::run(): void`

## REST edge

- Base URL: `/api/v1/`, every endpoint requires `Authorization: Bearer {token}`
- Enforced by `ability:{domain}:{verb}` + `module:{module-key}` (`EnforceModuleAccess`) — see [[architecture]] and [[../../../architecture/api-design]].
