---
domain: security
type: security
build-status: planned
status: unverified
color: "#EF4444"
updated: 2026-06-20
---

# Authentication & Authorization

## Two guards, two identities

| Guard | Identity | Panel | Notes |
|---|---|---|---|
| `admin` | `Admin` model | `/admin` staff console | FlowFlex employees; `super_admin` role column |
| `web` | `User` model | `/app` tenant panel | Company members; roles via Spatie (team-scoped) |

Cross-guard login bounce (a guest visit to one guard's URL hijacking the other's login via a shared
`url.intended`) is solved by `GuardScopedLoginResponse` + a prefix filter in `PublicAuthController`.

## Login model — exactly two ways in

Decided 2026-06-20 ([[../decisions/decision-2026-06-20-workspace-hub-and-login-model]]). There is **no**
other login: no per-domain logins, no public self-registration ([[../decisions/decision-2026-06-10-no-public-registration]]).

| Login | Guard | Who | Entry points | Lands on |
|---|---|---|---|---|
| **Workspace login** | `web` / `User` | Tenant company members | (a) the workspace login page, **and** (b) the public website front-end (Inertia + Vue) "Log in" → the **same** auth | **[[../domains/core/workspace-hub/_module|Workspace Hub]]** (domain selector) |
| **Admin login** | `admin` / `Admin` | Internal FlowFlex staff only | `/admin` login | Staff console (`/admin`) — **no hub** |

- Both tenant entry points resolve to the **same** `web` session; the public site's "Log in" is a funnel
  to workspace auth ([[../frontend/_index]]), not a separate mechanism.
- **Post-login (tenant only):** land on the Workspace Hub and pick a domain before entering it. Admin
  users bypass the hub and go straight to the staff console.

## Authorization — Spatie, team-scoped

- `spatie/laravel-permission` with **teams = `company_id`**. Permissions are `domain.module.action` strings.
- **Not** Laravel Policies — authorization is permission-checks. See [[../architecture/patterns/policy]].
- **Mandatory `canAccess()`** on every Filament resource/page (permission + module-active). DoD gate;
  see [[decisions/decision-2026-06-11-security-contract-hardening]].
- Owner role holds every permission; owner-only gates on settings + marketplace.

## MFA + verification

- Self-service **2FA / TOTP** (Filament native `multiFactorAuthentication`, recoverable) on both panels;
  `add_two_factor_columns_*` migrations; `AppAuthenticationWithQrFix` fixes a double-base64 QR.
- **Email verification mandatory**; email change resets verification. See
  [[decisions/decision-2026-06-11-2fa-and-mandatory-email-verification]].

> [!warning] null-team 403 trap
> Any Spatie `can()/hasRole()` evaluated **before** CompanyContext sets the team id reads an empty
> permission cache → phantom 403. Always set context first. Full detail: [[tenancy-isolation]].

## Related

- [[tenancy-isolation]] · [[webhooks-signing]] · [[../architecture/security]] · [[../domains/core/rbac/_module]] · [[_moc|Security MOC]]
