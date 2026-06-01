---
type: module
domain: Finance & Accounting
panel: finance
module-key: finance.ar
status: planned
color: "#4ADE80"
---

# Accounts Receivable

Customer payment tracking, aging reports, automated dunning, and AR dashboard. Builds on Invoicing.

## Core Features

- AR aging buckets: current, 1–30, 31–60, 61–90, 90+ days overdue
- Customer account statement: all invoices, payments, balance per customer
- Automated dunning: escalating reminder emails per aging bucket
- Dunning sequence: configurable steps (friendly → firm → final notice)
- AR turnover ratio and days-sales-outstanding (DSO) metric
- Write-off workflow for uncollectable invoices
- Payment allocation: apply a payment across multiple invoices
- Credit limit tracking per customer

## Data Model

| Table | Key Columns |
|---|---|
| `fin_ar_dunning_rules` | company_id, aging_bucket, days_overdue, email_template, escalation_level |
| `fin_ar_writeoffs` | company_id, invoice_id, amount_cents, reason, approved_by, written_off_at |

Reads from `fin_invoices` + `fin_payments` (Invoicing module).

## Filament

**Nav group:** Invoicing

- `ArAgingPage` (custom page) — aging report by customer with drill-down
- `CustomerStatementPage` (custom page) — per-customer statement
- `DunningRuleResource` — configure dunning sequences

## Cross-Domain / Jobs

- Scheduled dunning job sends reminders per bucket (see [[architecture/queue-jobs]], [[architecture/email]])

## Related

- [[domains/finance/invoicing]]
- [[domains/finance/general-ledger]]
