---
domain: events
module: sponsors
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sponsors

Event sponsors: tiers, deliverables, logos, and sponsorship revenue (with an optional Finance invoice bridge).

## Module-key

| Field | Value |
|---|---|
| key | `events.sponsors` |
| priority | p3 |
| panel | events |
| permission-prefix | `events.sponsors` |
| tables | `ev_sponsors`, `ev_sponsor_deliverables` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../events/_module\|events.events]] | Sponsors per event |
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Hard | [[../../core/file-storage/_module\|core.files]] | Logos |
| Soft | [[../../crm/contacts/_module\|crm.contacts]] | Sponsor contact link (read) |
| Soft | [[../../finance/invoicing/_module\|finance.invoicing]] | Draft-invoice the sponsorship (manual action *(assumed)*) |

## Core Features

- **Sponsor record** — name, logo, tier, contact, sponsorship amount, status (committed/paid *(assumed)*).
- **Tiers** — platinum/gold/silver/bronze (fixed set v1 *(assumed)*).
- **Deliverables** — checklist per sponsor (logo placement, booth, speaking slot) with due dates + overdue reminders.
- **Logos on landing** grouped by tier.
- **Revenue tracking** — brick/money; optional draft invoice when Finance is active.

## See features/

- [[features/sponsor-management|Sponsor Management]] — sponsor records, tiers, contact + revenue.
- [[features/deliverables|Deliverables Tracking]] — the per-sponsor deliverable checklist + reminders.
- [[features/sponsor-revenue|Sponsor Revenue]] — the committed/paid revenue summary widget.

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

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's sponsors data
- [ ] Module gating: artifacts hidden when `events.sponsors` inactive
- [ ] Revenue math committed vs. paid (brick/money).
- [ ] Deliverable reminder fires once.
- [ ] Landing shows logos grouped by tier.
- [ ] Invoice action hidden without `finance.invoicing`.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | contact | crm.contacts | Sponsor `contact_id` links a CRM contact (read) |
| Commands | draft invoice | finance.invoicing | `CreateSponsorInvoiceAction` — manual bridge for the sponsorship amount (soft) |

**Data ownership:** `events.sponsors` writes only `ev_sponsors` + `ev_sponsor_deliverables`. The CRM contact is read-only (`contact_id` reference). The Finance invoice is created through Finance's own service/action (`fin_invoices` owned by Finance), storing the returned `fin_invoice_id` locally — sponsors never writes Finance or CRM tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../events/_module|Events]] · [[../../crm/contacts/_module|CRM Contacts]] · [[../../finance/invoicing/_module|Finance Invoicing]]
- [[../_index|Events MOC]]
