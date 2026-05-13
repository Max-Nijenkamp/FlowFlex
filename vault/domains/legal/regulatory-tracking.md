---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.regulatory
status: planned
color: "#4ADE80"
---

# Regulatory Tracking

> Monitor regulatory changes relevant to the company, assess their impact, and create implementation tasks before enforcement deadlines.

**Panel:** `legal`
**Module key:** `legal.regulatory`

## What It Does

Regulatory Tracking is the early-warning system for new laws, amendments, and regulatory guidance that affect the company. Legal and compliance teams log incoming regulatory changes — from official sources, subscription services, or industry bodies — assess which parts of the business are affected, assign impact ratings, and create structured implementation plans with tasks and deadlines. This ensures that when a regulation comes into force, the company has already acted rather than scrambling at the last moment.

## Features

### Core
- Regulatory change record: title, regulatory body, jurisdiction, regulation name, change type (new legislation, amendment, guidance update, consultation), effective date, description
- Source: record where the change was identified (publication, regulator alert, legal briefing, news)
- Impact assessment: rate business impact (high/medium/low) and identify which business areas are affected (HR, Finance, Operations, Data, Sales, IT)
- Status workflow: identified → under review → impact assessed → implementation planned → implemented → closed
- Implementation tasks: create sub-tasks with owner, due date, and description for each action required to comply
- Effective date alert: notification to responsible owner as the effective date approaches

### Advanced
- Affected jurisdictions filter: view all changes affecting a specific country or region
- Regulatory body subscriptions: configure which regulators to monitor; mark incoming items from each as requiring review
- Regulatory calendar: timeline view of all effective dates for tracked changes
- Consultation tracking: for regulations still in consultation phase, track the response submission deadline and the final implementation date separately
- Change impact map: link a regulatory change to the specific contracts, policies, or processes it affects
- Regulatory digest: weekly summary of new items added to the tracker for distribution to relevant stakeholders

### AI-Powered
- Change summarisation: generate a plain-language summary of the key requirements from uploaded regulatory text
- Impact suggestion: based on the regulation topic and affected jurisdiction, suggest which business functions are most likely to be affected

## Data Model

```erDiagram
    legal_regulatory_changes {
        ulid id PK
        ulid company_id FK
        string title
        string regulatory_body
        string jurisdiction
        string regulation_name
        string change_type
        date effective_date
        date consultation_deadline
        text description
        string source
        string status
        string impact_level
        json affected_areas
        ulid assigned_to FK
        timestamps timestamps
    }

    legal_regulatory_tasks {
        ulid id PK
        ulid change_id FK
        string description
        ulid owner_id FK
        date due_date
        string status
        timestamps timestamps
    }

    legal_regulatory_changes ||--o{ legal_regulatory_tasks : "requires"
```

| Table | Purpose |
|---|---|
| `legal_regulatory_changes` | Regulatory change records with impact assessment |
| `legal_regulatory_tasks` | Implementation tasks per change |

## Permissions

```
legal.regulatory.view-any
legal.regulatory.create
legal.regulatory.assess
legal.regulatory.manage-tasks
legal.regulatory.delete
```

## Filament

**Resource class:** `RegulatoryChangeResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `RegulatoryCalendarPage` (effective date timeline view)
**Widgets:** `UpcomingEffectiveDatesWidget` (regulations taking effect in 90 days)
**Nav group:** Compliance

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Compliance.ai | Regulatory change monitoring and tracking |
| Lex Machina (regulatory) | Regulatory intelligence and tracking |
| Thomson Reuters Regulatory Intelligence | Regulatory change management |
| Clausematch | Regulatory change implementation tracking |

## Related

- [[compliance-calendar]] — implementation deadlines become compliance obligations
- [[risk-register]] — non-compliance risk from regulatory changes recorded here
- [[contracts]] — contracts affected by regulatory changes linked and reviewed
- [[matter-management]] — regulatory investigations tracked as matters
