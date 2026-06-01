---
type: module
domain: Events Management
panel: events
module-key: events.sponsors
status: planned
color: "#4ADE80"
---

# Sponsors

Manage event sponsors: tiers, deliverables, logos, and sponsorship revenue.

## Core Features

- Sponsor record: name, logo, tier, contact, sponsorship amount
- Sponsorship tiers: platinum/gold/silver/bronze with defined benefits
- Deliverables tracking per sponsor (logo placement, booth, speaking slot)
- Sponsor logos on event landing page (by tier)
- Sponsorship revenue tracking (links Finance)
- Sponsor contact management (links CRM)
- Deliverable checklist completion

## Data Model

| Table | Key Columns |
|---|---|
| `ev_sponsors` | company_id, event_id, name, logo_media_id, tier, contact_id, amount_cents, status |
| `ev_sponsor_deliverables` | sponsor_id, company_id, description, status, due_date |

## Filament

**Nav group:** Sponsors

- `SponsorResource` — manage sponsors per event, deliverable checklist
- Sponsorship revenue summary

## Cross-Domain

- Sponsorship revenue can create Finance invoices; contacts link to CRM

## Related

- [[domains/events/events]]
- [[domains/crm/contacts]]
- [[domains/finance/invoicing]]
