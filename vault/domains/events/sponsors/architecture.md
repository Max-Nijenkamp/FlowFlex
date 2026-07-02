---
domain: events
module: sponsors
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sponsors — Architecture

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `SponsorService::revenue(eventId): Money` | service | Committed + paid split per event (brick/money). |
| `CreateSponsorInvoiceAction` | lorisleiva action | Soft Finance bridge — draft an invoice for the sponsor amount via the Finance service; store the returned `fin_invoice_id`. Hidden when `finance.invoicing` inactive. |
| `DeliverableReminderCommand` | scheduled command | Send an overdue reminder once per deliverable (guarded by `reminded` flag). |

## Status

- Sponsor `status`: committed / paid *(assumed)*.
- Deliverable `status`: open / done, with an optional `due_date` and a `reminded` boolean for idempotent reminders.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `SponsorResource` | Sponsors | #1 CRUD resource | Per-event; deliverables relation; create-invoice action (soft). |
| Revenue summary widget | Sponsors | #6 widget | Per event by tier. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.sponsors.view-any')
        && BillingService::hasModule('events.sponsors');
}
```

Logos render on the public landing grouped by tier.

## Events

None fired or consumed. Finance/CRM interactions are read/command via their services. See [[../../../architecture/event-bus]].

## Money

- `amount_cents` (bigint) + `currency`; all revenue arithmetic via brick/money.
