---
type: adr
date: 2026-07-02
status: decided
domain: All
color: "#F97316"
---

# Rate-Limit & Token Hardening — Livewire Action Throttles, Per-Company Quotas, Sanctum Rotation + Company Binding

## Context

The 2026-07-02 audit found the rate-limiting and token story solid but with three systemic holes:

1. **Livewire/Filament actions have no throttle convention.** [[architecture/security]] says "custom Filament forms with sensitive actions apply the `api-write` limiter" as prose — no mechanism, no example, nothing a spec can cite. Buttons that send email, fire webhooks, generate PDFs, or mutate money are effectively unthrottled.
2. **Per-company quotas exist only for exports.** API read/write limits are per-token/per-user — one tenant's script with many tokens can starve others (tenant-fairness gap).
3. **Sanctum tokens never rotate and are only implicitly company-bound.** No expiry default, no rotation policy, no documented behavior for tokens when a user's company relationship changes; abilities are domain-coarse (`hr:write`).

## Options Considered

1. **Codify all three in [[architecture/security]] + [[architecture/api-design]] and enforce via spec security contract.** Chosen.
2. **Per-permission token abilities** (mirror the full Spatie set in token scopes). Rejected for now — explosion of scopes; domain-level abilities + server-side permission checks already double-gate every call.
3. **Leave per-company quotas to infrastructure (API gateway).** Rejected — self-hosted Redis limiters already exist; one more named limiter is cheap and testable.

## Decision

1. **Livewire action throttle convention**: any Filament/Livewire action that (a) sends outbound comms, (b) mutates money/inventory, (c) generates files/PDFs, or (d) calls external APIs **must** name a Redis limiter — default `RateLimiter::for('panel-action')`: **30/min per user** with per-action override syntax documented in [[architecture/security]]. Specs cite the limiter in `security.md` exactly like exports/webhooks (extends [[decisions/decision-2026-06-11-security-contract-hardening]] rule 5).
2. **Per-company API quota**: new named limiter `api-company` — **1000 req/min per `company_id`** across all tokens of that tenant *(assumed — tune with real traffic)*, layered on top of the existing per-token limits. 429 + `Retry-After`, quota state in `X-RateLimit-Company-*` headers.
3. **Sanctum token expiry + rotation**: new tokens default to **90-day expiry** *(assumed)*; `POST /api/v1/auth/tokens/{id}/rotate` issues a replacement with the same abilities and revokes the original (7-day grace overlap for zero-downtime rotation). Expiry-warning notification 14 days out via core.notifications.
4. **Explicit company binding**: a personal access token is bound to the issuing user's `company_id` at creation; middleware sets the permission team context from the **token's** company, not the user's current one, and tokens are revoked on company detach/offboarding. Documented in [[architecture/security]] + [[architecture/multi-tenancy]] cross-link.
5. Abilities stay domain-coarse (`hr:read`/`hr:write`/`*`) — every request still passes the full Spatie permission check server-side.

## Consequences

- [[architecture/security]]: new "Panel Action Throttling" subsection, `api-company` limiter, expanded Sanctum section (expiry, rotation, company binding, revocation triggers).
- [[architecture/api-design]]: rotation endpoint, expiry in token responses, `X-RateLimit-Company-*` headers.
- `core.api` module spec inherits the rotation endpoint + expiry columns; propagation waves add throttle citations to module `security.md` files where actions match categories (a)–(d).
- Tests: limiter isolation recipe in [[architecture/patterns/testing-pattern]] already covers named limiters; new cases for rotation grace + token-company binding.

## Related

- [[architecture/security]] · [[architecture/api-design]] · [[architecture/multi-tenancy]]
- [[decisions/decision-2026-06-11-security-contract-hardening]]
- [[architecture/patterns/testing-pattern]]
