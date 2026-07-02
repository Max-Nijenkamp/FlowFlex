---
domain: core
module: webhooks
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Webhooks — API (DTOs)

Parent: [[_module]] · See also [[architecture]]

Fires no events and consumes none directly — the `WebhookDispatcher` listens to the whole [[../../../architecture/event-bus]] map generically. The cross-module surface is the endpoint resource, its two actions (see [[architecture]]), and one DTO.

## DTOs

### CreateWebhookEndpointData (input)

| Field | Type | Validation |
|---|---|---|
| url | string | required, url, starts with `https://` |
| events | array<string> | required, each in the event-bus map, each module active |

Message: "Webhook URLs must use HTTPS."

## Signature contract

Outbound payloads carry `X-FlowFlex-Signature` = HMAC-SHA256 of the raw payload keyed by the endpoint secret. Consumers verify with `hash_equals`. See [[../../../security/webhooks-signing]].
