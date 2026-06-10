---
type: module
domain: Events Management
domain-key: events
panel: events
module-key: events.sponsors
status: planned
priority: p3
depends-on: [events.events, core.billing, core.rbac, core.files]
soft-depends: [crm.contacts, finance.invoicing]
fires-events: []
consumes-events: []
patterns: [money]
tables: [ev_sponsors, ev_sponsor_deliverables]
permission-prefix: events.sponsors
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Sponsors

Manage event sponsors: tiers, deliverables, logos, and sponsorship revenue.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/events/events\|events.events]] | sponsors per event |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, logos |
| Soft | [[domains/crm/contacts\|crm.contacts]] (sponsor contact), [[domains/finance/invoicing\|finance.invoicing]] (invoice the sponsorship — manual create-invoice action *(assumed)*) | links |

---

## Core Features

- Sponsor record: name, logo, tier, contact, sponsorship amount, status (committed/paid *(assumed)*)
- Sponsorship tiers: platinum/gold/silver/bronze (per-company tier config *(assumed: fixed set v1)*)
- Deliverables tracking per sponsor (logo placement, booth, speaking slot) — checklist with due dates
- Sponsor logos on event landing page grouped by tier
- Sponsorship revenue tracking (brick/money; invoice link when finance active)
- Deliverable overdue reminders

---

## Data Model

### ev_sponsors — id, company_id (indexed), event_id FK, name, logo_media_id nullable, tier (in set), contact_id nullable (CRM), amount_cents, currency, status (committed/paid), fin_invoice_id nullable, deleted_at
### ev_sponsor_deliverables — id, sponsor_id FK, company_id, description, status (open/done), due_date nullable, reminded boolean

---

## DTOs

### CreateSponsorData — event_id, name, tier (in set), amount_cents (min:0), contact_id?, deliverables[{description, due_date?}]

## Services & Actions

- `SponsorService::revenue(eventId): Money` — committed + paid split
- `CreateSponsorInvoiceAction` — finance soft bridge (draft invoice for sponsor amount)
- Deliverable reminder command (once per deliverable)

---

## Filament

**Nav group:** Sponsors

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SponsorResource` | #1 CRUD resource | per-event, deliverables relation, create-invoice action (soft) |
| Revenue summary widget | #6 widget | per event by tier |

Logos on landing page by tier.

---

## Permissions

`events.sponsors.view-any` · `events.sponsors.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Revenue math committed vs paid (brick/money)
- [ ] Deliverable reminder once
- [ ] Landing shows logos grouped by tier
- [ ] Invoice action hidden without finance.invoicing

---

## Build Manifest

```
database/migrations/xxxx_create_ev_sponsors_table.php
database/migrations/xxxx_create_ev_sponsor_deliverables_table.php
app/Models/Events/{Sponsor,SponsorDeliverable}.php
app/Data/Events/CreateSponsorData.php
app/Services/Events/SponsorService.php
app/Actions/Events/CreateSponsorInvoiceAction.php
app/Console/Commands/Events/DeliverableReminderCommand.php
app/Filament/Events/Resources/SponsorResource.php
database/factories/Events/SponsorFactory.php
tests/Feature/Events/SponsorTest.php
```

---

## Related

- [[domains/events/events]]
- [[domains/crm/contacts]]
- [[domains/finance/invoicing]]
