---
type: module
domain: IT & Security
panel: it
module-key: it.service-desk
status: planned
color: "#4ADE80"
---

# Service Desk

> Internal IT helpdesk for employee requests and incidents — with a self-service portal, SLA tracking, and AI-powered ticket classification.

**Panel:** `it`
**Module key:** `it.service-desk`

## What It Does

Service Desk is the primary contact point between employees and the IT team. Employees submit tickets via a self-service portal, email, or Slack. Each ticket is automatically classified by type (incident, service request, access request, problem), assigned a priority, and routed to the appropriate IT agent or team queue. SLA targets define response and resolution deadlines. An integrated knowledge base deflects routine requests before they become tickets, reducing IT team workload.

## Features

### Core
- Ticket submission: self-service portal with form + file attachment; email-to-ticket; Slack slash command
- Ticket types: incident, service request, access request, problem
- Priority: critical, high, medium, low — based on impact and urgency
- Status workflow: open → in progress → waiting (for employee or third party) → resolved → closed
- SLA targets: per ticket type and priority; response time and resolution time tracked with countdown
- Assignment: to individual agent or to a team queue; round-robin auto-assign available
- Agent workspace: ticket list with filters, inline reply composer, internal notes, and related asset/employee panel

### Advanced
- Knowledge base integration: employees see suggested KB articles as they type their ticket subject; deflects ~30% of submissions
- SLA breach alerts: notification to team lead when a ticket SLA is at risk
- Automation rules: auto-assign by keyword (VPN → network team), auto-close resolved tickets after 48h, auto-suggest KB articles
- Linked assets: associate a ticket with the affected asset record from [[asset-management]]
- Employee satisfaction survey: CSAT survey sent after ticket closure; responses logged per agent
- Escalation to incident: one-click escalation from a service ticket to a major incident record in [[incident-management]]

### AI-Powered
- Auto-classification: AI reads ticket subject and body and suggests type, category, and priority for agent confirmation
- Resolution suggestion: surface the top 3 knowledge base articles and similar past resolved tickets at time of assignment

## Data Model

```erDiagram
    it_tickets {
        ulid id PK
        ulid company_id FK
        string ticket_number
        string type
        string priority
        string status
        string category
        string subject
        text description
        ulid submitter_id FK
        ulid assigned_to FK
        ulid assigned_team FK
        ulid asset_id FK
        timestamp response_due_at
        timestamp resolution_due_at
        timestamp first_response_at
        timestamp resolved_at
        integer csat_score
        timestamps timestamps
    }

    it_ticket_comments {
        ulid id PK
        ulid ticket_id FK
        ulid author_id FK
        text body
        boolean is_internal
        json attachments
        timestamp posted_at
    }

    it_tickets ||--o{ it_ticket_comments : "has"
```

| Table | Purpose |
|---|---|
| `it_tickets` | Ticket header with type, priority, SLA, and status |
| `it_ticket_comments` | Public replies and internal notes |

## Permissions

```
it.service-desk.view-any
it.service-desk.submit
it.service-desk.respond
it.service-desk.assign
it.service-desk.close
```

## Filament

**Resource class:** `TicketResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `AgentDashboardPage` (queue view with SLA countdowns and filters), `EmployeePortalPage` (self-service submission and tracking)
**Widgets:** `OpenTicketsByPriorityWidget`, `SlaComplianceWidget`
**Nav group:** Incidents

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Freshservice | IT helpdesk with SLA and knowledge base |
| Jira Service Management | IT service desk and request portal |
| ServiceNow ITSM (SMB) | Incident and request management |
| Zendesk (internal IT) | Ticket management and self-service portal |

## Related

- [[incident-management]] — tickets escalate to incidents for P1/P2 events
- [[change-management]] — service requests may become change requests
- [[access-management]] — access request ticket type routed here
- [[asset-management]] — tickets linked to the asset in fault
- [[it-analytics]] — ticket volume, SLA %, and CSAT tracked as IT KPIs
