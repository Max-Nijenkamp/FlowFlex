---
type: moc
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
color: "#DC2626"
last_updated: 2026-05-08
---

# CRM & Sales — Map of Content

Complete customer relationship management. Contacts, pipeline, helpdesk, proposals, client portal, AI sales coaching, and customer success.

**Panel:** `crm`  
**Phase:** 3 (core) · 8 (extensions)  
**Migration Range:** `250000–299999`  
**Colour:** Red `#DC2626` / Light: `#FEF2F2`  
**Icon:** `heroicon-o-user-group`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Contact & Company Management | 3 | 📅 planned | Contact profiles, company records, interaction history |
| Sales Pipeline | 3 | 📅 planned | Kanban pipeline, deal stages, forecasting |
| Shared Inbox & Email | 3 | 📅 planned | Team inbox, email threading, assignment rules |
| Customer Support & Helpdesk | 3 | 📅 planned | Tickets, SLAs, agent queue, CSAT, macros |
| Quotes & Proposals | 3 | 📅 planned | Quote builder, PDF export, e-sign, acceptance |
| Customer Data Platform | 8 | planned | Unified customer profile, event stream, segments |
| Client Portal | 8 | planned | Customer self-service: invoices, tickets, projects |
| Loyalty & Retention | 8 | planned | Points, rewards, retention campaigns |
| AI Sales Coach | 8 | planned | Call analysis, rep coaching, deal risk scoring |
| [[sales-forecasting\|Sales Forecasting & Commit]] | 8 | planned | Rep commit, ML probability, gap-to-target, accuracy tracking |
| [[commission-management\|Commission Management]] | 8 | planned | Commission plans, splits, earnings portal, accruals |
| [[contract-lifecycle-management\|Contract Lifecycle Management]] | 8 | planned | Contract negotiation, redlining, renewal pipeline |
| Revenue Intelligence | 8 | planned | Pipeline AI, win probability, scenario modelling |
| Deal Room | 8 | planned | Branded deal microsite, mutual action plan, buyer analytics |
| Sales Sequences & Cadences | 8 | planned | Automated email+call+LinkedIn sequences |
| Customer Success Platform | 8 | planned | Health scores, success plans, QBR builder, renewals |
| [[cpq\|CPQ — Configure, Price, Quote]] | 8 | planned | Product configurator, price books, discount approvals |
| [[partner-relationship-management\|Partner Relationship Management]] | 8 | planned | Reseller portal, deal registration, MDF management |
| [[territory-quota-management\|Territory & Quota Management]] | 8 | planned | Territory assignment, quota setting, commission plans |
| [[telephony-call-center\|Telephony & Call Center]] | 3 | planned | CTI, click-to-call, IVR, call recording, transcription |
| [[email-tracking\|Email Tracking & Send from CRM]] | 3 | planned | Open/click tracking, send from CRM, reply detection |
| [[meeting-scheduler\|Meeting Scheduler]] | 3 | planned | Calendly-style booking pages, round-robin, CRM auto-log |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `DealClosed` | Sales Pipeline | Finance (create invoice), Projects (create project) |
| `DealLost` | Sales Pipeline | Analytics, CRM (nurture sequence) |
| `TicketResolved` | Helpdesk | Marketing (CSAT survey), Analytics |
| `ContactCreated` | Contact Management | Marketing (add to list) |
| `FormSubmissionReceived` | Marketing (consumed) | CRM (auto-create contact) |
| `InvoicePaid` | Finance (consumed) | CRM (update customer record) |
| `EventRegistrationReceived` | Marketing (consumed) | CRM (create/update contact) |

---

## Permissions Prefix

`crm.contacts.*` · `crm.pipeline.*` · `crm.inbox.*` · `crm.tickets.*`  
`crm.quotes.*` · `crm.portal.*` · `crm.sequences.*`

---

## Competitors Displaced

Salesforce · HubSpot CRM · Pipedrive · Freshsales · Zendesk · Intercom · Gainsight

---

## Related

- [[MOC_Domains]]
- [[entity-contact]]
- [[MOC_Finance]] — deal → invoice
- [[MOC_Marketing]] — form submissions → contacts
- [[MOC_Projects]] — closed deal → project kickoff
