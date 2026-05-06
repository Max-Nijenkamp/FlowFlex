---
tags: [flowflex, domain/crm, support, helpdesk, tickets, phase/3]
domain: CRM & Sales
panel: crm
color: "#2563EB"
status: planned
last_updated: 2026-05-06
---

# Customer Support & Helpdesk

Multi-channel customer support. Email, live chat, and self-service portal — all in one ticket queue.

**Who uses it:** Customer support team, customers
**Filament Panel:** `crm`
**Depends on:** [[Contact & Company Management]]
**Phase:** 3
**Build complexity:** Very High — 3 resources, 3 pages, 8 tables

## Events Fired

- `TicketResolved` → consumed by [[Email Marketing]] (trigger CSAT survey), CRM (update contact timeline)

## Events Consumed

- `FieldJobCompleted` (from [[Field Service Management]]) → closes related support ticket

## Sub-modules

### Ticket Management
- Ticket creation from email, form, live chat
- Ticket assignment (manual or rule-based)
- Status workflow: Open → In Progress → Pending Customer → Resolved → Closed
- Internal notes (not visible to customer)
- Merge duplicate tickets

### SLA Rules
- Response time SLA (must first-reply within N hours)
- Resolution time SLA (must resolve within N hours)
- SLA breach alerts
- SLA pause rules (pause clock when waiting on customer)
- Escalation on SLA breach

### Canned Responses
- Library of saved reply templates
- Variable insertion (customer name, ticket number, agent name)
- Team or personal response libraries

### CSAT Surveys
- Auto-send CSAT survey on ticket resolution
- Rating scale (1–5 or thumbs up/down)
- Optional follow-up comment
- CSAT score dashboard per agent, per team

### Multi-Channel Inbox
- Email (via shared inbox)
- Live chat widget (embeddable on website)
- Web form
- Rule-based chatbot (answer common questions automatically)

### Customer Self-Service Portal
- Customer logs in to see their ticket history
- Submit new tickets
- Track status
- Browse knowledge base articles (links to [[Knowledge Base & Wiki]])

### Slack-Native Ticketing
- Customers can raise tickets directly from a Slack channel
- Agent responses appear back in Slack

## Database Tables (8)

1. `tickets` — ticket records
2. `ticket_messages` — all messages on a ticket thread
3. `ticket_sla_rules` — SLA configuration per priority/type
4. `ticket_sla_breaches` — SLA breach records
5. `canned_responses` — saved response library
6. `csat_surveys` — CSAT survey instances
7. `csat_responses` — customer survey responses
8. `chatbot_rules` — automated response rules

## Related

- [[CRM Overview]]
- [[Contact & Company Management]]
- [[Shared Inbox & Email]]
- [[Knowledge Base & Wiki]]
- [[Email Marketing]]
- [[Client Portal]]
