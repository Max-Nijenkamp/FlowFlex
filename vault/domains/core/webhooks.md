---
type: module
domain: Core Platform
panel: app
module-key: core.webhooks
status: planned
color: "#4ADE80"
---

# Webhooks

Configurable outbound webhooks on domain events. Companies can subscribe to events (employee hired, invoice paid, deal won) and receive HTTP POST payloads to their own endpoints.

---

## Core Features

- Webhook endpoint management: URL, secret, active events, enabled/disabled
- Event selection: grouped by domain — subscribe to any combination
- Signed payloads: `X-FlowFlex-Signature` header with HMAC-SHA256 of payload + secret
- Delivery log: timestamp, event type, response status, retry count
- Automatic retries: 3 retries with exponential backoff on non-2xx responses
- Test button: send a test payload to verify endpoint is reachable

---

## Data Model

| Table | Key Columns |
|---|---|
| `webhook_endpoints` | company_id, url, secret, events (json array), is_active |
| `webhook_deliveries` | endpoint_id, event_type, payload (json), response_status, delivered_at, attempts |

---

## Filament

**`/app` panel:**
- `WebhookEndpointResource` — list, create, edit endpoints; view delivery log per endpoint

---

## Related

- [[domains/core/api-clients]]
