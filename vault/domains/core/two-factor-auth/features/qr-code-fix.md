---
domain: core
module: two-factor-auth
feature: qr-code-fix
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Two-Factor Auth — QR Code Fix

Parent: [[../_module]]

`app/Support/Filament/AppAuthenticationWithQrFix.php` corrects an empty enrollment QR code in imagick-less environments.

## The bug

`AppAuthenticationWithQrFix extends Filament\Auth\MultiFactor\App\AppAuthentication` (namespace `App\Support\Filament`) and overrides:

```php
protected function generateQrCodeDataUri(string $secret): string
```

google2fa's `getQRCodeInline()` uses the bacon **SVG** backend and already returns a **complete** `data:image/svg+xml;base64,…` URI. Filament's imagick-less fallback path then base64-encodes that URI **a second time**, so the `<img src>` is a double-wrapped, unrenderable blob — the browser shows an empty image and users cannot scan to enrol.

## The fix

The override detects the already-complete `data:` URI (double-wrap) and unwraps one base64 layer, returning the valid single-encoded data URI. In both panel providers the subclass is aliased `as AppAuthentication`, so the fix applies wherever multi-factor auth is registered ([[../architecture]]).

## UI

- **Kind**: background — this feature is a rendering fix inside Filament's built-in enrollment page (no page of its own). The visible surface it corrects is the multi-factor **enrollment QR** shown by Filament's App-authentication flow.
- **Page**: no page of its own — patches the QR `<img>` on Filament's built-in multi-factor **enrollment** screen (reached from account/profile → set up authenticator app).
- **Layout**: a single `<img>` data-URI inside Filament's enrollment modal/panel; the fix ensures it renders a scannable SVG QR instead of an empty blob.
- **Key interactions**: user opts into 2FA → Filament calls `generateQrCodeDataUri($secret)` (overridden here) → valid single-encoded `data:image/svg+xml;base64,…` returned → QR renders → user scans in authenticator app.
- **States**: empty · loading · error · selected — *before the fix* the QR is permanently the **empty/error** state (unrenderable double-wrapped blob); *after the fix* it renders correctly. No loading/selected states (static image).
- **Gating**: rides the enrolling user's own session — no separate permission. Applies on both `/app` (web-user guard) and `/admin` (admin guard) wherever `->multiFactorAuthentication(...)` is registered.

## Data

- Owns / writes: no tables of its own. This is a pure view/rendering override — it does not persist anything. (The enrollment flow it corrects writes the encrypted `app_authentication_secret` / `app_authentication_recovery_codes` columns on `users`/`admins`, owned by this same module — see [[../data-model]].)
- Reads: nothing cross-domain. Consumes only the in-memory `$secret` passed by Filament for the current enrolling user.
- Cross-domain writes: none — effects other domains only via events ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: none.
- Shared entity: none — the fix is self-contained inside `AppAuthenticationWithQrFix` and touches no domain data.

## Related

- [[../_module]] · [[../architecture]] · [[../decisions]]
