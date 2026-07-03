---
domain: finance
module: accounts-receivable
feature: payment-allocation
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Payment Allocation

Apply a single customer payment across multiple open invoices.

- Driven by `AllocatePaymentData`: `customer_id`, `amount_cents` (min:1), `payment_date`, and `allocations[{invoice_id, amount_cents}]`.
- Cross-field validation: `sum(allocations) === amount_cents`, and each allocation `≤` that invoice's open balance.
- `ArService::allocatePayment` records per-invoice payments through `InvoiceService::recordPayment` — invoice/payment rows stay owned by [[../../invoicing/_module|finance.invoicing]].
- Partial allocations update each invoice's state; the aging cache is busted.
- Permission: `finance.ar.allocate-payment`. All amounts via brick/money.

## UI
- **Kind**: custom-page
- **Page**: "Allocate payment" slide-over (`/finance/ar/allocate`)
- **Layout**: customer picker → list of that customer's open invoices, each with an allocation-amount input → live sum-check against the total payment amount
- **Key interactions**: pick customer, enter per-invoice amounts, live validation that `sum(allocations) === amount_cents` and each ≤ that invoice's open balance; submit
- **States**: empty (customer has no open invoices) · loading (submit / recordPayment) · error (sum mismatch or allocation exceeds open balance) · selected (customer chosen, open invoices listed with inputs)
- **Gating**: `finance.ar.allocate-payment`

## Data
- Owns / writes: no invoice/payment rows of its own (amounts as integer minor units / cents via brick/money)
- Reads: open invoices for the selected customer (via finance.invoicing)
- Cross-domain writes: per-invoice payments recorded through `InvoiceService::recordPayment` — those `fin_payments`/`fin_invoices` rows stay owned by finance.invoicing; write only through its owning service, never its tables ([[../../../../security/data-ownership]])

## Relations
- Consumes: none directly
- Feeds: each `InvoiceService::recordPayment` may fire `InvoicePaid` (owned by finance.invoicing) when a balance completes
- In-domain: `ArService::allocatePayment` orchestrates the split and busts the aging cache

## Test Checklist

### Unit
- [ ] Cross-field validation: `sum(allocations.amount_cents) === amount_cents` (brick/money integer cents)
- [ ] Each allocation is rejected when it exceeds that invoice's open balance

### Feature (Pest)
- [ ] `allocatePayment` records per-invoice payments through `InvoiceService::recordPayment` (never writes `fin_payments`/`fin_invoices` directly) under a pessimistic money lock, and busts the aging cache
- [ ] Partial allocations update each invoice's state; over-allocation across the batch is rejected; tenant isolation — cannot allocate against another company's invoices; `allocate-payment` permission enforced

### Livewire
- [ ] The allocation action live-validates the running sum against the payment total and blocks submit on mismatch; `canAccess` / action denied without `finance.ar.allocate-payment`

See [[../api]], [[../architecture]].
