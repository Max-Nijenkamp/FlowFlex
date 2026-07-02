---
domain: communications
module: sms-channel
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# SMS Channel — API / DTOs

## DTOs

### `ConnectSmsData` (input)

| Field | Type | Rules |
|---|---|---|
| `provider` | enum | in: twilio, vonage |
| `virtual_number_e164` | string | required, E.164 (`propaganistas/laravel-phone`) |
| `api_key` | string | required, verified against provider before save |
| `api_secret` | string | required, verified against provider before save |

Inbound provider payload → normalised `InboundMessageData` (inbox contract).

## Service surface (read API for other modules)

| Method | Kind | Notes |
|---|---|---|
| `OptOutService::isOptedOut(e164): bool` | read | consumed by the driver + [[../broadcast/_module\|comms.broadcast]] materialisation |

## Public / Portal Endpoints

### `POST /webhooks/comms/sms` (guest)

- Provider inbound + delivery-status webhook. **Signature-verified**.
- `STOP` inbound → opt-out row (+ provider confirmation *(assumed)*).
- Normal inbound → `InboundMessageData` → `InboxService::handleInbound`.
- Delivery callbacks → update `comms_messages.delivery_status`.
- **Rate-limited** — [[security]].

## Related

- [[_module]] · [[architecture]] · [[../shared-inbox/api]]
