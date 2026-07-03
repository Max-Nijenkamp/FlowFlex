---
domain: crm
module: appointment-scheduling
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Appointment Scheduling — Security

## Permissions

| Permission | Grants |
|---|---|
| `crm.scheduling.view-any` | Access the scheduling area (meeting types, bookings). |
| `crm.scheduling.manage-types` | Create / edit meeting types. |
| `crm.scheduling.manage-own-availability` | Edit own working hours. |
| `crm.scheduling.cancel-booking` | Cancel a booking (`CancelBookingAction`) — notifies both sides. |
| `crm.scheduling.mark-no-show` | Mark a booking no-show / completed *(assumed)*. |

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.scheduling.view-any')
        && hasModule('crm.scheduling');
}
```

See [[../../../security/authn-authz]].

## Tenant Isolation

All three tables carry an indexed `company_id` scoped via `BelongsToCompany` / `CompanyScope`. The public booking flow resolves the tenant from `{company-slug}` and pins the company context before any query. See [[../../../security/tenancy-isolation]].

## Public / Portal Guard (HIGH)

The public booking routes live in a dedicated guest/no-auth route group, isolated from the app session guard — no Sanctum session leakage between the public flow and authenticated panels. The middleware stack (tenant resolution from slug, throttle, honeypot) is documented and reviewed. See [[../../../security/authn-authz]] and [[../../../security/webhooks-signing]].

## Rate Limiting

- **Public booking (MEDIUM)** — a named limiter (`RateLimiter::for('public-booking')`) covers the public booking POST, including slot lookup and Stripe PaymentIntent creation, to prevent enumeration and payment-abuse. See [[../../../security/threat-model]].
- **Calendar sync external API (v1.x)** — outbound Google/Outlook calls (token refresh, busy-time fetch, booking push in [[./features/calendar-sync]]) run under a named `panel-action` limiter and provider-side backoff to avoid rate-limit bans *(assumed — deferred with OAuth calendar sync)*.
- **Booking-panel actions** — cancel / no-show / mark-complete that dispatch confirmation/cancellation mail run under the default `panel-action` limiter (comms category).

## Module Gating

Panel artifacts gated behind `hasModule('crm.scheduling')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

| Field | Reason |
|---|---|
| `crm_availability.calendar_connection` | OAuth token blob for Google/Outlook calendar sync (v1.x). Stored as `text`, `encrypted` cast. See [[../../../security/encryption]]. |

## Stripe PaymentIntent

Paid meeting types require a successful PaymentIntent before the booking is confirmed; the intent id is stored on `crm_bookings.stripe_payment_intent_id`. Payment webhooks are signature-verified — see [[../../../security/webhooks-signing]].

## Source Security Notes

- Public route isolation and rate limiting per [[../../../security/threat-model]].
- OAuth blob encryption per [[../../../security/encryption]].
- GDPR: prospect email captured at booking is subject to retention rules — see [[../../../security/data-privacy-gdpr]].
