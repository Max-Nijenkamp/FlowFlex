---
domain: foundation
module: filament-panels
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Filament Panels — Architecture

The Filament 5 panel shells: Admin (`/admin`, `admin` guard, `Admin` model, no CompanyScope) and App (`/app`, `web` guard, `User` model, CompanyScope active), plus a shared `App\Filament\Auth` namespace (`PanelLogin`, `EditProfile`). These are the *host* every domain's resources and pages mount into. Provider key-lines, the middleware chain, and the panel diagram live in [[_module]]; the guard-split boundary in [[security]].

## Filament Artifacts

**Nav group:** — (panel infrastructure — the container, not a nav item)

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `AppPanelProvider` / `AdminPanelProvider` | Panel shell — hosts all module artifacts (not a decision-table row) | [[../../../architecture/patterns/filament-panel-chrome]] (Switchboard+ chrome, full-height sidebar, spotlight) | `/app`: web guard, CompanyScope, sky `#38BDF8`, persistent auth middleware · `/admin`: admin guard, no scope, Indigo. Domain resources/pages register into `/app` as their domains rebuild (21-panel target) |
| `PanelLogin`, `EditProfile` (shared `App\Filament\Auth`) | Filament framework auth pages (Livewire) | — | login · password reset · email verify · 2FA (`AppAuthentication::make()->recoverable()`) · profile. Filament-owned — **not** ui-strategy row #13 (that row's Vue+Inertia login is the public-site SPA auth, not the in-panel login). Documented deviation *(assumed)* |

**Access contract (mandatory):** `foundation.panels` is always-on platform infrastructure — it is **not** module-gated by `BillingService`. Its access boundary is the **guard split** (`admin` vs `web`, non-overlapping — an `Admin` is rejected on `/app`, a tenant `User` on `/admin`) plus the persistent auth-middleware chain `Authenticate → SetCompanyContext → SetLocale → EnsureSubscriptionActive → RedirectToSetupWizard` run `isPersistent: true` ([[../../../architecture/filament-patterns]] #7, [[../../../architecture/patterns/tenant-context-pitfalls]]). Per-artifact `canAccess() = can('{permission}') && BillingService::hasModule('{module-key}')` gating applies to the domain resources/pages that mount *into* these shells, not to the shells themselves. The login/profile pages are public/authenticated Filament Livewire surfaces, not Vue+Inertia.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Profile edit (`EditProfile`: name, email, password, 2FA) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → "record changed" conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Auth-column writes (`last_login_at`, `email_verified_at`, 2FA secret) | n/a | Framework-managed, idempotent, single-actor writes on the authenticating user's own row — no concurrent-editor contention |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].
