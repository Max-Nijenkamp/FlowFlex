---
domain: core
module: api-clients
feature: token-scopes
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Token Scopes (Abilities)

Parent: [[../_module]] · See [[../security]] · [[../architecture]]

Each token carries named abilities of the form `{domain}:{verb}` where verb is `read` or `write` (e.g. `hr:read`, `finance:write`).

- Abilities are validated at creation against the ability registry; only domains whose module is active are assignable.
- API routes enforce the ability with the `ability:{domain}:{verb}` middleware — a `hr:read` token is rejected on a hr write endpoint.
- Ability scopes mirror the RBAC permission domains ([[../rbac/_module]]).

## UI

- **Kind**: simple-resource
- **Page**: the ability selector is part of the create form on `ApiClientResource` at `/app/api-clients`; enforcement itself is `ability:{domain}:{verb}` middleware on the `/api/v1/` REST edge (background).
- **Layout**: an ability multi-select in the token-create form, grouped by domain with `read`/`write` variants; only abilities for active modules are offered.
- **Key interactions**: admin picks the abilities a token should carry at creation; at request time the middleware allows/denies per ability.
- **States**: empty = no abilities selectable if no modules active (unlikely) · loading = form skeleton · error = an ability for an inactive module rejected at validation; a `hr:read` token POSTing to a hr endpoint → 403 at the edge · selected = chosen abilities shown as chips.
- **Gating**: `core.api.create` to set abilities; REST enforcement via `ability:{domain}:{verb}` + `EnforceModuleAccess`.

## Data

- Owns / writes: the `abilities` json on Sanctum `personal_access_tokens` (set at create; immutable after). No dedicated table.
- Reads: the ability registry and the company's active-module set (via `BillingService::hasModule`, read-only) to constrain assignable abilities.
- Cross-domain writes: none — see [[../../../../security/data-ownership]].

## Relations

- Consumes: none.
- Feeds: the ability check gates `/api/v1/` calls into every domain's REST endpoints (read/write per verb); no event emitted.
- Shared entity: the ability registry mirrors [[../rbac/_module]] permission domains; the active-module set is owned by [[../billing-engine/_module]] (read-only).
