---
tags: [flowflex, domain/it, sso, identity, saml, oidc, phase/4]
domain: IT & Security
panel: it
color: "#4F46E5"
status: planned
last_updated: 2026-05-08
---

# SSO & Identity Provider

One login for everything — connect FlowFlex to your company's identity provider (Azure AD, Okta, Google Workspace) or use FlowFlex AS your identity provider. Single Sign-On via SAML 2.0 and OIDC. Employees don't need another password.

**Who uses it:** IT admins, system administrators
**Filament Panel:** `it`
**Depends on:** Core, [[Roles & Permissions (RBAC)]]
**Phase:** 4

---

## Features

### SSO as Service Provider (SP)

- FlowFlex connects TO your existing IdP
- Protocols: SAML 2.0, OpenID Connect (OIDC), OAuth 2.0
- Supported IdPs: Azure Active Directory, Okta, Google Workspace, Auth0, OneLogin, Ping Identity, ADFS, any SAML-compliant IdP
- SP-initiated and IdP-initiated login flows
- Just-in-time (JIT) provisioning: user auto-created in FlowFlex on first SSO login
- Attribute mapping: map IdP attributes (department, role, manager) → FlowFlex fields

### SCIM Provisioning

- SCIM 2.0 API for automatic user sync
- Auto-create: new IdP user → FlowFlex tenant created
- Auto-update: IdP role/department change → FlowFlex profile updated
- Auto-deprovision: user deactivated in IdP → FlowFlex access revoked within minutes
- Group sync: IdP groups mapped to FlowFlex roles/teams
- Prevents orphaned accounts (ex-employees keeping access)

### FlowFlex as Identity Provider (IdP)

- Use FlowFlex login as the auth source for other apps
- OIDC / OAuth 2.0 provider
- Client app registration: add any app that supports OIDC
- Token scopes: profile, email, roles, custom claims
- Use case: internal tools (Grafana, internal dashboards, custom apps) authenticate via FlowFlex

### Session Management

- Configurable session lifetime (e.g. expire after 8h of inactivity)
- Enforce re-auth for sensitive actions (delete, export, permission change)
- Concurrent session limit per user (e.g. max 3 active sessions)
- Active session viewer per user: device, IP, last active
- Admin: force logout all sessions for a user (offboarding)

### Multi-Factor Authentication (MFA)

- TOTP (Google Authenticator, Authy) — admin can enforce company-wide
- SMS OTP (via Twilio)
- Hardware keys: WebAuthn / FIDO2 (YubiKey, Touch ID, Windows Hello)
- Backup codes: generate 8 single-use codes
- MFA bypass for SSO: if IdP enforces MFA, don't double-prompt
- Trusted devices: "Don't ask on this device for 30 days"

### Directory Sync

- Real-time sync: org chart, department hierarchy, manager relationships pulled from Azure AD / Google
- Displayed in FlowFlex Org Chart module
- Employee offboarding: one action in IdP → all FlowFlex access revoked

### Audit & Compliance

- Login audit log: every login attempt, method, IP, success/failure, timestamp
- MFA events: enrolments, bypass attempts
- SCIM provisioning log: create/update/deprovision events
- Suspicious login alerts: new country, new device, impossible travel detection
- SOC 2 evidence exports

---

## Database Tables (3)

### `sso_configurations`
| Column | Type | Notes |
|---|---|---|
| `provider_type` | enum | `saml`, `oidc`, `google`, `azure`, `okta` |
| `entity_id` | string nullable | SAML IdP entity ID |
| `sso_url` | string nullable | SAML SSO endpoint |
| `certificate` | text nullable | IdP signing cert |
| `client_id` | string nullable | OIDC |
| `client_secret_hash` | string nullable | OIDC (hashed) |
| `attribute_map` | json | IdP attr → FlowFlex field |
| `jit_enabled` | boolean default true | |
| `scim_enabled` | boolean default false | |
| `scim_token_hash` | string nullable | |
| `enforce_mfa` | boolean default false | |

### `sso_oidc_clients`
| Column | Type | Notes |
|---|---|---|
| `name` | string | app name |
| `client_id` | string unique | |
| `client_secret_hash` | string | |
| `redirect_uris` | json | string[] |
| `allowed_scopes` | json | string[] |
| `active` | boolean | |

### `it_login_events`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK nullable | |
| `email` | string | |
| `method` | enum | `password`, `sso`, `magic_link` |
| `mfa_method` | enum nullable | `totp`, `sms`, `webauthn` |
| `ip_address` | string | |
| `user_agent` | string | |
| `country_code` | string nullable | |
| `success` | boolean | |
| `failure_reason` | string nullable | |
| `occurred_at` | timestamp | |

---

## Permissions

```
it.sso.view
it.sso.configure
it.sso.manage-clients
it.sso.view-audit-log
it.sso.manage-mfa-policy
```

---

## Competitor Comparison

| Feature | FlowFlex | Okta | Azure AD | JumpCloud |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€2+/user/mo) | ❌ | ❌ |
| SAML + OIDC support | ✅ | ✅ | ✅ | ✅ |
| SCIM auto-provisioning | ✅ | ✅ | ✅ | ✅ |
| FlowFlex as IdP | ✅ | ✅ | ✅ | ✅ |
| WebAuthn/FIDO2 MFA | ✅ | ✅ | ✅ | ✅ |
| Impossible travel detection | ✅ | ✅ | ✅ | partial |

---

## Related

- [[IT Overview]]
- [[Roles & Permissions (RBAC)]]
- [[Access & Permissions Audit]]
- [[Security & Compliance]]
