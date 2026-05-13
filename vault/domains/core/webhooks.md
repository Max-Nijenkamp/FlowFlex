---
type: module
domain: Core Platform
panel: app
module-key: core.webhooks
status: planned
color: "#4ADE80"
---

# Webhooks

> Outbound webhook endpoint management — register URLs, subscribe to event types, and receive HMAC-SHA256-signed payloads for every domain event.

**Panel:** `app`
**Module key:** `core.webhooks`

## What It Does

The Webhooks module lets companies register external URLs that receive signed HTTP POST payloads whenever a subscribed event fires in FlowFlex. Any domain event (EmployeeHired, InvoicePaid, DealWon, LeaveApproved) can be subscribed to via a webhook endpoint. Delivery is handled as a queued job with three retry attempts and exponential backoff. Every delivery attempt is logged. Endpoints can be individually toggled active or inactive without deletion. The HMAC-SHA256 signature in the `X-FlowFlex-Signature` header lets the receiving server verify payload authenticity.

## Features

### Core
- `WebhookEndpointResource`: list, create, edit, toggle active/inactive, delete
- `events` JSON array on each endpoint declares which event types it subscribes to (e.g. `["employee.created", "invoice.paid"]`)
- Delivery: queued job posts JSON payload to endpoint URL with `X-FlowFlex-Signature: sha256=<hmac>` header
- HMAC-SHA256 signature computed with `endpoint.secret` against the raw JSON body
- Retry policy: 3 attempts with exponential backoff (30s, 5m, 30m); failure logged to `webhook_deliveries`

### Advanced
- Delivery log per endpoint: timestamp, event type, HTTP response code, response time, status (delivered/failed/retried)
- Secret rotation: owner can regenerate the endpoint secret; old secret immediately invalid
- Event catalog: Filament select component showing all available event types grouped by domain — prevents typos in event subscriptions
- Endpoint health badge: shows last-delivery status on endpoint list row — green (delivered), amber (retrying), red (failed)
- Test delivery: trigger a `test.ping` event to any endpoint on demand to verify connectivity

### AI-Powered
- Failure pattern detection: if an endpoint returns 5xx for 24 hours, auto-disable and notify owner rather than continuing to queue failed deliveries
- Payload schema documentation: AI-generated payload schema shown in Filament for each event type so developers know exactly what JSON to expect

## Data Model

```erDiagram
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

    webhook_deliveries {
        ulid id PK
        ulid webhook_endpoint_id FK
        ulid company_id FK
        string event_type
        integer http_status
        integer response_time_ms
        string status
        integer attempt
        json request_payload
        text response_body
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `events` | JSON array of subscribed event type strings |
| `secret` | Used for HMAC-SHA256 signature; stored encrypted |
| `status` | delivered / failed / retrying |
| `attempt` | Which retry attempt this delivery row represents |

## Permissions

- `core.webhooks.view`
- `core.webhooks.create`
- `core.webhooks.edit`
- `core.webhooks.delete`
- `core.webhooks.view-deliveries`

## Filament

- **Resource:** `WebhookEndpointResource` — list with health badge, create form, edit form, delivery log action
- **Pages:** `ListWebhookEndpoints`, `CreateWebhookEndpoint`, `EditWebhookEndpoint`
- **Custom pages:** None
- **Widgets:** None
- **Nav group:** Integrations (app panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Zapier (webhook trigger) | Outbound webhook delivery |
| Make (Integromat) | Webhook-based automation triggers |
| Svix | Managed webhook delivery service |
| Hookdeck | Webhook gateway and delivery management |

## Related

- [[api-clients]]
- [[notifications]]
- [[audit-log]]
