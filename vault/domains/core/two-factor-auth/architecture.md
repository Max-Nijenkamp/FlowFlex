---
domain: core
module: two-factor-auth
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Two-Factor Auth — Architecture

Parent: [[_module]]

2FA is Filament's built-in multi-factor feature, registered per panel with one custom subclass to fix QR rendering.

## Panel-provider wiring

Both providers register the same pair, email verification first:

```php
->emailVerification()          // "no portal access without verified email"
->multiFactorAuthentication(
    AppAuthentication::make()->recoverable(),
)
```

- `AppPanelProvider` (line ~49) — `/app`, web users.
- `AdminPanelProvider` (line ~43) — `/admin`, staff/admins.
- `->recoverable()` enables recovery codes alongside the TOTP factor.
- In both providers `AppAuthentication` is an **alias** for the custom subclass (`use App\Support\Filament\AppAuthenticationWithQrFix as AppAuthentication`).

## The QR-fix subclass

`app/Support/Filament/AppAuthenticationWithQrFix.php` — namespace `App\Support\Filament`, `class AppAuthenticationWithQrFix extends Filament\Auth\MultiFactor\App\AppAuthentication`. It overrides one method:

```php
protected function generateQrCodeDataUri(string $secret): string
```

**The bug it fixes:** google2fa's `getQRCodeInline()` (bacon SVG backend) already returns a complete `data:image/svg+xml;base64,…` URI. Filament's imagick-less fallback then wraps that URI in base64 a **second** time, so the browser renders an empty image. The override detects the double-wrap and unwraps once, yielding a valid data URI. Detail: [[features/qr-code-fix]].

## Enrollment / challenge flow

```mermaid
flowchart TD
    A[User logs in] --> B{email verified?}
    B -->|no| C[emailVerification gate\nblocks portal access]
    B -->|yes| D{2FA enrolled?}
    D -->|no, opts in| E[generateQrCodeDataUri\nQR-fix unwrap]
    E --> F[scan in authenticator app]
    F --> G[secret + recovery codes\nsaved encrypted]
    D -->|yes| H[TOTP challenge]
    H -->|code ok| I[session established]
    H -->|lost device| J[recovery code]
    J --> I
```

## Filament Artifacts

**Filament Artifacts:** None (backend / auth module — 2FA is Filament's **built-in** multi-factor feature registered on `AppPanelProvider` and `AdminPanelProvider` via `->multiFactorAuthentication(...)`; the enrollment and challenge screens are framework-provided auth pages, not module-owned resources or custom pages). The only module code is the `AppAuthenticationWithQrFix` subclass (a `generateQrCodeDataUri` override, see [[features/qr-code-fix]]) plus the two panel-provider registration lines.

**Access contract:** 2FA is enforced at the **panel auth layer**, not via `canAccess()`. Each provider chains `->emailVerification()` immediately before `->multiFactorAuthentication(AppAuthentication::make()->recoverable())`, so a verified email is required before enrollment/challenge on both `/app` (web-user guard) and `/admin` (admin guard). `core.2fa` is a platform auth capability (always active) — no `BillingService::hasModule()` gate, no permission string (self-service). See [[security]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| TOTP enrollment (write encrypted `app_authentication_secret` + recovery codes) | n/a | Single-owner self-service — the user writes only their own row once at enrollment; no concurrent-editor surface |
| Challenge / recovery-code redemption | n/a (delegated) | Managed by Filament's built-in multi-factor feature; recovery codes are single-use, redemption handled framework-side — not a module-owned write path |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Related

- [[_module]] · [[data-model]] · [[security]] · [[features/qr-code-fix]]
- [[../../../architecture/filament-patterns]]
