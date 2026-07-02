---
domain: core
module: staff-console
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Staff Console — Security

Parent: [[_module]]

## Permissions

None. The admin guard has no spatie teams; access = being an `Admin`. Tenant-side permissions are untouched.

## Authorization

`canAccess()` on every artifact: `auth('admin')->check()`. The `/admin` panel is staff-only (admin guard + IP allowlist in prod); no spatie permissions on the admin guard *(assumed)*. See [[../../../security/authn-authz]].

## Tenancy / context-leak

Admin requests run with **no CompanyContext** → `CompanyScope` no-ops → cross-company queries are intentional. Mutations that need a context set it per call via `RunsInCompanyContext` and forget it in `finally` so nothing leaks into later admin queries (covered by a test). See [[../../../security/tenancy-isolation]] and [[architecture]].

## PII

No own encrypted fields. Reads `companies` (incl. encrypted `stripe_customer_id`, owned by [[../billing-engine/_module]]) and `user_invitations`. See [[../../../security/encryption]].
