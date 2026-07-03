---
domain: finance
module: accounts-receivable
type: module
module-key: finance.ar
priority: v1
build-status: planned
status: wip
depends-on: [finance.invoicing, core.billing, core.rbac, core.notifications]
soft-depends: []
fires-events: []
consumes-events: [InvoicePaid]
patterns: [custom-pages, money, email, queues]
tables: [fin_ar_dunning_rules, fin_ar_writeoffs]
permission-prefix: finance.ar
encrypted-fields: []
color: "#4ADE80"
updated: 2026-07-03
---

# Accounts Receivable

Customer payment tracking, aging reports, automated dunning, and an AR dashboard. Builds on Invoicing — aging and dunning operate over the invoices and payments that module owns.

> Rebuild blueprint. Code was stripped to the [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell|app/admin shell]]; nothing here is built yet. This spec is the source of truth for the rebuild.

## Module-key

`finance.ar`

**Priority:** v1  
**Panel:** finance  
**Permission prefix:** `finance.ar`  
**Tables:** `fin_ar_dunning_rules`, `fin_ar_writeoffs`

## Purpose

AR turns the raw invoice/payment ledger into collections workflow: bucket open balances by age, chase overdue customers with escalating reminders, allocate incoming payments across invoices, and write off what can't be collected. It owns the dunning rules and write-off records; everything else it reads from [[../invoicing/_module|finance.invoicing]].

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../invoicing/_module\|finance.invoicing]] | aging/dunning operate on `fin_invoices` + `fin_payments` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, dunning mails |

## Core Features

- AR aging buckets: current, 1–30, 31–60, 61–90, 90+ days overdue.
- Customer account statement: all invoices, payments, and balance per customer.
- Automated dunning: escalating reminder emails per aging bucket.
- Dunning sequence: configurable steps (friendly → firm → final notice).
- AR turnover ratio and days-sales-outstanding (DSO) metric.
- Write-off workflow for uncollectable invoices (posts a GL write-off entry via invoicing/ledger services).
- Payment allocation: apply a single payment across multiple invoices.
- Credit-limit tracking per customer (`fin_customers.credit_limit_cents` column added) *(assumed)*.

## Permissions

`finance.ar.view-any` · `finance.ar.view` · `finance.ar.manage-dunning` · `finance.ar.write-off` · `finance.ar.allocate-payment`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ProcessDunningCommand` | notifications | daily 07:00 | per invoice: fires the next escalation level only once (`last_dunning_level` guard) |

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:finance:ar-aging` | 1 h | `InvoicePaid` listener, payment allocation, write-off |

See [[../../../architecture/caching]].

## Test Checklist

- [ ] Tenant isolation: company A cannot age company B invoices, edit its dunning rules, allocate against or write off its invoices
- [ ] Module gating: artifacts hidden when `finance.ar` inactive
- [ ] Aging buckets correct at boundary days (30/31, 90/91)
- [ ] Dunning fires escalation levels in order, once each, stops on payment
- [ ] `InvoicePaid` resets dunning level + busts aging cache
- [ ] Payment allocation sums validated; partial allocations update each invoice state
- [ ] Write-off posts GL entry + records approver
- [ ] DSO computed via brick/money over fixture data

## Build Manifest

```
database/migrations/xxxx_create_fin_ar_dunning_rules_table.php
database/migrations/xxxx_create_fin_ar_writeoffs_table.php
database/migrations/xxxx_add_ar_columns_to_fin_invoices_and_customers.php
app/Models/Finance/{DunningRule,ArWriteoff}.php
app/Data/Finance/{WriteOffData,AllocatePaymentData,ArAgingData,StatementData}.php
app/Contracts/Finance/ArServiceInterface.php
app/Services/Finance/ArService.php
app/Listeners/Finance/UpdateARAgingListener.php
app/Console/Commands/Finance/ProcessDunningCommand.php
app/Mail/Finance/DunningMail.php
app/Filament/Finance/Pages/{ArAgingPage,CustomerStatementPage}.php
app/Filament/Finance/Resources/DunningRuleResource.php
database/factories/Finance/DunningRuleFactory.php
tests/Feature/Finance/{ArAgingTest,DunningTest,PaymentAllocationTest}.php
```

## Cross-Domain Edges

**Data ownership.** This module writes only its own tables (`fin_ar_dunning_rules`, `fin_ar_writeoffs`); all cross-domain effects happen via events or the owning domain's service — never a direct write into another domain's tables ([[../../../security/data-ownership]]). Payment rows are owned by invoicing, not AR.

| Direction | Event / Call | Counterpart |
|---|---|---|
| Consumes | `InvoicePaid` → reset dunning + bust aging cache | [[../invoicing/_module\|finance.invoicing]] |
| Reads | `fin_invoices` + `fin_payments` (read-only) for aging | [[../invoicing/_module\|finance.invoicing]] |
| Calls | `InvoiceService::recordPayment` for allocation (payment rows owned by invoicing) | [[../invoicing/_module\|finance.invoicing]] |
| Calls | write-off posts bad-debt GL via ledger/invoicing services | [[../general-ledger/_module\|finance.ledger]] |

## Entity Notes

- [[architecture]] — services, money handling, dunning job, GL posting path
- [[data-model]] — tables + ERD (and the source tables AR reads from invoicing)
- [[api]] — DTOs, service methods, events
- [[security]] — access contract, permissions
- [[decisions]] — credit-limit + dunning-tracking column additions
- [[unknowns]] — `*(assumed)*` items
- Features: [[features/aging-report]], [[features/dunning]], [[features/payment-allocation]], [[features/write-off]]

## Related

- [[../invoicing/_module]]
- [[../general-ledger/_module]]
- [[../financial-reporting/_module]]
- [[../../../architecture/event-bus]]
- [[../../../glossary]]
