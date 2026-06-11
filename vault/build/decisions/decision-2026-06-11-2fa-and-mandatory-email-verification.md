---
type: adr
date: 2026-06-11
status: decided
domain: All
color: "#F97316"
---

# Self-Service 2FA + Mandatory Email Verification

## Context
Auth so far: password login per panel + Sanctum API tokens. No 2FA surface, no email-verification gate. Founder directive (2026-06-11): users must be able to enable 2FA themselves whenever they want, and nobody accesses any portal without a verified email; changing email forces re-verification.

## Options Considered
1. Laravel Fortify full install — brings its own routes/views, fights Filament panel auth
2. Custom TOTP implementation on Filament login flow — full control, small surface
3. Filament-native: `MustVerifyEmail` + `verified` panel middleware + custom 2FA challenge page

## Decision
Option 3. `User implements MustVerifyEmail`; all panels run email-verified middleware; invitation accept counts as verification; email change nulls `email_verified_at` and triggers re-verification mail to the new address. TOTP 2FA self-service in user settings (QR enable + code confirm, password+code disable, encrypted secret + single-use encrypted recovery codes), challenge step injected after password login.

## Consequences
- Every existing and future panel gets the gate for free (middleware on base panel config)
- Demo seeder must set `email_verified_at` or local logins break
- Tests: unverified-blocked, email-change-resets, 2FA challenge, recovery code consumption

## Related
- [[../../architecture/security]] — Authentication Security section updated
