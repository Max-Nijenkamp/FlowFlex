---
domain: communications
module: shared-inbox
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Shared Inbox — API / DTOs

## DTOs

### `SendMessageData` (input)

| Field | Type | Rules |
|---|---|---|
| `conversation_id` | ulid | required, exists in company |
| `body` | text | required; channel capability-validated (e.g. WhatsApp 24h-window check via driver) |
| `is_internal_note` | boolean | default false |
| `attachments` | array | file uploads (MIME/size contract — [[security]]) |

### `InboundMessageData` (normalised driver output → `handleInbound`)

| Field | Type | Notes |
|---|---|---|
| `channel_id` | ulid | resolved by the channel driver |
| `external_party` | string | email / phone of counterpart |
| `body` | text | purified before store |
| `external_id` | string nullable | provider id — dedupe key |
| `attachments` | array | inbound media |
| `meta` | jsonb | provider metadata |

### Output — `MessageData`, `ConversationData`

Read models returned to the inbox UI (message row + conversation header + assignee/status).

## Service surface (read/command API for other modules)

| Method | Kind | Notes |
|---|---|---|
| `InboxService::handleInbound(InboundMessageData): MessageData` | command | called by channel drivers only |
| `InboxService::send(SendMessageData): MessageData` | command | routes through resolved `ChannelDriver::send` |
| `InboxService::assign / setStatus / snooze` | command | conversation lifecycle |
| `ChannelDriverRegistry::register(type, driver)` | registration | channel modules call in their providers |

## Public / Portal Endpoints

None inside this module. Inbound channel **webhooks** live in the channel modules (email/whatsapp/sms), each signature-verified, normalising to `InboundMessageData` and handing to `InboxService`. See those modules' `api.md`.

## Related

- [[_module]] · [[architecture]] · [[data-model]] · [[../../../architecture/patterns/dto-pattern]]
