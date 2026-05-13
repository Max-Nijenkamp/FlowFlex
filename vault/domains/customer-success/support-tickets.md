---
type: module
domain: Customer Success
panel: cs
module-key: cs.tickets
status: planned
color: "#4ADE80"
---

# Support Tickets (CS View)

> A read-only CS-lens into customer support history — ticket volume, unresolved escalations, and QBR summaries — without leaving the Customer Success panel.

**Panel:** `/cs`
**Module key:** `cs.tickets`

## What It Does

This is not a help desk. It is a lightweight view into the Support domain's ticket data, filtered to the accounts and contacts managed by a CS manager. CS managers need to see the support burden carried by each customer — high ticket volume, long resolution times, and unresolved tickets are critical churn risk signals. This module surfaces that data directly on account pages, feeds it into the health score calculation (via the Health Scores module), and generates a ticket summary suitable for inclusion in a Quarterly Business Review (QBR). All data reads from `support_tickets` (Support domain) — no new write models.

## Features

### Core
- Account ticket history: on any CRM account/contact page within the CS panel, a relation manager tab shows all support tickets raised by that account's contacts — with ticket ID, subject, status, priority, created date, and resolution time
- Unresolved ticket count: a per-account count of currently open tickets surfaced on the account health score dashboard as a contributing signal
- Ticket status distribution: per account, a breakdown of tickets by status (open / in progress / resolved / closed) and by priority (critical / high / medium / low) — shown as a small stacked bar chart
- CS escalation path: CS managers can flag a support ticket for escalation from within the CS panel — sets an `escalated_to_cs` boolean on the `support_tickets` record and notifies the assigned CS manager; the support team sees the escalation flag in their queue
- Resolution time visibility: for each account, display the mean and 90th-percentile ticket resolution time over the past 90 days — colour-coded against the company's SLA targets if configured

### Advanced
- QBR ticket summary: generate a plain-English paragraph summarising an account's support activity over a configurable period (e.g. last quarter): total tickets, resolved %, average resolution time, top recurring issue categories — ready to paste into a QBR deck
- Health score contribution: the number of open critical/high tickets and the mean resolution time are published as signals to the Health Scores module — CS managers configure the weighting via Health Scores settings, not here
- Ticket trend chart: per account, a time-series chart of ticket volume per week over the past 6 months — surfaced in the account detail view so CS can see if support load is increasing
- Aggregate portfolio view: for a CS manager responsible for a named account set, a table of all their accounts ranked by current open critical tickets — a quick escalation triage view

### AI-Powered
- Issue theme detection: AI analyses the subject lines and descriptions of an account's last 20 tickets using GPT-4o to extract recurring themes (e.g. "API authentication errors", "billing discrepancies", "slow load times") — surfaced as a theme list on the account support tab to help CS managers prepare proactive conversations
- QBR narrative generation: given the ticket summary data, AI drafts a two-paragraph QBR narrative covering support activity and any concerns — editable before inclusion in a QBR document

## Data Model

This module has no standalone data model. All ticket data is read from the Support domain's `support_tickets` table. The only additions are:

```
support_tickets (existing — Support domain adds these columns for CS use)
├── escalated_to_cs boolean default false
└── cs_escalation_owner_id ulid FK nullable (references users)
```

These two columns are added via a migration in the Support domain's migration range — CS module reads them, Support module writes the base ticket data.

## Permissions

```
cs.tickets.view-account-tickets
cs.tickets.escalate-tickets
cs.tickets.view-portfolio-summary
cs.tickets.generate-qbr-summary
cs.tickets.view-ticket-trends
```

## Filament

- **Relation manager:** `CustomerTicketsRelationManager` added to the Account/Contact resource in the CS panel — shows ticket history as a read-only table with sortable columns and a filter for status and priority
- **Widget:** `AccountSupportSummaryWidget` on the account detail page — shows open ticket count (with RAG colour), mean resolution time, and a mini trend sparkline; includes an "Escalate" action button that opens a modal to flag a ticket for CS escalation
- **Custom page:** `CsPortfolioTicketsPage` — shows a table of all accounts managed by the logged-in CS manager, ranked by open critical ticket count; a quick-scan escalation triage view
- **No standard CRUD resource** — this module is entirely read-focused; the only write action is the escalation flag on existing tickets
- **Nav group:** Accounts (cs panel)

## Displaces

No direct displacement — this is a view layer that prevents CS managers from needing to context-switch to the Support panel to check customer ticket history. It closes a gap that would otherwise require manual cross-panel navigation or separate exports.

## Related

- [[health-scores]]
- [[success-plans]]
- [[playbooks]]
- [[churn-risk]]
- [[../support/INDEX]]
- [[../crm/contacts]]

## Implementation Notes

### Cross-Domain Data Access
This module reads from the Support domain's `support_tickets` table. The CS panel must have a database policy allowing SELECT on `support_tickets` filtered by `company_id`. No writes are made to `support_tickets` except the two escalation columns, which are managed by a dedicated `EscalateTicketAction` in the CS domain that calls `support_tickets` via a repository interface rather than directly modifying the model — to respect domain boundaries.

Define a `SupportDomain::TicketRepositoryInterface` that the CS module depends on, with a concrete implementation in the Support domain. The CS domain never imports Support domain model classes directly.

### Health Score Integration
The Health Scores module (cs.health) pulls the open critical ticket count and mean resolution time as signals via a `SupportHealthSignalProvider` class. This provider is registered by the Support domain's ServiceProvider and called by the Health Scores calculation job. The CS Tickets module does not push data to health scores — the health scores engine pulls it on demand.

### QBR Summary Generation
The QBR summary is generated on demand (not cached) by a `GenerateQbrSummaryJob` that aggregates the last 90 days of ticket data for the account and either returns a formatted text summary or passes it to GPT-4o for narrative generation. The job result is returned inline (not queued to email) since QBR sessions are interactive — the CS manager is sitting at the screen when they click "Generate Summary".
