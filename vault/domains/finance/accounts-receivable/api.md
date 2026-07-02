---
domain: finance
module: accounts-receivable
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Receivable — DTOs, Services & Events

## DTOs

### WriteOffData
| Field | Type | Validation |
|---|---|---|
| invoice_id | string | required |
| reason | string | required, max:1000 |

Amount is not supplied — it is the invoice's open balance.

### AllocatePaymentData
| Field | Type | Validation |
|---|---|---|
| customer_id | string | required |
| amount_cents | int | min:1 |
| payment_date | date | required |
| allocations | array<{invoice_id, amount_cents}> | required |

Cross-field: `sum(allocations.amount_cents) === amount_cents`; each allocation `≤` that invoice's open balance.

### ArAgingData / StatementData (output)
- `ArAgingData` — bucketed open balances (current, 1–30, 31–60, 61–90, 90+), optionally per customer.
- `StatementData` — per-customer invoices, payments, and running balance over a period.

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

Interface→Service: `ArServiceInterface` → `ArService`.

- `aging(?string $customerId = null): ArAgingData` — bucketed open balances.
- `statement(string $customerId, CarbonImmutable $from, CarbonImmutable $to): StatementData`.
- `writeOff(WriteOffData $data): void` — posts a GL bad-debt entry, voids the remaining balance; `finance.ar.write-off`, approver recorded.
- `allocatePayment(AllocatePaymentData $data): void` — records per-invoice payments through `InvoiceService::recordPayment`.
- `dso(CarbonImmutable $period): float`.

## Events

### Consumes: `InvoicePaid` (from finance.invoicing)
Listener `UpdateARAgingListener` — recomputes the account's aging cache and resets its dunning level. Queued, `WithCompanyContext`, per the [[../../../architecture/event-bus]] contract.

AR fires no events.

See [[security]], [[../invoicing/_module]], [[../financial-reporting/_module]].
