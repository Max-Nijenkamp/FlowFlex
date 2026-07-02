---
domain: core
module: two-factor-auth
type: decision
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Two-Factor Auth — Decisions

Parent: [[_module]]

## Mandatory email verification + self-service TOTP 2FA

Both portals enforce email verification before portal access, and offer self-service TOTP two-factor authentication with recovery codes on both the web-user and admin guards. The pairing (`->emailVerification()` then `->multiFactorAuthentication(...->recoverable())`) is registered identically on `AppPanelProvider` and `AdminPanelProvider`.

→ [[../../../decisions/decision-2026-06-11-2fa-and-mandatory-email-verification]]

## Custom QR-fix subclass over vanilla Filament

Rather than patch Filament, a thin `AppAuthenticationWithQrFix` subclass overrides `generateQrCodeDataUri()` to correct the double-base64-wrapped (empty) enrollment QR in imagick-less environments. See [[features/qr-code-fix]].

## Related

- [[_module]] · [[security]] · [[architecture]]
