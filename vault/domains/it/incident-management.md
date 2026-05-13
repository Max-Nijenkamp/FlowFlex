---
type: module
domain: IT & Security
panel: it
module-key: it.incidents
status: planned
color: "#4ADE80"
---

# Incident Management

> Log, classify, and resolve IT incidents with severity tiers, SLA tracking, root cause analysis, and post-incident review.

**Panel:** `it`
**Module key:** `it.incidents`

## What It Does

Incident Management covers major IT events that impact services — a server outage, network failure, security breach, or critical application going down. Unlike a regular service desk ticket (which handles routine requests), an incident triggers an escalation process with severity classification, a dedicated resolver team, real-time status communication to affected users, and a post-incident review to prevent recurrence. The module is aligned to ITIL incident management principles without requiring dedicated ITIL tooling.

## Features

### Core
- Incident creation: title, description, affected service, severity (P1 critical, P2 high, P3 medium, P4 low), initial impact assessment
- Severity-based SLA: P1 = 15-minute response, 4-hour resolution; P2 = 30-minute response, 8-hour resolution; configurable
- Incident status: new → investigating → identified → monitoring → resolved → post-incident review → closed
- Resolver assignment: assign to an individual or an on-call group
- Impact and urgency matrix: automatic priority calculation from impact (number of users affected) and urgency
- Status communication: broadcast incident status updates to affected users via email or in-app notification

### Advanced
- Escalation rules: auto-escalate to next tier if SLA response threshold is breached
- On-call schedule integration: alert the on-call engineer via PagerDuty-style notification (webhook to on-call tool)
- Linked tickets: associate related service desk tickets as child incidents of the major incident
- Timeline: chronological log of all actions, updates, and escalations with timestamps
- Post-incident review (PIR): structured template (what happened, timeline, root cause, contributing factors, actions to prevent recurrence)
- Problem linking: if same root cause recurs, link incident to a problem record for permanent fix tracking

### AI-Powered
- Severity suggestion: AI analyses incident description and affected service to suggest appropriate P-level
- Similar incident search: surface the 3 most similar past incidents with their resolution notes at time of creation

## Data Model

```erDiagram
    it_incidents {
        ulid id PK
        ulid company_id FK
        string incident_number
        string title
        text description
        string affected_service
        string severity
        string status
        integer impacted_user_count
        ulid assigned_to FK
        ulid assigned_team FK
        timestamp response_due_at
        timestamp resolution_due_at
        timestamp resolved_at
        timestamps timestamps
    }

    it_incident_updates {
        ulid id PK
        ulid incident_id FK
        ulid author_id FK
        string status_change
        text message
        boolean notify_affected_users
        timestamp posted_at
    }

    it_post_incident_reviews {
        ulid id PK
        ulid incident_id FK
        text what_happened
        text root_cause
        text contributing_factors
        json action_items
        ulid authored_by FK
        timestamp completed_at
    }

    it_incidents ||--o{ it_incident_updates : "has"
    it_incidents ||--|| it_post_incident_reviews : "reviewed in"
```

| Table | Purpose |
|---|---|
| `it_incidents` | Incident header with severity, SLA, and status |
| `it_incident_updates` | Timeline of status updates and communications |
| `it_post_incident_reviews` | PIR document per resolved incident |

## Permissions

```
it.incidents.view-any
it.incidents.create
it.incidents.update
it.incidents.resolve
it.incidents.manage-pir
```

## Filament

**Resource class:** `IncidentResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `IncidentTimelinePage` (chronological update timeline), `PirPage` (post-incident review form)
**Widgets:** `ActiveIncidentsWidget` (P1 and P2 incidents currently open with SLA countdown)
**Nav group:** Incidents

## Displaces

| Competitor | Feature Replaced |
|---|---|
| ServiceNow Incident Management | ITIL-aligned incident workflow |
| PagerDuty (incident tracking) | Incident classification and PIR |
| Opsgenie | Incident management and escalation |
| Freshservice Incidents | Severity-based incident handling |

## Related

- [[service-desk]] — P3/P4 incidents handled as service desk tickets
- [[change-management]] — emergency changes raised from incidents
- [[it-analytics]] — incident volume and MTTR tracked as IT KPIs
- [[audit-compliance]] — major incidents recorded in audit evidence
