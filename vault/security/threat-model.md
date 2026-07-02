---
domain: security
type: security
build-status: planned
status: unverified
color: "#EF4444"
updated: 2026-06-20
---

# Threat Model & Hardening

## Public / unauthenticated surfaces

- Marketing site (Vue/Inertia), public auth (login/forgot/reset — throttled), invite registration
  (`/register/invite/{token}`), webhooks ([[webhooks-signing]]), health endpoint (throttled, no detail leak).
- **No public self-registration** — companies are staff-created/invite-only
  ([[decisions/decision-2026-06-10-no-public-registration]]).
- Public quote-accept + careers surfaces were **removed** with the CRM/HR strip; they return when rebuilt.

## Controls

| Control | Status |
|---|---|
| Rate limiting (login, webhooks, API, health) | built (`throttle:*` middleware) |
| Mandatory `canAccess()` on panels | built (DoD gate) |
| Tenant isolation (CompanyScope) | built — [[tenancy-isolation]] |
| Webhook signature verification | built — [[webhooks-signing]] |
| Encrypted PII | convention, none active (shell) — [[encryption]] |
| Rich-text XSS purification (`ezyang/htmlpurifier`) | installed |
| Security headers / CORS / CSRF | per [[../architecture/security]] |

## Open / unverified

> [!warning] UNVERIFIED — needs confirmation
> - Production security headers, TLS, WAF, secrets store — not provisioned ([[../infrastructure/deployment]]).
> - A formal pen-test / threat review beyond the spec-level [[../build/security-audit-2026-06-11]] hasn't run.

## Related

- [[authn-authz]] · [[tenancy-isolation]] · [[../architecture/security]] · [[_moc|Security MOC]]
