---
domain: security
type: security
build-status: planned
status: unverified
color: "#EF4444"
updated: 2026-06-20
---

# Webhook Signature Verification

Inbound webhooks are **signature-verified + throttled + session/CSRF-free**. Two providers today:

| Webhook | Route | Middleware | Controller |
|---|---|---|---|
| Stripe (billing events) | `POST /api/stripe/webhook` | `VerifyStripeSignature` + `throttle` | `StripeWebhookController` |
| Resend (email bounces/complaints) | `POST /api/resend/webhook` | `VerifyResendSignature` + `throttle:60,1` | `ResendWebhookController` |

Both middleware live in `app/Http/Middleware/`. Signature failure → reject before any handler runs.
No detail leaked to anonymous callers (see [[threat-model]]).

Outbound webhooks (platform → customer endpoints) are signed by `WebhookDispatcher` and delivered via
`DeliverWebhookJob`; see [[../domains/core/webhooks/_module]].

## Related

- [[../infrastructure/mail]] · [[../domains/core/billing-engine/_module]] · [[../architecture/security]] · [[_moc|Security MOC]]
