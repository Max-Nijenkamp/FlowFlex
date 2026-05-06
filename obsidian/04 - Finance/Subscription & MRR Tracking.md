---
tags: [flowflex, domain/finance, subscription, mrr, saas, phase/5]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-06
---

# Subscription & MRR Tracking

For SaaS and subscription businesses running on FlowFlex. Revenue metrics, churn, and recognition.

**Who uses it:** Finance team, founders, investors
**Filament Panel:** `finance`
**Depends on:** [[Invoicing]], [[Contact & Company Management]]
**Phase:** 5

## Events Consumed

- `InvoicePaid` (from [[Invoicing]]) → updates MRR records

## Features

- **MRR dashboard** — total MRR, new MRR, expansion MRR, churned MRR, net MRR
- **ARR** (annualised recurring revenue)
- **Churn rate** — customer and revenue churn, monthly and annual
- **Cohort analysis** — MRR by signup cohort over time
- **Customer lifetime value (LTV) calculation**
- **LTV:CAC ratio tracking**
- **Revenue recognition** — spread recognition over subscription period — ASC 606 / IFRS 15
- **Deferred revenue liability tracking** — cash received but not yet earned
- **Dunning management** — failed payment retry rules and sequences
- **Subscription health dashboard** — at-risk accounts by payment history and engagement

## Related

- [[Finance Overview]]
- [[Invoicing]]
- [[Contact & Company Management]]
- [[Customer Data Platform]]
