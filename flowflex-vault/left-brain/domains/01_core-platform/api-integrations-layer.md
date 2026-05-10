---
type: module
domain: Core Platform
panel: app
cssclasses: domain-admin
phase: 1
status: in-progress
migration_range: 010001–019999
last_updated: 2026-05-10
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# API & Integrations Layer

REST API with Laravel Sanctum authentication, allowing third-party systems to read and write FlowFlex data on behalf of a company. Includes API client management, token lifecycle, webhook delivery with HMAC signing, and per-client rate limiting.

**Panel:** `app` (company owner manages their own API clients and webhook endpoints)  
**Phase:** 1 — infrastructure layer required by all integration-capable Phase 2+ modules

---

## Features

### REST API

- Routes defined in `routes/api.php`, protected by `auth:sanctum` guard
- All API responses are company-scoped — a token can only access data belonging to its company
- API versioning prefix: `/api/v1/`
- Rate limiting: 60 requests per minute per API client (Laravel `RateLimiter` by `client_id`)
- Response format: JSON, camelCase keys, ISO 8601 timestamps

### API Client Management

- Company creates named API clients in the app panel (`ApiClientResource`)
- On creation, `client_secret` is shown in plaintext exactly once; it is stored hashed (SHA-256) and cannot be recovered
- Secret can be regenerated; old tokens issued under the previous secret are immediately revoked
- Clients can be individually revoked (soft-delete or `is_active = false`)
- `scopes` JSON field restricts what the client is allowed to read/write (e.g. `["hr:read", "crm:write"]`)

### API Tokens

- Each API client issues short-lived tokens via `POST /api/v1/auth/token`
- `ApiToken` stores a hashed token value, `last_used_at`, and `expires_at`
- Expired tokens are rejected; `last_used_at` updated on every authenticated request

### Webhook Endpoints

- Company registers webhook URLs in the app panel (`WebhookEndpointResource`)
- `events` JSON array declares which event types the endpoint subscribes to (e.g. `["employee.created", "invoice.paid"]`)
- Delivery is a queued job: signs the JSON payload with HMAC-SHA256 using `endpoint.secret`, posts to `url`
- Retry policy: 3 attempts with exponential backoff; failure logged to `notification_log`
- `last_triggered_at` updated on each successful delivery
- Endpoints can be individually toggled active/inactive without deletion

### Filament App Panel Resources

- `ApiClientResource` — list, create, edit, delete; secret shown once on creation; regenerate action
- `WebhookEndpointResource` — list, create, edit, toggle active, delete; delivery log per endpoint

---

## Data Model

```erDiagram
    api_clients {
        ulid id PK
        ulid company_id FK
        string name
        string client_id "unique"
        string client_secret "sha256 hashed"
        json scopes
        boolean is_active "default true"
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

    webhook_endpoints {
        ulid id PK
        ulid company_id FK
        string url
        string secret
        json events
        boolean is_active
        timestamp last_triggered_at
        timestamps created_at/updated_at
    }

    api_clients ||--o{ api_tokens : "has"
```

> Note: `ApiClient::is_active` default must be set via `protected $attributes = ['is_active' => true]` on the model. Eloquent does not read database column defaults.

---

## Permissions

```
core.api.manage-clients
core.api.manage-webhooks
core.api.view-logs
```

---

## Related

- [[MOC_CorePlatform]]
- [[notifications-alerts]] — webhook channel uses WebhookEndpoint
- [[audit-log]] — API actions are audit-logged
- [[module-billing-engine]] — API access may be gated per plan tier
