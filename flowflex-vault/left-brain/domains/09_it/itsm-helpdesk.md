---
type: module
domain: IT & Security
panel: it
phase: 3
status: planned
cssclasses: domain-it
migration_range: 603000–603499
last_updated: 2026-05-09
---

# ITSM Helpdesk

ITIL-aligned IT service management. Internal helpdesk for employees. Incident management, service requests, SLA tracking, and knowledge base. Replaces a separate Jira Service Management or Freshservice.

---

## Ticket Types

| Type | Example | Target SLA |
|---|---|---|
| Incident | Laptop won't turn on | 4h response, 8h resolution |
| Service request | New software licence | 2 business days |
| Access request | Grant access to shared drive | 1 business day |
| Change request | See [[change-management-itil]] | Per change type |
| Problem | Same incident recurring | Root cause investigation |

---

## Ticket Lifecycle

```
Employee submits ticket (portal, email, Slack)
→ Auto-classified by AI (type + category + priority)
→ Assigned to IT queue or specific agent
→ Agent works ticket
→ Updates employee via portal/email
→ Resolved → employee confirms or auto-close after 48h
→ Closed
```

---

## Employee Self-Service Portal

- Submit tickets with form + attachments
- Track status of all my open tickets
- Search knowledge base before submitting (deflects ~30% of tickets)
- Browse service catalogue (request new equipment, access, software)

---

## SLA Management

SLA rules per ticket type + priority:
- Response time: time from creation to first response
- Resolution time: time from creation to resolved
- Business hours or 24/7 (configurable per SLA)
- Breach alerts: notify team lead when SLA at risk
- SLA report: % in compliance by team, agent, ticket type

---

## Automation Rules

- Auto-assign by keyword: "VPN" → network team, "Mac" → hardware team
- Auto-escalate if no response in X hours
- Auto-close resolved tickets after 48h if no feedback
- Auto-suggest KB articles when ticket created (AI match on subject)

---

## Data Model

### `it_tickets`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| ticket_number | varchar(50) | |
| type | enum | incident/request/access/change/problem |
| priority | enum | critical/high/medium/low |
| status | enum | open/in_progress/waiting/resolved/closed |
| submitter_id | ulid | FK employee |
| assigned_to | ulid | nullable FK |
| category | varchar(100) | |
| subject | varchar(300) | |
| response_due_at | timestamp | nullable |
| resolution_due_at | timestamp | nullable |
| resolved_at | timestamp | nullable |

---

## Migration

```
603000_create_it_tickets_table
603001_create_it_ticket_comments_table
603002_create_it_sla_policies_table
```

---

## Related

- [[MOC_IT]]
- [[service-catalog-it]]
- [[change-management-itil]]
- [[team-password-secrets-vault]]
