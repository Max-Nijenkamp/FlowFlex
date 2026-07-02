---
domain: events
module: sponsors
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Sponsors — Decisions

## ADR: Finance invoice via soft manual bridge

- **Context:** Sponsorship revenue should be invoiceable.
- **Decision:** `CreateSponsorInvoiceAction` drafts a Finance invoice through the Finance service and stores the returned `fin_invoice_id`; the action is hidden when `finance.invoicing` is inactive (manual v1 *(assumed)*).
- **Consequences:** Loose coupling; Finance owns `fin_invoices`. Sponsors works fully without Finance.

## ADR: CRM contact is a read-only reference

- **Context:** A sponsor may map to a CRM contact.
- **Decision:** Store `contact_id` as a soft read reference; never write `crm_contacts` from Sponsors.
- **Consequences:** Sponsors degrades gracefully without CRM; no bounded-context write leak. See [[../../../security/data-ownership]].

## ADR: Fixed tier set in v1

- **Context:** Sponsorship tiers vary by company.
- **Decision (assumed):** v1 ships a fixed tier set (platinum/gold/silver/bronze); per-company custom tiers deferred.
- **Consequences:** Simpler landing grouping; revisit if companies need custom tiers.

## ADR: Idempotent deliverable reminders

- **Context:** Overdue deliverables need chasing without spam.
- **Decision:** `DeliverableReminderCommand` sends one reminder per deliverable, guarded by a `reminded` flag.
- **Consequences:** No duplicate reminders; flag reset semantics for re-reminding are undecided ([[unknowns]]).
