---
domain: core
module: staff-console
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Staff Console — Security

Parent: [[_module]]

## Permissions

None. The admin guard has no spatie teams; access = being an `Admin`. Tenant-side permissions are untouched.

## Authorization

`canAccess()` on every artifact: `auth('admin')->check()`. The `/admin` panel is staff-only (admin guard + IP allowlist in prod); no spatie permissions on the admin guard *(assumed)*. See [[../../../security/authn-authz]].

## Rate Limiting

`ProvisionCompanyAction` sends the owner invitation email (a comms side effect) — the `CreateCompany` submit therefore names the `panel-action` limiter (30/min per admin) per the security contract ([[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]). Module activate/deactivate and suspend delegate to `BillingService`; any external (Stripe) call there carries billing-engine's own limiter. The read-only cross-company resources and dashboard widgets need no action limiter.

## Tenancy / context-leak

Admin requests run with **no CompanyContext** → `CompanyScope` no-ops → cross-company queries are intentional. Mutations that need a context set it per call via `RunsInCompanyContext` and forget it in `finally` so nothing leaks into later admin queries (covered by a test). See [[../../../security/tenancy-isolation]] and [[architecture]].

## PII

No own encrypted fields. Reads `companies` (incl. encrypted `stripe_customer_id`, owned by [[../billing-engine/_module]]) and `user_invitations`. See [[../../../security/encryption]].
