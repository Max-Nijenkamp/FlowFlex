---
domain: communications
module: email-channel
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Email Channel — API / DTOs

## DTOs

### `ConnectEmailChannelData` (input)

| Field | Type | Rules |
|---|---|---|
| `address` | string | required, email |
| `signature` | text nullable | purified HTML |

Inbound provider payload → normalised `InboundMessageData` (the inbox contract; see [[../shared-inbox/api]]).

## Public / Portal Endpoints

### `POST /webhooks/comms/email/inbound` (guest)

- Provider inbound-email webhook (Resend / inbound relay *(assumed)*). **Signature-verified**; unknown `inbound_token` → dropped.
- Spam-score header over threshold → dropped + logged.
- Parses HTML/plain → `InboundMessageData` → `InboxService::handleInbound`.
- **Rate-limited** (throttle) — [[security]].

## Related

- [[_module]] · [[architecture]] · [[../shared-inbox/api]] · [[../../../architecture/patterns/dto-pattern]]
