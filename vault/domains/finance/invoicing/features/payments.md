---
domain: finance
module: invoicing
feature: payments
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Payments

Payments are recorded against an invoice (`fin_payments`); partial payments are supported.

- `recordPayment(RecordPaymentData)` validates `amount_cents` ≤ open balance ("Payment exceeds the open balance."), then moves invoice state.
- A payment below the balance moves the invoice to `partially_paid`; a payment completing the balance moves it to `paid` and fires `InvoicePaid`.
- Each payment posts a balanced journal entry via `LedgerService::post` (AR ↓ / cash ↑) — a direct in-domain call, no event.
- Overpayment is rejected.
- Payment methods: bank-transfer / stripe / cash / other, with an optional `reference_number`.

## UI
- **Kind**: custom-page
- **Page**: record-payment slide-over launched from `InvoiceResource` (+ payments relation-manager) — `/finance/invoices/{id}`
- **Layout**: right-side slide-over over the invoice record: amount input (defaulted to open balance), payment method select, payment date, `reference_number`, submit
- **Key interactions**: enter amount ≤ open balance → submit → state transition + journal post; relation-manager lists prior payments inline
- **States**: empty (no payments yet — relation-manager shows empty state) · loading (submit spinner) · error ("Payment exceeds the open balance." / overpayment rejected) · selected (invoice record open, slide-over primed with default amount)
- **Gating**: `finance.invoicing.record-payment`

## Data
- Owns / writes: `fin_payments` (amounts as integer minor units / cents via brick/money)
- Reads: `fin_invoices` open balance (own module)
- Cross-domain writes: balanced GL journal (AR ↓ / cash ↑) via `LedgerService::post` only — never writes `fin_journal_*` directly ([[../../../../security/data-ownership]])

## Relations
- Consumes: none directly
- Feeds: `InvoicePaid` (fired when a payment completes the balance) → consumed by finance.ar, finance.cashflow, CRM
- In-domain: `LedgerService::post` for the AR/cash entry; `InvoicePaid` is consumed indirectly by finance.ar payment-allocation and finance.currency (FX realised gain/loss)

## Test Checklist

### Unit
- [ ] `amount_cents` ≤ open balance validation (brick/money, integer minor units); overpayment rejected with "Payment exceeds the open balance."
- [ ] Payment below balance yields `partially_paid`; payment completing balance yields `paid`

### Feature (Pest)
- [ ] `recordPayment` posts a balanced GL entry (AR ↓ / cash ↑) via `LedgerService::post` and moves invoice state under `DB::transaction()` + `lockForUpdate()`
- [ ] Payment completing the balance fires `InvoicePaid` with the contract payload; partial payment does not fire it
- [ ] Concurrency: two simultaneous payments on one invoice cannot overpay (row lock re-reads balance); tenant isolation on the invoice read

### Livewire
- [ ] Record-payment slide-over validates amount, defaults to open balance, and is gated by `finance.invoicing.record-payment`
- [ ] Payments relation manager lists prior payments for the tenant only

See [[../api]], [[../data-model]], [[../../general-ledger/_module]].
