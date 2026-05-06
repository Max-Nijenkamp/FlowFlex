---
tags: [flowflex, domain/crm, overview, phase/3]
domain: CRM & Sales
panel: crm
color: "#2563EB"
status: planned
last_updated: 2026-05-06
---

# CRM Overview

The customer relationship layer. Contacts, deals, support tickets, and the full customer journey — from first touch to loyal advocate.

**Filament Panel:** `crm`
**Domain Colour:** Blue `#2563EB` / Light: `#DBEAFE`
**Domain Icon:** `building-office-2` (Heroicons)
**Phase:** 3 (core: Contacts, Sales Pipeline, Shared Inbox, Customer Support) + 5 (full suite)

## Modules in This Domain

| Module | Phase | Description |
|---|---|---|
| [[Contact & Company Management]] | 3 | 360° contact records, activity timeline |
| [[Sales Pipeline]] | 3 | Deal pipeline, forecasting, win/loss |
| [[Shared Inbox & Email]] | 3 | Shared team inbox, email sequences |
| [[Customer Support & Helpdesk]] | 3 | Ticket management, SLAs, live chat |
| [[Quotes & Proposals]] | 5 | Quote builder, e-sign on acceptance |
| [[Customer Data Platform]] | 5 | Unified customer profile, CDP |
| [[Client Portal]] | 5 | White-labelled self-service portal |
| [[Loyalty & Retention]] | 5 | Points, referrals, churn prediction |

## Key Events from This Domain

| Event | Source | Consumed By |
|---|---|---|
| `TicketResolved` | [[Customer Support & Helpdesk]] | Marketing (trigger CSAT survey), CRM (update contact timeline) |

## Key Events Consumed

| Event | From | What CRM Does |
|---|---|---|
| `InvoiceOverdue` | [[Invoicing]] | Creates follow-up task in CRM |
| `ProjectMilestoneReached` | [[Project Planning]] | Updates deal status |
| `FieldJobCompleted` | [[Field Service Management]] | Updates support ticket |
| `OrderPlaced` | E-commerce | Updates customer record |
| `ContractExpiring` | [[Contract Management]] | Creates renewal task |

## Related

- [[Contact & Company Management]]
- [[Sales Pipeline]]
- [[Customer Support & Helpdesk]]
- [[Panel Map]]
