---
type: adr
date: 2026-05-10
status: decided
color: "#F97316"
---

# Decision: Stripe webhook uses signature verification only when secret is configured

## Context

`StripeWebhookController` needs to verify Stripe's webhook signature to prevent spoofed events. In local dev, `STRIPE_WEBHOOK_SECRET` is not set (using Stripe CLI `stripe listen` provides a different secret each session).

## Options Considered

1. **Always require signature** — Breaks local dev without Stripe CLI; developers must always run `stripe listen`.
2. **Never verify signature** — Security vulnerability in production; any POST to `/stripe/webhook` updates billing status.
3. **Verify only when secret is configured** — Graceful local dev; strict in production when `STRIPE_WEBHOOK_SECRET` is set.

## Decision

```php
$secret = config('services.stripe.webhook_secret');
if ($secret) {
    \Stripe\Webhook::constructEvent($request->getContent(), $sig, $secret);
}
```

`STRIPE_WEBHOOK_SECRET` must be set in production `.env`. Local dev can test without it, but production will always verify.

## Consequences

- Production security: enforced via `STRIPE_WEBHOOK_SECRET` in env
- Local dev: works without Stripe CLI running (useful for seeded test data)
- Risk: if `STRIPE_WEBHOOK_SECRET` is accidentally omitted from production env, webhook is unsecured. Mitigation: add to env validation in `AppServiceProvider` or deployment checklist.

## Related Left Brain

- [[module-billing-engine]]
