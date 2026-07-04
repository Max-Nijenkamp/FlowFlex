---
domain: core
module: two-factor-auth
type: module
build-status: complete
status: wip
color: "#4ADE80"
updated: 2026-07-04
---

# Two-Factor Authentication

Self-service TOTP two-factor authentication with recovery codes, wired into Filament's multi-factor auth on both the `/app` (web users) and `/admin` (staff) panels. Paired with mandatory email verification — no portal access without a verified email. Encrypted secret + recovery-code storage. Built platform capability (no flat spec existed; reconstructed from code).

## Module-key

`core.2fa` *(assumed)*

**Priority:** v1-core *(assumed)*  
**Panel:** app + admin (panel auth layer, not a nav surface)  
**Permission prefix:** none (self-service — enforced at the panel auth layer, no permission strings)  
**Tables:** none of its own — encrypted `app_authentication_secret` + `app_authentication_recovery_codes` columns added to `users` and `admins`  
**Events:** fires none · consumes none

## Core Features

- `->multiFactorAuthentication(AppAuthentication::make()->recoverable())` on both `AppPanelProvider` and `AdminPanelProvider`.
- Each provider calls `->emailVerification()` immediately before it — 2FA sits behind a verified-email gate.
- Uses a custom `AppAuthenticationWithQrFix` subclass (aliased `as AppAuthentication`) that fixes an empty enrollment QR code — see [[architecture]].
- TOTP secret + recovery codes persisted as encrypted `text` columns on both `users` and `admins` — see [[data-model]].

## Sibling notes

- [[architecture]] — QR-fix subclass, panel-provider wiring, enrollment/challenge flow
- [[data-model]] — the two migrations (users + admins columns)
- [[security]] — TOTP self-service, recovery codes, encrypted storage, email-verification gate
- [[decisions]] — mandatory email verification + self-service TOTP ADR
- [[unknowns]] — UNVERIFIED `two_factor_enabled` column; module-key/priority assumptions
- [[features/totp-enrollment]] — self-service TOTP enrollment + login challenge + recovery codes
- [[features/qr-code-fix]] — the double-base64 QR unwrap

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | foundation.panels | `/app` + `/admin` auth surfaces |
| Soft | [[../notifications/_module]] | recovery-code / enrollment notices *(assumed)* |

## Build Manifest (flat paths)

```
app/Support/Filament/AppAuthenticationWithQrFix.php
app/Providers/Filament/AppPanelProvider.php    (registers ->multiFactorAuthentication(...))
app/Providers/Filament/AdminPanelProvider.php  (registers ->multiFactorAuthentication(...))
database/migrations/2026_06_11_180000_add_two_factor_columns_to_users_table.php
database/migrations/2026_06_11_220000_add_two_factor_columns_to_admins_table.php
```

> [!warning] UNVERIFIED — needs confirmation: exact `AppPanelProvider` / `AdminPanelProvider` paths under `app/Providers/Filament/` (registration lines confirmed at ~49 / ~43; enclosing file path assumed to be the conventional Filament location).

## Test Checklist

- [ ] Tenant isolation: a user reads/writes only their own `app_authentication_secret` / recovery codes (own row) — never another user's or another company's
- [ ] Module gating: n/a (platform auth capability, always active — enforced at the panel auth layer)
- [ ] Email-verification gate blocks enrollment/challenge until the email is verified (both `/app` and `/admin`)
- [ ] Enrollment persists the TOTP secret + recovery codes as encrypted `text` (never plaintext at rest)
- [ ] Enrollment QR renders a scannable single-encoded data URI (QR-fix unwrap) in imagick-less environments
- [ ] A valid recovery code establishes a session when the authenticator device is lost; a spent code is rejected

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | none | — | 2FA fires no domain events |
| consumes | none | — | 2FA consumes no domain events |

Data ownership: two-factor-auth owns and writes only its two encrypted columns (`app_authentication_secret`, `app_authentication_recovery_codes`) on the existing `users` and `admins` tables — no tables of its own (Fortify/Filament-style user columns). It reads only the current user's own row, and effects other domains only via events (there are none) ([[../../../security/data-ownership]]).

## Related

- [[../../../decisions/decision-2026-06-11-2fa-and-mandatory-email-verification]]
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../security/authn-authz]] · [[../../../security/encryption]] · [[../../../security/data-ownership]] · [[../../../architecture/filament-patterns]]
- [[../../../glossary]]
