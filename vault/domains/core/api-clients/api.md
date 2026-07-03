---
domain: core
module: api-clients
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
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
| expires_at | ?CarbonImmutable | nullable, after:now; **defaults to now + 90 days** when omitted *(assumed — per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]])* |

Ability strings follow `{domain}:{verb}` where verb is `read` or `write`, and the domain's module must be active for the ability to be assignable. The created token is **bound to the issuing user's `company_id`** at creation (per the token-hardening ADR).

## Action surface

- `CreateApiTokenAction::run(CreateApiTokenData $data): string` — returns the plain token **once**; binds it to the issuing user's `company_id` and applies the 90-day default expiry
- `RotateApiTokenAction::run(string $tokenId): string` — issues a replacement with identical abilities + company binding, revokes the original after a 7-day grace overlap; returns the new plain token **once** (per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]])
- `RevokeApiTokenAction::run(string $tokenId): void`
- `RevokeAllApiTokensAction::run(): void`

## REST edge

- Base URL: `/api/v1/`, every endpoint requires `Authorization: Bearer {token}`
- Enforced by `ability:{domain}:{verb}` + `module:{module-key}` (`EnforceModuleAccess`) — see [[architecture]] and [[../../../architecture/api-design]].
- **Rotation endpoint:** `POST /api/v1/auth/tokens/{id}/rotate` (gated by `core.api.rotate`) issues a replacement token and revokes the original with a 7-day grace overlap for zero-downtime rotation — per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].
- **Per-company quota:** the `api-company` limiter (1000 req/min per `company_id` *(assumed)*) layers on top of per-token `api` / `api-write` limits; quota state in `X-RateLimit-Company-*` headers — see [[security]].
