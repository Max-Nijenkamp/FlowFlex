---
domain: communications
module: whatsapp
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# WhatsApp — API / DTOs

## DTOs

### `CreateTemplateData` (input)

| Field | Type | Rules |
|---|---|---|
| `name` | string | required, provider regex (lowercase_underscore) |
| `category` | enum | in: marketing, utility, authentication |
| `language` | string | required |
| `body` | text | placeholder `{{n}}` syntax validated |
| `variables` | array | sample values |

### `SendTemplateData` (input)

| Field | Type | Rules |
|---|---|---|
| `conversation_id` or `phone_e164` | ulid / string | one required |
| `template_id` | ulid | required, must be `approved` |
| `variable_values` | array | count matches template placeholders |

## Public / Portal Endpoints

### `POST /webhooks/whatsapp` (guest)

- Provider inbound + status webhook. **Verify-token / signature validated before any processing** (fail-closed → 403).
- Normalises payload → `InboundMessageData` → `InboxService::handleInbound` (inbox writes the message row).
- Delivery/read receipts update `comms_messages.delivery_status` via the inbox.
- **Rate-limited** (throttle middleware) — [[security]].

## Provider API (outbound, read/command)

Send message, submit template, poll template status, media up/download — all via the chosen provider (360dialog / Twilio / Meta). Mocked with `Http::fake` in tests.

## Related

- [[_module]] · [[architecture]] · [[../shared-inbox/api]] · [[../../../architecture/patterns/dto-pattern]]
