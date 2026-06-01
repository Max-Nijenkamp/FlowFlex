---
type: module
domain: Core Platform
panel: app
module-key: core.api
status: planned
color: "#4ADE80"
---

# API Clients

API key management for the FlowFlex REST API. Company admins create Sanctum tokens with specific ability scopes, rotate or revoke them, and monitor last-used timestamps.

---

## Core Features

- Create API tokens with named scopes (abilities): `hr:read`, `hr:write`, `finance:read`, etc.
- Token shown once at creation — stored as hash, never re-revealed
- Token listing: name, scopes, last used, created by
- Revoke individual tokens or all tokens
- Rate limits enforced per token (configurable via Company Settings)
- API base URL: `/api/v1/` — all endpoints require `Authorization: Bearer {token}`

---

## Data Model

Uses Sanctum's `personal_access_tokens` table — no custom tables.

| Column | Notes |
|---|---|
| `name` | Human-readable token name |
| `token` | SHA-256 hash |
| `abilities` | JSON array of scope strings |
| `last_used_at` | Timestamp |
| `expires_at` | Optional expiry |

---

## Filament

**`/app` panel:**
- `ApiClientResource` — list, create, revoke tokens; display created token once

---

## Related

- [[architecture/auth-rbac]] — Sanctum guard setup
