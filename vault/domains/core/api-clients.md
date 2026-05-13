---
type: module
domain: Core Platform
panel: app
module-key: core.api
status: planned
color: "#4ADE80"
---

# API Clients

> API key management for external integrations — create named API clients, issue Sanctum tokens, restrict by scope, and revoke at any time.

**Panel:** `app`
**Module key:** `core.api`

## What It Does

API Clients gives company owners a self-service interface to create named API credentials for connecting third-party systems to FlowFlex. Each API client has a unique client ID and a hashed secret shown in plaintext exactly once at creation. Clients can be scoped to specific read/write permissions (e.g. `hr:read`, `crm:write`) so third-party systems only access what they need. All API requests are company-scoped — a token can only access data belonging to its own company. The module also covers the REST API versioning convention and rate limiting configuration.

## Features

### Core
- Company creates named API clients from `ApiClientResource` in the app panel
- Client secret shown in plaintext once at creation — stored hashed (SHA-256), unrecoverable; regeneration revokes all existing tokens
- `ApiClientResource`: list, create, edit, revoke, regenerate secret actions
- REST API at `/api/v1/` protected by `auth:sanctum`; all responses company-scoped
- Rate limiting: 60 requests per minute per API client via Laravel `RateLimiter` keyed by `client_id`

### Advanced
- `scopes` JSON field on `api_clients` restricts access (e.g. `["hr:read", "crm:write"]`) — validated on every authenticated API request
- `ApiToken` model: short-lived tokens issued via `POST /api/v1/auth/token`; stores hashed token, `last_used_at`, `expires_at`
- Expired tokens rejected; `last_used_at` updated on every successful request
- `is_active` boolean: clients can be individually soft-disabled without deletion
- Per-client audit log: all API requests tagged with `api_client_id` in the audit log for traceability

### AI-Powered
- Anomaly detection on API usage: sudden traffic spikes, calls outside normal hours, access to unusual resource types — flagged and reported to company owner
- Scope recommendations: after 30 days of usage, suggest tightening scopes to only what has actually been accessed

## Data Model

```erDiagram
    api_clients {
        ulid id PK
        ulid company_id FK
        string name
        string client_id "unique"
        string client_secret "sha256 hashed"
        json scopes
        boolean is_active
        timestamps created_at/updated_at
    }

    api_tokens {
        ulid id PK
        ulid api_client_id FK
        string token "hashed"
        timestamp last_used_at
        timestamp expires_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `client_secret` | SHA-256 hash; plaintext shown once on creation only |
| `scopes` | JSON array e.g. `["hr:read", "crm:write"]` |
| `is_active` | Default `true`; set via model `$attributes` not DB default |

## Permissions

- `core.api.manage-clients`
- `core.api.view-clients`
- `core.api.regenerate-secret`
- `core.api.revoke-client`
- `core.api.view-logs`

## Filament

- **Resource:** `ApiClientResource` — list with status badges, create form, edit form, secret regeneration action
- **Pages:** `ListApiClients`, `CreateApiClient`, `EditApiClient`
- **Custom pages:** None
- **Widgets:** None
- **Nav group:** Integrations (app panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Auth0 | API key management |
| Postman API Platform | API credential management |
| AWS API Gateway | API key issuance and rate limiting |
| Zapier (API trigger) | Webhook and API integration |

## Related

- [[webhooks]]
- [[audit-log]]
- [[billing-engine]]
