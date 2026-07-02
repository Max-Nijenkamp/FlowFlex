---
domain: events
module: tickets
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tickets — API / DTOs

## `PurchaseTicketData` (public, via registration flow)

| Field | Type | Rules |
|---|---|---|
| `registration_id` | ulid | required; registered + not yet confirmed |
| `ticket_id` | ulid | required; in sales window; not sold out |
| `discount_code` | string | nullable; valid + under `max_uses` |

Validation messages are attendee-facing ("This ticket is sold out.").

## `RefundTicketData`

| Field | Type | Rules |
|---|---|---|
| `purchase_id` | ulid | required; status `paid` |
| `reason` | string | nullable |

## Command API (internal)

- `TicketService::purchase(PurchaseTicketData)` — creates PaymentIntent; confirmation happens on webhook.
- `TicketService::refund(RefundTicketData)` — Stripe refund + registration cancel + count decrement.

## Public / Portal + Webhook Endpoints

| Route | Method | Auth | Purpose |
|---|---|---|---|
| purchase (on landing) | POST | guest (rate-limited) | Start a paid purchase (Stripe Elements). |
| discount validate | POST | guest (rate-limited) | Validate a discount code (enumeration-throttled). |
| Stripe webhook | POST | signature-verified | `payment_intent.succeeded` / refund events. |
