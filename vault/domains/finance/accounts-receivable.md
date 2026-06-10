---
type: module
domain: Finance & Accounting
domain-key: finance
panel: finance
module-key: finance.ar
status: planned
priority: v1
depends-on: [finance.invoicing, core.billing, core.rbac, core.notifications]
soft-depends: []
fires-events: []
consumes-events: [InvoicePaid]
patterns: [custom-pages, money, email, queues]
tables: [fin_ar_dunning_rules, fin_ar_writeoffs]
permission-prefix: finance.ar
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Accounts Receivable

Customer payment tracking, aging reports, automated dunning, and AR dashboard. Builds on Invoicing.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/finance/invoicing\|finance.invoicing]] | aging/dunning operate on fin_invoices + fin_payments |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, dunning mails |

---

## Core Features

- AR aging buckets: current, 1–30, 31–60, 61–90, 90+ days overdue
- Customer account statement: all invoices, payments, balance per customer
- Automated dunning: escalating reminder emails per aging bucket
- Dunning sequence: configurable steps (friendly → firm → final notice)
- AR turnover ratio and days-sales-outstanding (DSO) metric
- Write-off workflow for uncollectable invoices (posts GL write-off entry via invoicing/ledger services)
- Payment allocation: apply a payment across multiple invoices
- Credit limit tracking per customer (`fin_customers.credit_limit_cents` column added *(assumed)*)

---

## Data Model

### fin_ar_dunning_rules

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| aging_bucket | string | 1-30 / 31-60 / 61-90 / 90+ |
| days_overdue | int | trigger threshold |
| email_template | string | template key |
| escalation_level | int | 1..n, unique `(company_id, escalation_level)` |
| is_active | boolean | default true |

### fin_ar_writeoffs

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), invoice_id FK | ulid | |
| amount_cents | bigint | |
| reason | text | required |
| approved_by | ulid FK users | |
| written_off_at | timestamp | |

Reads from `fin_invoices` + `fin_payments` (Invoicing module). Dunning sends tracked on invoice meta *(assumed: `fin_invoices.last_dunning_level` int column added by this module)*.

---

## DTOs

### WriteOffData — invoice_id, reason (required, max:1000); amount = open balance
### AllocatePaymentData — customer_id, amount_cents (min:1), payment_date, allocations[{invoice_id, amount_cents}] — cross-field: sum(allocations) == amount_cents, each ≤ invoice open balance

## Services & Actions

Interface→Service: `ArServiceInterface` → `ArService`.

- `aging(?string $customerId = null): ArAgingData` — bucketed open balances
- `statement(string $customerId, CarbonImmutable $from, CarbonImmutable $to): StatementData`
- `writeOff(WriteOffData $data): void` — posts GL bad-debt entry, voids remaining balance; `finance.ar.write-off` permission, approver recorded
- `allocatePayment(AllocatePaymentData $data): void` — records per-invoice payments through `InvoiceService::recordPayment`
- `dso(CarbonImmutable $period): float`

## Events

### Consumes: InvoicePaid (from finance.invoicing)
Listener: `UpdateARAgingListener` — recompute aging cache for the account, reset dunning level (per [[architecture/event-bus]]).

---

## Filament

**Nav group:** Invoicing

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ArAgingPage` | #9 report custom page | buckets by customer, drill-down to invoices |
| `CustomerStatementPage` | #9 report custom page | per-customer, period selector, PDF export |
| `DunningRuleResource` | #1 CRUD resource | sequence steps |

---

## Permissions

`finance.ar.view` · `finance.ar.manage-dunning` · `finance.ar.write-off` · `finance.ar.allocate-payment`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ProcessDunningCommand` | notifications | daily 07:00 | per invoice: only fires the next escalation level once (`last_dunning_level` guard) |

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:finance:ar-aging` | 1 h | InvoicePaid listener, payment allocation, write-off |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Aging buckets correct at boundary days (30/31, 90/91)
- [ ] Dunning fires escalation levels in order, once each, stops on payment
- [ ] `InvoicePaid` resets dunning level + busts aging cache
- [ ] Payment allocation sums validated; partial allocations update each invoice state
- [ ] Write-off posts GL entry + records approver
- [ ] DSO computed via brick/money over fixture data

---

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

---

## Related

- [[domains/finance/invoicing]]
- [[domains/finance/general-ledger]]
- [[architecture/event-bus]]
