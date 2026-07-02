---
domain: events
module: sponsors
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sponsors — API / DTOs

## `CreateSponsorData`

| Field | Type | Rules |
|---|---|---|
| `event_id` | ulid | required; exists in company |
| `name` | string | required |
| `tier` | enum | required; in: platinum, gold, silver, bronze |
| `amount_cents` | int | required; min:0 |
| `contact_id` | ulid | nullable; exists in CRM contacts (read) |
| `deliverables` | array | of `{ description, due_date? }` |

## Command / Read API (internal)

- `SponsorService::revenue(eventId): Money` — committed + paid split for the event.
- `CreateSponsorInvoiceAction(sponsor)` — draft a Finance invoice via the Finance service; store `fin_invoice_id`.

## Public / Portal Endpoints

None. Sponsor logos render on the public event landing (read-only, grouped by tier) but there is no sponsor-facing endpoint.
