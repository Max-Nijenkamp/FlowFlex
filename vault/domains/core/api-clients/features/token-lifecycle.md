---
domain: core
module: api-clients
feature: token-lifecycle
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Token Lifecycle (Create-once / Revoke)

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

- **Create:** `CreateApiTokenAction` mints the token and returns the plain value **once**, shown in a copy-once modal. Only the SHA-256 hash is persisted.
- **List:** name, scopes, `last_used_at`, and creator are shown in `ApiClientResource`; the secret is never re-revealed.
- **Expire:** optional `expires_at`; an expired token authenticates as 401.
- **Revoke:** `RevokeApiTokenAction` (single) or `RevokeAllApiTokensAction` (all) — a revoked token returns 401 immediately.

## UI

- **Kind**: simple-resource
- **Page**: `ApiClientResource` at `/app/api-clients` (list + create).
- **Layout**: list table — name, scopes (abilities), `last_used_at`, created-by. Create form: name, ability multi-select, optional `expires_at`. On create, a copy-once modal reveals the plain token; row actions: revoke (single), revoke-all (bulk/header).
- **Key interactions**: admin creates a token → copies it once from the modal → uses it as a Bearer token; revokes a compromised token or all tokens.
- **States**: empty = "No API tokens yet" with a create CTA · loading = table/form skeleton · error = duplicate-name validation, or expiry set in the past · selected = the copy-once modal after creation (token never re-shown).
- **Gating**: `core.api.view-any` (list), `core.api.create` (create), `core.api.revoke` (revoke) — plus `BillingService::hasModule('core.api')`.

## Data

- Owns / writes: Sanctum `personal_access_tokens` (create hash, `last_used_at`, delete on revoke) + the added `created_by` column *(assumed)*. This module owns no dedicated table.
- Reads: only the token rows scoped to the company's service user; ability registry to validate abilities.
- Cross-domain writes: none — see [[../../../../security/data-ownership]].

## Relations

- Consumes: none.
- Feeds: none (issues credentials; fires no events).
- Shared entity: Sanctum `personal_access_tokens` (framework table, this module is its sole writer here); company scope derives from the token's owning per-company service user *(assumed)*.

## Test Checklist

### Unit
- [ ] Created token is persisted only as a SHA-256 hash; the returned plain value never appears in the row
- [ ] `expires_at` defaults to now + 90 days when not supplied *(assumed — per [[../../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]])*

### Feature (Pest)
- [ ] `CreateApiTokenAction` returns the plain token once, binds it to the issuing user's `company_id`, and stores only the hash
- [ ] `RevokeApiTokenAction` → subsequent request with that token returns 401 immediately; `RevokeAllApiTokensAction` clears every token for the company
- [ ] Expired token (`expires_at` in the past) authenticates as 401
- [ ] `RotateApiTokenAction` issues a replacement with identical abilities + company binding and revokes the original after the grace window (concurrent double-rotate rejected via row lock)

### Livewire
- [ ] Create action reveals the copy-once modal exactly once; the secret is never re-shown on the list
- [ ] Duplicate token name (per company) and past `expires_at` fail form validation; create/rotate/revoke denied without the matching `core.api.*` permission
