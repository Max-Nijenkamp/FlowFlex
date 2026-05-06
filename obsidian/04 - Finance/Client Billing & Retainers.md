---
tags: [flowflex, domain/finance, client-billing, retainers, phase/5]
domain: Finance & Accounting
panel: finance
color: "#059669"
status: planned
last_updated: 2026-05-06
---

# Client Billing & Retainers

For professional services firms. Convert time and expenses into client invoices, manage retainer balances, and give clients a payment portal.

**Who uses it:** Account managers, finance team
**Filament Panel:** `finance`
**Depends on:** [[Invoicing]], [[Time Tracking]], [[Contact & Company Management]] (client records)
**Phase:** 5

## Events Consumed

- `TimeEntryApproved` (from [[Time Tracking]]) → marks time as billable, available for invoicing

## Features

- **Time-to-invoice conversion** — select a client, date range → all unbilled time becomes invoice line items
- **Expense-to-invoice conversion** — add approved client expenses to invoice
- **Retainer setup** — client pays monthly cap in advance, hours drawn down against it
- **Retainer balance tracking** — hours consumed vs hours remaining in period
- **Rollover rules** — unused hours carry over to next period — or don't
- **Overage billing** — hours above retainer cap billed at standard or premium rate
- **Retainer burn rate alert** — client used 80% of retainer, notify account manager
- **Client payment portal** — branded, client logs in to see all invoices and pay online
- **Unbilled time alert** — flag projects where time hasn't been billed in over 30 days
- **Client profitability report** — revenue vs payroll/time cost per client

## Related

- [[Finance Overview]]
- [[Invoicing]]
- [[Time Tracking]]
- [[Contact & Company Management]]
- [[Client Portal]]
