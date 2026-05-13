---
type: domain-index
domain: Support & Help Desk
panel: support
panel-path: /support
panel-color: Green
color: "#4ADE80"
---

# Support & Help Desk

Support & Help Desk is the customer-facing service domain of FlowFlex. It covers the full customer support lifecycle — from inbound ticket creation through SLA management, knowledge self-service, live chat, and agent performance analytics. The Support panel lives at `/support`. It is distinct from the Omnichannel Inbox domain: Support handles structured ticket workflows and SLAs, while Inbox handles unstructured real-time channel conversations.

## Navigation Groups

- **Inbox** — Tickets, Live Chat, Canned Responses
- **Knowledge** — Knowledge Base, Automations
- **Settings** — SLA Policies, Analytics

## Modules

| Module | File | Module Key | Description |
|---|---|---|---|
| Support Tickets | [[support-tickets]] | `support.tickets` | Email-to-ticket, assignment, priority, SLA-tracked status workflow, merge, tagging, bulk actions |
| Knowledge Base | [[knowledge-base]] | `support.knowledge-base` | Public help centre with categories, article versioning, Meilisearch, CSAT feedback |
| Live Chat Widget | [[live-chat-widget]] | `support.live-chat` | Embeddable JS widget, Reverb real-time, AI first-response from knowledge base |
| SLA Management | [[sla-management]] | `support.sla` | SLA policy builder by priority + business hours, breach alerts, SLA reporting |
| Canned Responses | [[canned-responses]] | `support.canned-responses` | Shared reply templates with variable substitution, personal/team/company scope |
| Ticket Automations | [[ticket-automations]] | `support.automations` | Rule-based IF/THEN automation with visual builder, conflict detection, audit log |
| Support Analytics | [[support-analytics]] | `support.analytics` | Volume trends, response time averages, CSAT scores, agent performance, heatmaps |

## Primary Displaces

Zendesk, Freshdesk, Helpscout, Zoho Desk, Intercom

## Related

- [[domains/inbox/INDEX]] — Omnichannel Inbox (channel conversations, WhatsApp, social)
- [[domains/crm/INDEX]] — CRM & Sales (contacts linked to tickets)
- [[domains/communications/INDEX]] — Communications (internal team announcements)
- [[architecture/filament-patterns]]
- [[architecture/module-system]]
