---
type: moc
domain: Professional Services Automation
panel: psa
cssclasses: domain-psa
phase: 7
color: "#7E22CE"
last_updated: 2026-05-09
---

# Professional Services Automation (PSA) — Map of Content

Built specifically for agencies, consultancies, IT service firms, and professional services companies. Client engagement management, resource utilisation, project profitability, retainers, and agency-specific billing. Replaces Accelo, BigTime, Teamwork, and Harvest.

**Panel:** `psa`  
**Phase:** 7  
**Migration Range:** `870000–889999`  
**Colour:** Purple `#7E22CE` / Light: `#F5F3FF`  
**Icon:** `heroicon-o-briefcase`

---

## Why This Domain Exists

Agencies and consultancies are a huge ICP but their needs are different from standard project management:
- Profitability per client (not just per project)
- Utilisation rates (are staff billable enough?)
- Retainer management (monthly hours, rollovers, burndown)
- Multi-project resource scheduling
- WIP (Work in Progress) billing for finance

Current tools are either too generic (Jira) or too expensive/complex (Workday PSA, NetSuite). FlowFlex PSA targets agencies 5–200 people.

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Client Engagement Management | 7 | planned | Engagement setup, SOW linking, client contacts |
| Utilisation & Capacity Tracking | 7 | planned | Billable vs non-billable hours, bench tracking, target utilisation |
| Project Profitability | 7 | planned | Revenue vs cost per project, per client, per engagement type |
| Retainer & SOW Management | 7 | planned | Monthly hour buckets, rollover rules, burndown tracking |
| Resource Scheduling (PSA) | 7 | planned | Cross-project resource demand, forward-looking capacity, role matching |
| Agency Billing Intelligence | 7 | planned | WIP billing, milestone billing, time-based billing, client reports |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `RetainerHoursBurnedDown` | Retainer Management | Notifications (account manager + client), Finance |
| `UtilisationBelowTarget` | Utilisation Tracking | Notifications (operations manager), PSA (bench report) |
| `ProjectMarginsWarning` | Project Profitability | Notifications (project manager, director) |
| `TimeEntryApproved` | Projects (consumed) | PSA Billing (mark billable), Utilisation (update metrics) |

---

## Filament Panel Structure

**Navigation Groups:**
- `Clients` — Engagements, SOWs, Retainers
- `Resources` — Utilisation Dashboard, Capacity Planner, Bench
- `Profitability` — Project P&L, Client P&L, Margin Reports
- `Billing` — WIP Register, Billing Queue, Client Reports

---

## Relationship to Projects Domain

PSA extends the Projects domain — it reads time entries, tasks, and project data from `projects` panel and adds the agency-specific financial and resourcing layer on top. Does not duplicate project management features — those live in the `projects` panel.

---

## Permissions Prefix

`psa.engagements.*` · `psa.utilisation.*` · `psa.profitability.*`  
`psa.retainers.*` · `psa.billing.*`

---

## Competitors Displaced

Accelo · BigTime · Teamwork · Harvest · Float (resource planning) · Forecast.app · Productive.io

---

## Related

- [[MOC_Domains]]
- [[MOC_Projects]] — PSA reads from projects domain
- [[MOC_Finance]] — billing → invoicing
- [[MOC_CRM]] — client engagements linked to CRM contacts/companies
- [[entity-project]]
