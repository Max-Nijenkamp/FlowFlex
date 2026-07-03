---
domain: core
module: two-factor-auth
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Two-Factor Auth — Security

Parent: [[_module]]

2FA is a security control in its own right; this note records its own hardening properties.

## Permissions

None — 2FA is self-service and defines no permission strings. There is no command action or state transition to gate; authorization is the user's own authenticated session behind the `->emailVerification()` gate. `core.2fa` is a platform auth capability (always active), so there is no module-gating verb either.

## Rate limiting

The TOTP challenge and recovery-code redemption are login-security surfaces and are throttled by the framework login/auth throttle (failed-attempt limiting), not a panel-action limiter — there is no comms/money/file/external-API side effect on this module's own paths.

## TOTP, self-service

Users and admins enrol themselves in TOTP (authenticator app) — no admin provisioning step. Registered via Filament's `->multiFactorAuthentication(...)` on both panels ([[architecture]]).

## Recovery codes

`->recoverable()` issues recovery codes at enrollment. A lost authenticator device is recoverable via a one-time code rather than an admin reset, avoiding a social-engineering reset path.

## Encrypted secret storage

`app_authentication_secret` and `app_authentication_recovery_codes` are stored as encrypted `text` columns on `users` and `admins` — the TOTP secret is never at rest in plaintext. See [[data-model]] and [[../../../security/encryption]].

## Email-verification gate

Each panel calls `->emailVerification()` immediately before `->multiFactorAuthentication(...)` — "no portal access without verified email." Email ownership is proven before any 2FA enrollment or challenge. See [[../../../security/authn-authz]].

## Both guards covered

The control applies to **both** the web-user guard (`/app`, `AppPanelProvider`) and the admin/staff guard (`/admin`, `AdminPanelProvider`) — staff console access is 2FA-eligible on the same footing as tenant users.

## Related

- [[_module]] · [[architecture]] · [[data-model]] · [[decisions]]
- [[../../../security/authn-authz]] · [[../../../security/encryption]] · [[../../../architecture/filament-patterns]]
