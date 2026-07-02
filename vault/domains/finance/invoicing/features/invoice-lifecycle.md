---
domain: finance
module: invoicing
feature: invoice-lifecycle
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — Invoice Lifecycle (State Machine)

Column: `fin_invoices.status` — `InvoiceState` (`spatie/laravel-model-states`).

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `sent` | `finance.invoicing.send` | number assigned (if not yet), PDF generated, mail queued |
| `sent` | `partially_paid` | payment recorded < balance | journal entry posted |
| `sent` / `partially_paid` | `paid` | payment completes balance | fires `InvoicePaid`; journal entry |
| `sent` / `partially_paid` | `overdue` | scheduled command past `due_date` | reminder mail per config |
| `overdue` | `paid` / `partially_paid` | payment | as above |
| `draft` / `sent` / `overdue` | `voided` | `finance.invoicing.void` | reversal entry if anything posted; paid invoices cannot be voided |

- Invoice numbers are assigned at first send, never reused, gap-free per company *(assumed: advisory lock)*. Audited.
- `void` of a paid invoice throws `CannotVoidPaidInvoiceException`.

## UI

- **Kind**: simple-resource (state lives on `InvoiceResource`; transitions are row/header actions, not a bespoke page).
- **Page**: `InvoiceResource` — list + edit (`/finance/invoices`). Header actions: Send, Record payment, Void.
- **Layout**: table columns (number, customer, status badge, total, due date); state as a coloured badge; edit form only enabled while `draft`.
- **Key interactions**: Send → confirm modal → assigns number + queues PDF/mail (optimistic badge flip); Void → confirm + reason; post-`paid` rows are read-only.
- **States**: empty (no invoices → "create your first invoice" CTA) · loading (table skeleton) · error (action toast + retry) · selected (row → edit/infolist).
- **Gating**: view `finance.invoicing.view-any`; Send `finance.invoicing.send`; Void `finance.invoicing.void`; Record payment `finance.invoicing.record-payment`.

## Data

- Owns / writes: `fin_invoices`, `fin_invoice_lines` (own module). Amounts integer minor units via brick/money.
- Reads: none cross-domain for the transition itself.
- Cross-domain writes: journal entries go through `LedgerService::post` (never writes `fin_journal_*` directly); `paid` fires `InvoicePaid` — never writes another domain's tables ([[../../../../security/data-ownership]]).

## Relations

- Feeds: `InvoicePaid` → consumed by [[../../accounts-receivable/_module|finance.ar]] (aging cache bust, dunning reset), [[../../cash-flow/_module|finance.cashflow]] (drop paid inflow), and CRM account rollups.
- In-domain: each transition side-effects a GL post via [[../../general-ledger/_module|finance.ledger]] (direct service call, no event).

See [[../api]], [[../architecture]], [[../../../../architecture/patterns/states]].
