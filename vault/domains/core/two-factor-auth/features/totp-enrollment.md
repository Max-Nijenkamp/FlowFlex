---
domain: core
module: two-factor-auth
feature: totp-enrollment
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Two-Factor Auth — TOTP Enrollment & Challenge

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

Self-service TOTP enrollment and the login-time challenge, using Filament's built-in multi-factor authentication (`->multiFactorAuthentication(AppAuthentication::make()->recoverable())`) registered on both `AppPanelProvider` (`/app`) and `AdminPanelProvider` (`/admin`). Recovery codes are issued via `->recoverable()`. The whole flow sits behind `->emailVerification()` — no enrollment or challenge until email is verified.

## UI

- **Kind**: custom-page — Filament's own multi-factor enrollment + challenge screens (framework-provided pages, not custom-built by us, but interactive full-page auth surfaces, not resource tables).
- **Page**: Filament multi-factor **enrollment** (reached from account/profile → "Set up authenticator app") and the multi-factor **challenge** page shown mid-login. Routes are Filament's built-in MFA routes on each panel (`/app`, `/admin`).
- **Layout**: enrollment — a QR code (rendered via [[qr-code-fix]]) plus a manual secret string, a 6-digit code confirmation input, and a one-time display of recovery codes. Challenge — a single 6-digit TOTP input with a "use a recovery code" fallback link.
- **Key interactions**:
  1. User opts in → QR + secret shown → user scans in authenticator app → enters the current 6-digit code to confirm → recovery codes displayed once → secret + codes persisted encrypted.
  2. On subsequent logins, after password + verified email, the challenge page requests the current TOTP code; on lost device the user submits a recovery code instead.
- **States**: empty (no code entered) · loading (verifying code) · error (invalid/expired code → inline error, retry) · selected (n/a; single input). Enrollment adds a "codes shown once" confirmation state.
- **Gating**: the enrolling/challenged user's own authenticated session; gated behind `->emailVerification()` (verified email required first). Applies to both the web-user guard (`/app`) and admin guard (`/admin`).

## Data

- Owns / writes: this module's own encrypted columns on the existing `users` and `admins` tables — `app_authentication_secret` and `app_authentication_recovery_codes` (encrypted `text`). No tables of its own; these columns are added by 2FA's two migrations ([[../data-model]]). Uses Fortify/Filament-style user columns rather than a dedicated `two_factor` table.
- Reads: nothing cross-domain. Reads the current user's own row (own module's columns).
- Cross-domain writes: none — effects other domains only via events ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events).
- Feeds: none — `fires-events: none`. A recovery-code / enrollment notice *may* flow through [[../../notifications/_module]] as a soft integration *(assumed)*, but no event contract is defined.
- Shared entity: the `users` / `admins` records themselves (identity), owned by the auth/foundation layer; 2FA only writes its own added columns on them.

> [!warning] UNVERIFIED — whether an enrollment/recovery notification is actually dispatched to [[../../notifications/_module]] is a soft-dependency assumption in [[../_module]], not confirmed against code.

## Related

- [[../_module]] · [[../architecture]] · [[../data-model]] · [[../security]] · [[qr-code-fix]]
- [[../../../../security/authn-authz]] · [[../../../../security/encryption]]
