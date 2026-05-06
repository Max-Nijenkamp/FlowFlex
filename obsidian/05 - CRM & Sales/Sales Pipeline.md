---
tags: [flowflex, domain/crm, sales-pipeline, deals, phase/3]
domain: CRM & Sales
panel: crm
color: "#2563EB"
status: planned
last_updated: 2026-05-06
---

# Sales Pipeline

Visual deal pipeline with custom stages. From first contact to closed-won.

**Who uses it:** Sales team, sales managers
**Filament Panel:** `crm`
**Depends on:** [[Contact & Company Management]]
**Phase:** 3
**Build complexity:** High — 2 resources, 2 pages, 4 tables

## Events Consumed

- `ProjectMilestoneReached` (from [[Project Planning]]) → updates related deal status
- `QuoteAccepted` → auto-creates deal in pipeline (from [[Quotes & Proposals]])

## Features

- **Visual deal pipeline** — kanban view with custom stages
- **Probability weighting** — assign close probability per stage
- **Revenue forecasting** — weighted pipeline sum
- **Win/loss tracking** — with reason codes for lost deals
- **Multiple pipelines** — by product line, by region, by team
- **Deal rotation rules** — auto-assign new deals from a round-robin pool
- **Stale deal alerts** — deals with no activity in N days
- **Deal card details** — contact, company, value, close date, owner, tags

## Related

- [[CRM Overview]]
- [[Contact & Company Management]]
- [[Quotes & Proposals]]
- [[Invoicing]]
- [[Client Billing & Retainers]]
