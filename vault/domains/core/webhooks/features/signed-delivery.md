---
domain: core
module: webhooks
feature: signed-delivery
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Signed Delivery

Parent: [[../_module]] · See [[../architecture]]

Every outbound payload is HMAC-signed.

- `DeliverWebhookJob` computes `X-FlowFlex-Signature` = HMAC-SHA256 of the raw payload keyed by the endpoint secret, then POSTs (10s timeout).
- Receivers verify the header with `hash_equals` (constant-time) against their copy of the secret.
- The secret is stored encrypted (`webhook_endpoints.secret`, `encrypted` cast), shown once at creation, and rotatable via `RotateWebhookSecretAction`.
- See [[../../../security/webhooks-signing]] and [[../security]].

## UI

- **Kind**: background (delivery) + simple-resource (secret management)
- **Page**: signing/POST is background (`DeliverWebhookJob`, no page). The secret is created/rotated on `WebhookEndpointResource` at `/app/webhook-endpoints`.
- **Layout**: endpoint form/detail shows a create-once secret field and a "rotate secret" action; the plaintext appears once in a copy-once modal, never again.
- **Key interactions**: on endpoint create the secret is revealed once (copy); rotate → new plaintext once via `RotateWebhookSecretAction`. Signing itself is unattended per delivery.
- **States**: empty = no secret shown after first reveal (masked) · loading = rotate in progress · error = rotate failure toast · selected = the endpoint whose secret is being rotated.
- **Gating**: `core.webhooks.update` for rotate (+ `BillingService::hasModule('core.webhooks')`); the outbound POST carries no user identity.

## Data

- Owns / writes: `webhook_endpoints.secret` (encrypted `text` cast, create/rotate). No other tables.
- Reads: the raw event payload passed by the dispatcher; the endpoint's own secret to compute the HMAC. No other domain's tables.
- Cross-domain writes: none — the signature is computed in-process and sent over HTTP; nothing is written outside this module. See [[../../../../security/data-ownership]].

## Relations

- Consumes: the raw payload from any domain event (via the `WebhookDispatcher`) — read-only, delivered as-sent.
- Feeds: an external HTTP endpoint (outside FlowFlex); no internal event emitted.
- Shared entity: `webhook_endpoints.secret` (owned here); receivers hold their own copy to verify.

## Test Checklist

### Unit
- [ ] `X-FlowFlex-Signature` equals HMAC-SHA256 of the raw payload keyed by the endpoint secret
- [ ] A tampered payload / wrong secret fails `hash_equals` verification

### Feature (Pest)
- [ ] `DeliverWebhookJob` sets the signature header on the outbound POST; the secret is read from the encrypted column
- [ ] `RotateWebhookSecretAction` returns a new plaintext once, re-encrypts at rest, and holds a lock (no double-rotate); old signatures no longer verify

### Livewire
- [ ] Rotate action shows the new secret once (copy-once modal) and requires `core.webhooks.rotate`
- [ ] Secret field is masked after first reveal; rotate denied when `core.webhooks` inactive
