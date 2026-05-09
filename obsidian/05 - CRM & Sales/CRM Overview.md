---
tags: [flowflex, domain/crm, overview, phase/3]
domain: CRM & Sales
panel: crm
color: "#2563EB"
status: built
last_updated: 2026-05-07
---

# CRM Overview

The customer relationship layer. Contacts, deals, support tickets, and the full customer journey — from first touch to loyal advocate.

**Filament Panel:** `crm`
**Domain Colour:** Blue `#2563EB` / Light: `#DBEAFE`
**Domain Icon:** `building-office-2` (Heroicons)
**Phase:** 3 (core: Contacts, Sales Pipeline, Shared Inbox, Customer Support) · 8 (extensions: Quotes, CDP, Client Portal, Loyalty)

## Modules in This Domain

| Module | Phase | Description |
|---|---|---|
| [[Contact & Company Management]] | 3 | 360° contact records, activity timeline |
| [[Sales Pipeline]] | 3 | Deal pipeline, forecasting, win/loss |
| [[Shared Inbox & Email]] | 3 | Shared team inbox, email sequences |
| [[Customer Support & Helpdesk]] | 3 | Ticket management, SLAs, live chat |
| [[Quotes & Proposals]] | 8 | Quote builder, product lines, e-sign, convert to invoice |
| [[Customer Data Platform]] | 8 | Unified customer profile, cross-touchpoint data |
| [[Client Portal]] | 8 | White-labelled self-service portal for clients |
| [[Loyalty & Retention]] | 8 | Points system, churn scoring, win-back campaigns |
| [[AI Sales Coach]] | 3 | Deal health scores, next best action, win/loss AI analysis |
| [[Revenue Intelligence & Forecasting]] | 3 | 4-model forecast, quota management, rep accuracy tracking |
| [[Deal Room]] | 3 | Branded buyer collaboration portal, mutual action plan |
| [[Sales Sequences & Cadences]] | 3 | Multi-step email + call + LinkedIn outreach automation |
| [[Customer Success Platform]] | 3 | Health scores, success plans, playbooks, QBR builder, renewals |

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
