---
domain: security
type: security
build-status: planned
status: unverified
color: "#EF4444"
updated: 2026-06-20
---

# Security — Map of Content

Cross-cutting security model for FlowFlex. **Planned** — the app was removed 2026-06-20, so these are
the target security controls to build, not shipped code. Per-domain authorization detail lives in each
module's `security.md`. (Facts here were verified against the codebase before it was deleted — treat as
the spec to rebuild to; see [[../decisions/decision-2026-06-20-app-project-removed]].)

## Notes

- [[authn-authz]] — two-guard auth (staff/tenant), Spatie permission teams, mandatory `canAccess()`
- [[tenancy-isolation]] — CompanyScope, CompanyContext, the null-team 403 family
- [[webhooks-signing]] — Stripe + Resend signature verification middleware
- [[data-privacy-gdpr]] — DSAR, consent log, erasure cascades
- [[encryption]] — encrypted PII columns (national id, IBAN, salary, DOB)
- [[threat-model]] — surfaces, rate limiting, headers, open items

## Source of truth

`app/Http/Middleware/*`, `app/Support/Scopes/CompanyScope.php`, `app/Support/Services/CompanyContext.php`,
the panel providers, `app/config/permission.php`, and [[../architecture/security]] /
[[../architecture/patterns/tenant-context-pitfalls]].

## Related

- [[../architecture/_moc|Architecture]] · [[../infrastructure/_moc|Infrastructure]] · [[../00-index/MOC|Vault MOC]]
