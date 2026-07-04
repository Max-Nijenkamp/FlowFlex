---
domain: foundation
module: filament-panels
feature: panel-auth
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Panel Auth (login, 2FA, profile — shared `Filament\Auth`)

Login, password reset, email verification, 2FA, and profile editing — a shared `App\Filament\Auth` namespace both panels reuse. No Breeze/Jetstream; Filament owns auth end to end.

## Behaviour

- `PanelLogin` — custom login page; `/app` adds `->passwordReset()->emailVerification()`, `AppAuthentication` MFA (recoverable), and login throttling.
- `EditProfile` (`isSimple: false`) — full profile editor (name, email, password, 2FA).
- Guard-correct: `/app` authenticates `web`/`User`; `/admin` authenticates `admin`/`Admin`; cross-guard login is rejected (verified `PanelAuthTest`).
- Successful login establishes the tenant context on the next authenticated request ([[../../multi-tenancy-layer/features/persistent-context]]).

## UI

- **Kind**: custom-page (Filament auth pages — Livewire, not Vue). Public/unauthenticated surface, but rendered by Filament not Inertia.
- **Page**: `/app/login`, `/app/password-reset`, `/app/verify-email`, `/app/profile` (+ `/admin` equivalents).
- **Layout**: centered card on brand background; profile is a standard Filament form.
- **Key interactions**: submit credentials → (2FA challenge if enabled) → panel; reset flow via emailed link; toggle 2FA in profile.
- **States**: default · invalid credentials (error) · throttled (rate-limit message) · 2FA challenge · reset-sent.
- **Gating**: login/reset public; profile requires authentication.

## Data

- Owns: no tables. Writes: `users`/`admins` auth columns (own scaffold tables) — password, 2FA secrets, `last_login_at`, `email_verified_at`.
- Cross-domain writes: none.

## Relations

- Consumes: nothing external. Feeds: `SetCompanyContext` (context set post-auth); 2FA detail in [[../../../../domains/core/two-factor-auth/_module|two-factor-auth]].

## Test Checklist

### Unit
- [x] Login resolves the guard-correct model (`web` → User, `admin` → Admin)

### Feature (Pest)
- [x] Cross-guard login rejected — Admin on `/app`, User on `/admin` (`PanelAuthTest`)
- [x] Login throttling engages after repeated failed attempts

### Livewire
- [x] Login form validation: invalid credentials show an error; throttled shows the rate-limit message

## Unknowns

> [!warning] UNVERIFIED — login throttle values; whether `EditProfile` exposes 2FA management here or defers
> to the two-factor-auth module. See [[../unknowns]].

## Related

- [[../_module|Filament Panels]] · [[app-panel-shell]] · [[../../../../security/authn-authz]] · [[../../../../domains/core/two-factor-auth/_module]]
