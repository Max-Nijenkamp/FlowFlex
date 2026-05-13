---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.compliance
status: planned
color: "#4ADE80"
---

# Compliance Calendar

> Track regulatory obligations and internal compliance deadlines with assigned owners, evidence upload, and status tracking in a calendar view.

**Panel:** `legal`
**Module key:** `legal.compliance`

## What It Does

Compliance Calendar is the obligation management system for recurring and one-off compliance requirements — filing deadlines, regulatory reporting, licence renewals, policy review cycles, audit submission windows, and data protection impact assessment reviews. Each obligation has a due date, assigned owner, status, and an evidence slot to upload proof of completion. A calendar view shows all obligations by deadline so the compliance team can spot periods of heavy load and ensure nothing is missed. Owners receive reminders before their deadlines.

## Features

### Core
- Obligation record: title, description, regulatory source or internal policy, obligation type (filing, reporting, renewal, audit, review, training), due date, recurrence, assigned owner
- Recurrence: one-off or recurring (monthly, quarterly, semi-annual, annual)
- Status workflow: upcoming → in progress → complete → overdue → waived
- Evidence upload: attach the completed filing, renewal certificate, or confirmation document to the obligation record as proof of completion
- Reminder schedule: configurable alerts before due date (30, 14, 7, 2 days) sent to the assigned owner
- Calendar view: month view of all obligations; colour-coded by status and type; filter by owner or category

### Advanced
- Obligation library: pre-loaded library of common obligations by jurisdiction and industry (UK/EU GDPR, Companies House filings, VAT returns, health and safety reviews, ISO 9001 management review)
- Obligation tagging: group by regulation, department, or risk level for filtered views
- Obligation dependencies: mark that obligation B cannot begin until obligation A is complete
- Overdue escalation: if obligation passes due date without completion, auto-escalate to compliance manager
- Completion trend: track the % of obligations completed on time per quarter
- Delegation: owner can delegate completion to a colleague; delegation recorded in audit trail

### AI-Powered
- Obligation discovery: based on the company's industry, jurisdiction, and registered activities, suggest obligations that should be added to the calendar
- Due date clustering: flag quarters with an unusually high number of deadlines so the team can plan resource accordingly

## Data Model

```erDiagram
    legal_obligations {
        ulid id PK
        ulid company_id FK
        string title
        text description
        string obligation_type
        string regulatory_source
        ulid assigned_to FK
        date due_date
        string recurrence
        string status
        string evidence_url
        boolean is_overdue
        timestamps timestamps
    }

    legal_obligation_reminders {
        ulid id PK
        ulid obligation_id FK
        integer days_before
        string channel
        boolean sent
        timestamp sent_at
    }

    legal_obligations ||--o{ legal_obligation_reminders : "reminds via"
```

| Table | Purpose |
|---|---|
| `legal_obligations` | Compliance obligation records with due date and owner |
| `legal_obligation_reminders` | Reminder schedule per obligation |

## Permissions

```
legal.compliance.view-any
legal.compliance.create
legal.compliance.update
legal.compliance.complete
legal.compliance.manage-library
```

## Filament

**Resource class:** `ObligationResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `ComplianceCalendarPage` (month/quarter calendar view), `ComplianceStatusPage` (owner-filtered view with RAG status)
**Widgets:** `UpcomingObligationsWidget` (next 14 days), `OverdueObligationsWidget`
**Nav group:** Compliance

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Compliance.ai | Regulatory obligation tracking |
| LogicGate | Compliance calendar and task management |
| ComplyAdvantage | Regulatory deadline management |
| Navex Global | Compliance programme management |

## Related

- [[regulatory-tracking]] — new regulations discovered here become obligations in the calendar
- [[risk-register]] — non-compliance risks recorded in risk register
- [[contracts]] — contract renewal deadlines can be captured as obligations
- [[../it/audit-compliance]] — IT compliance obligations cross-referenced here
