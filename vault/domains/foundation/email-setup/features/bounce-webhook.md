---
domain: foundation
module: email-setup
feature: bounce-webhook
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Bounce Webhook (signature-verified suppression)

Resend posts bounce/complaint events to a signed webhook; a hard bounce flags the address undeliverable so we stop mailing it.

## Behaviour

- `POST /api/resend/webhook` → `ResendWebhookController` (invokable), behind `VerifyResendSignature` + `throttle:60,1`.
- Invalid/unsigned request → rejected before the controller, no state change.
- Valid hard-bounce → `HandleEmailBounceAction` sets `users.email_deliverable = false`.
- Stateless API route: no session/CSRF — the signature is the auth ([[security]]).

## UI

- **Kind**: background (inbound webhook — no screen). Suppression state (`email_deliverable`) may surface as a
  read-only flag in user admin screens owned by other modules.

## Data

- Owns: no tables. Writes: `users.email_deliverable` (scaffold column, foundation-owned).
- Cross-domain writes: none.

## Relations

- Consumes: Resend webhook events (external). Feeds: [[branded-mailable]] suppression check.
- Shared entity: `users` table ([[../../laravel-scaffold/data-model]]).

## Test Checklist

### Unit
- [ ] Hard-bounce event payload maps to `email_deliverable = false`; non-hard-bounce types are no-ops

### Feature (Pest)
- [ ] Valid-signature hard bounce flags the address undeliverable (`BounceWebhookTest`)
- [ ] Invalid/unsigned request rejected before the controller — no state change
- [ ] Webhook route enforces `throttle:60,1`

## Unknowns

> [!warning] UNVERIFIED — exact signature header/secret env var + reject status code; soft-bounce/complaint
> handling beyond hard-bounce. See [[api]], [[security]], [[../unknowns]].

## Related

- [[../_module|Email Setup]] · [[api]] · [[security]] · [[../../../../security/webhooks-signing]]
