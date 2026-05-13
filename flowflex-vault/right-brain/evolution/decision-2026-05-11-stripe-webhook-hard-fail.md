---
type: adr
date: 2026-05-11
status: decided
color: "#F97316"
---

# Decision: Stripe webhook hard-fails (500) when secret not configured

## Context

Security audit 2026-05-11 flagged that `StripeWebhookController` returned HTTP 200 when `STRIPE_WEBHOOK_SECRET` was not configured. Prior pattern (see `decision-2026-05-10-stripe-webhook-pattern`) was intentional: graceful local dev, no secret required.

The audit identified this as a security vulnerability: a missing secret means webhook signatures are never verified, so any actor can POST to `/stripe/webhook` and trigger subscription state changes (upgrades, cancellations) without a valid Stripe signature.

## Options Considered

1. **Graceful degradation (prior approach)** — skip verification when secret absent; return 200. Safe for local dev; dangerous in production if env var forgotten on deploy.
2. **Hard-fail with 500** — return 500 and log critical error when secret not configured. Breaks local dev unless `STRIPE_WEBHOOK_SECRET` is set; prevents silent security hole in production.
3. **Hard-fail only in production** — check `app()->environment('production')` and only fail there. Adds conditional complexity; still allows accidental misconfiguration on staging.

## Decision

**Option 2: hard-fail with 500.** Changed `StripeWebhookController` to:

```php
if (! config('services.stripe.webhook_secret')) {
    Log::critical('STRIPE_WEBHOOK_SECRET not configured — rejecting all webhook requests');
    abort(500, 'Webhook secret not configured');
}
```

Local dev must set `STRIPE_WEBHOOK_SECRET` (use Stripe CLI `stripe listen --forward-to ...` which provides a real signing secret). The risk of a silent production misconfiguration outweighs the minor local dev friction.

## Consequences

- Local dev requires `STRIPE_WEBHOOK_SECRET` in `.env` (use `stripe listen` CLI)
- Deployment checklist must include verifying `STRIPE_WEBHOOK_SECRET` is set
- Prior ADR `decision-2026-05-10-stripe-webhook-pattern` is superseded by this decision

## Related Left Brain

- [[billing-subscriptions]] — Stripe webhook handling
