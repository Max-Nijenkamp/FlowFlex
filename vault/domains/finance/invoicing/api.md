---
domain: finance
module: invoicing
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Invoicing — DTOs, Services & Events

## DTOs

### CreateInvoiceData
| Field | Type | Validation |
|---|---|---|
| customer_id | string | required, ulid in company |
| issue_date / due_date | CarbonImmutable | required; due ≥ issue |
| lines | array<{description, quantity, unit_price_cents, tax_rate_id?}> | min:1; quantity ≥ 0.01; unit_price ≥ 0 |
| discount_percent | float | between:0,100 |
| notes | ?string | max:2000 |
| recurring_schedule | ?string | in:monthly,quarterly,annually |

### RecordPaymentData
| Field | Type | Validation |
|---|---|---|
| invoice_id | string | required |
| amount_cents | int | min:1; ≤ open balance ("Payment exceeds the open balance.") |
| payment_date | CarbonImmutable | required |
| payment_method | string | in set |
| reference_number | ?string | max:100 |

### InvoiceData (output)
id, invoice_number, customer_name, status, issue_date, due_date, subtotal_cents, tax_total_cents, total_cents, paid_amount_cents, balance_cents, currency, total_formatted, lines[], payments[].

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

Interface→Service: `InvoiceServiceInterface` → `InvoiceService`.

- `create(CreateInvoiceData $data): InvoiceData` — totals computed via brick/money.
- `send(string $invoiceId): InvoiceData` — assigns number, generates PDF, queues mail.
- `recordPayment(RecordPaymentData $data): InvoiceData` — state move + `LedgerService::post` (AR ↓ / cash ↑); fires `InvoicePaid` when balance hits 0.
- `void(string $invoiceId, string $reason): InvoiceData` — throws `CannotVoidPaidInvoiceException`.
- Actions: `RecalculateInvoiceTotals`, `DuplicateInvoiceAction`.

## Events

### Fires: `InvoicePaid`
| Payload field | Type |
|---|---|
| company_id | string |
| invoice_id | string |
| crm_account_id | ?string |
| amount_cents | int |
| currency | string |
| paid_at | CarbonImmutable |

### Consumes: `DealWon` (from crm.deals)
Listener `CreateInvoiceStubListener` — creates a draft invoice, lines from `crm_deal_products` (fallback single line "Deal: {name}"), due date = customer payment terms, never auto-sent; no-op when the module is inactive. Contract: [[../../../architecture/event-bus]].

Invoice/payment postings into the ledger are direct in-domain service calls — no events.

See [[security]], [[../general-ledger/_module]], [[../financial-reporting/_module]].
