---
domain: events
module: sponsors
feature: sponsor-management
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Sponsor Management

Sponsor records per event: tier, logo, contact, amount, and the optional Finance invoice bridge.

## Behaviour

- CRUD a sponsor per event: name, logo, tier, `amount_cents`, optional CRM contact, status (committed/paid).
- "Create invoice" action drafts a Finance invoice (soft) and stores `fin_invoice_id`; hidden when Finance is inactive.
- Logos render on the public landing grouped by tier.

## UI

- **Kind**: simple-resource
- **Page**: `SponsorResource` list + form at `/app/events/sponsors` (nav group "Sponsors"), per-event filter.
- **Layout**: table (logo, name, tier badge, amount, status); form with logo upload, tier select, CRM contact picker, amount; deliverables relation manager.
- **Key interactions**: create/edit sponsor; create-invoice action (soft); status toggle committed→paid.
- **States**: empty (no sponsors → CTA) · loading (skeleton) · error (validation; invoice action hidden if Finance off) · selected (edit form).
- **Gating**: `events.sponsors.view-any`; edit/invoice need `events.sponsors.manage`.

## Data

- Owns / writes: `ev_sponsors` only (and `fin_invoice_id` from the Finance service return).
- Reads: CRM contact (read reference); event (Events service).
- Cross-domain writes: NONE — Finance invoice created via the Finance service; CRM contact read-only ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: revenue → [[sponsor-revenue|Sponsor Revenue]] widget; logos → public landing.
- Shared entity: `crm_contacts` (CRM), `fin_invoices` (Finance) — both referenced, not written.

## Test Checklist

### Unit
- [ ] Sponsor validation: tier, amount integer minor units (brick/money), logo upload contract

### Feature (Pest)
- [ ] Finance bridge visible only when `finance.invoicing` active; invoice drafted once (locked), `fin_invoice_id` stored
- [ ] Tenant isolation + permission on sponsor CRUD

### Livewire
- [ ] Sponsor form validates; invoice action hidden when finance inactive; gated by the sponsors permission

## Unknowns

- Finance-paid → auto status flip; custom tiers — see [[../unknowns]].

## Related

- [[../_module|Sponsors]] · [[deliverables]] · [[sponsor-revenue]] · [[../../finance/invoicing/_module|Finance Invoicing]]
