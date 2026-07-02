---
domain: core
module: two-factor-auth
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Two-Factor Authentication

Self-service TOTP two-factor authentication with recovery codes, wired into Filament's multi-factor auth on both the `/app` (web users) and `/admin` (staff) panels. Paired with mandatory email verification — no portal access without a verified email. Encrypted secret + recovery-code storage. Built platform capability (no flat spec existed; reconstructed from code).

- **module-key:** `core.2fa` *(assumed)* · **panel:** app + admin · **priority:** v1-core *(assumed)*
- **fires-events:** none · **consumes-events:** none

## What it does

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
