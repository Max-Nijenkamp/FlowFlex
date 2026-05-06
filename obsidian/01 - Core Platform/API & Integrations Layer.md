---
tags: [flowflex, core, api, integrations, webhooks, phase/1]
domain: Core Platform
panel: workspace
color: "#2199C8"
status: built
last_updated: 2026-05-06
---

# API & Integrations Layer

Every active module exposes its data through this layer. Third-party apps and automation tools connect here.

**Who uses it:** Developers, integration specialists, automation workflows
**Filament Panel:** `workspace`
**Depends on:** [[Authentication & Identity]], [[Roles & Permissions (RBAC)]]
**Build complexity:** Low — 1 resource, 1 table

## REST API

- Versioned REST API at `/api/v1/`
- Endpoints per active module (inactive modules return 404)
- Full CRUD where permissions allow
- Consistent response envelope: `{ data, meta, errors }`
- OpenAPI / Swagger documentation auto-generated
- Postman collection export
- API playground in the workspace dashboard

### Route Pattern

```
/api/v1/{module}/{resource}

Examples:
/api/v1/hr/employees
/api/v1/finance/invoices
/api/v1/projects/tasks
```

### Authentication

All API requests require:
```
Authorization: Bearer {api_key}
```

## API Key Management

- Generate named API keys per workspace
- Scoped keys (read-only, specific modules only)
- Key expiry dates
- Last used timestamp
- Revoke keys instantly
- Key rotation reminder alerts

## Webhooks

### Outbound

- Subscribe to events and POST to any URL
- Event selection per webhook (choose which events trigger it)
- Secret signing (HMAC-SHA256) for webhook verification
- Delivery log (see payload, response code, retry history)
- Manual retry on failed deliveries

### Inbound

- Inbound webhook URLs for integrations that push data in
- Route: `/webhooks/{provider}` (e.g. `/webhooks/stripe`, `/webhooks/shopify`)

## Native Connectors

### Productivity

| Connector | Features |
|---|---|
| Google Workspace | Calendar sync, Drive file picker, Gmail logging to CRM |
| Microsoft 365 | Outlook calendar sync, OneDrive, Teams notifications |

### Accounting

| Connector | Features |
|---|---|
| QuickBooks Online | Bidirectional sync: invoices, expenses, contacts |
| Xero | Bidirectional sync: invoices, expenses, bank feeds |

### E-commerce

| Connector | Features |
|---|---|
| Shopify | Products, orders, customers |
| WooCommerce | Products, orders, customers |

### Payments & Communication

| Connector | Features |
|---|---|
| Stripe | Customer data, payment events |
| Twilio | SMS sending, phone number management |
| SendGrid / Resend | Transactional email fallback |

### Automation

| Connector | Features |
|---|---|
| Zapier | Bi-directional: trigger and action zaps |
| Make / Integromat | Same as Zapier |

## Rate Limiting

- Default: 60 req/min per API key
- Starter plan: 30 req/min
- Pro plan: 60 req/min
- Enterprise: configurable, up to 1000 req/min

## GraphQL (Enterprise Tier)

- GraphQL gateway available on Enterprise tier
- Schema auto-generated from active modules
- Full query and mutation support

## Implementation

### Routes

`routes/api.php` — versioned under `/api/v1/`:

```
GET  /api/v1/health              → 200 { status: 'ok', version: '1.0' }  (public)
GET  /api/v1/me                  → company info (authenticated)
GET  /api/v1/modules             → active modules list (authenticated)
```

### Authentication Middleware

`app/Http/Middleware/AuthenticateApiKey.php`
- Accepts `Authorization: Bearer {key}` or `X-API-Key: {key}` header
- SHA-256 hashes the incoming key, looks up `api_keys.key_hash`
- Checks key not expired, soft-deleted, or wrong company
- Sets `$request->attributes->set('api_company', $company)` + `api_key`
- Updates `last_used_at` via `updateQuietly()` (no activity log event)
- Registered as `'api.key'` alias in `bootstrap/app.php`

### API Key Model

`app/Models/ApiKey.php`
- `scopes` → json array (e.g. `['modules:read', 'me:read']`)
- `key_hash` unique; `key_prefix` (first 12 chars) shown in UI for identification
- `isExpired(): bool` — checks `expires_at`
- `generateKey(): array` — returns `{ key, hash, prefix }` for `ff_` + 37 random chars

### Filament UI

`app/Filament/Workspace/Pages/Settings/ManageApiKeys.php`
- Inline table of keys (prefix, name, scopes, last_used_at, expires_at)
- Create-key modal: shows plaintext key **once** with Alpine.js copy button; only hash stored in DB
- Revoke action: soft-deletes the key

### Controllers

`app/Http/Controllers/Api/V1/ApiController.php`
- `me()` — returns company slug, name, locale, timezone
- `modules()` — returns active modules, filtered by key scopes if `modules:read` required

### Future (planned)

- Full CRUD endpoints per module (added as modules are built in Phase 2+)
- Outbound webhooks (subscribe to events, POST to URL)
- Inbound webhook routes `/webhooks/{provider}`
- OpenAPI/Swagger docs auto-generation

## Related

- [[Authentication & Identity]]
- [[Roles & Permissions (RBAC)]]
- [[Security Rules]]
- [[Multi-Tenancy & Workspace]]
- [[Tech Stack]]
