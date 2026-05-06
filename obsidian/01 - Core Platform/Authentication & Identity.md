---
tags: [flowflex, core, authentication, identity, phase/1]
domain: Core Platform
panel: admin
color: "#2199C8"
status: built
last_updated: 2026-05-06
---

# Authentication & Identity

Every user across every tenant authenticates through this module. It is the single identity layer for the entire platform — one login, every panel.

**Who uses it:** All users, admins, FlowFlex staff
**Filament Panel:** `admin`, `workspace`
**Depends on:** Nothing (foundational)
**Build complexity:** Medium — 3 resources, 2 pages, 4 tables

## Events Fired

- `UserLoggedIn` — triggers audit log entry
- `UserLoggedOut` — ends session record
- `UserPasswordChanged` — security notification dispatched
- `TwoFactorEnabled` — security confirmation notification
- `SuspiciousLoginDetected` — alert to workspace admin

## Features

### Login Methods

| Method | Description |
|---|---|
| Email + password | Standard credential login with bcrypt hashing |
| Google OAuth 2.0 | One-click login via Google account |
| Microsoft OAuth | Login via Microsoft / Azure AD account |
| GitHub OAuth | Login via GitHub account (useful for developer-heavy teams) |
| SAML SSO | Enterprise single sign-on (Okta, Azure AD, Auth0) |
| Magic link | Passwordless email link login (expires in 15 minutes) |
| Passkey / WebAuthn | Biometric and hardware key authentication |

### Two-Factor Authentication (2FA)

- TOTP authenticator app (Google Authenticator, Authy, 1Password)
- SMS OTP via Twilio (fallback option)
- Backup recovery codes (10 single-use codes generated on 2FA setup)
- Workspace admin can enforce 2FA as mandatory for all users
- Grace period setting (require 2FA within N days of account creation)

### Session Management

- Active session list per user (device, IP, location, last active)
- Revoke individual sessions remotely
- Revoke all other sessions ("sign out everywhere")
- Session timeout configuration per workspace (15min / 1hr / 8hr / never)
- Device fingerprinting for suspicious login detection
- Concurrent session limits (enterprise tier)

### Admin Impersonation

- FlowFlex super-admins can impersonate any tenant user for support purposes
- Workspace admins can impersonate any user within their tenant
- All impersonation sessions are logged with reason field
- A visible banner appears when impersonating ("You are viewing as Jane Smith")
- Impersonated sessions cannot change passwords or billing settings

## Database Tables (4)

1. `users` — core user record (shared across tenants via central database)
2. `sessions` — active user sessions with device/IP metadata
3. `two_factor_authentications` — TOTP secrets, backup codes
4. `sso_connections` — SAML provider configs per tenant

## Related

- [[Roles & Permissions (RBAC)]]
- [[Multi-Tenancy & Workspace]]
- [[Security Rules]]
- [[Admin Panel]]
- [[Workspace Panel]]
