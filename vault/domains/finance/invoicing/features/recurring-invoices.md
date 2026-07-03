---
domain: finance
module: invoicing
feature: recurring-invoices
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Recurring Invoices

Invoices carry a `recurring_schedule` (monthly / quarterly / annually) and a `next_recurring_at` date.

- `GenerateRecurringInvoicesCommand` (finance queue, daily 05:00) selects rows WHERE `next_recurring_at <= today`, generates the next invoice, and advances `next_recurring_at` in the same transaction.
- Idempotent: re-running on the same day produces one invoice per schedule, not duplicates.
- Generated invoices are intended to auto-generate and send per the schedule (number assigned, PDF, mail queued — same path as a manual send).

UNVERIFIED: whether the recurrence day-of-month is pinned or drifts is not enumerated in the spec.

## UI
- **Kind**: background
- **Page**: no page for the generation itself — `GenerateRecurringInvoicesCommand` runs headless (daily 05:00, finance queue). The only surface is the `InvoiceResource` form, which exposes the `recurring_schedule` and `next_recurring_at` config fields
- **Layout**: standard resource form fields on `InvoiceResource`; scheduled command has no UI
- **Key interactions**: set `recurring_schedule` + `next_recurring_at` on an invoice; the daily command generates the next invoice and advances the date
- **States**: empty (no recurring schedule set — fields blank) · loading (n/a for command) · error (generation failure logged to finance queue / Horizon, no user surface) · selected (invoice form open with schedule fields populated)
- **Gating**: `finance.invoicing.create`

## Data
- Owns / writes: `fin_invoices`, `fin_invoice_lines` (amounts as integer minor units / cents via brick/money)
- Reads: own tables only (source invoice + schedule fields)
- Cross-domain writes: none — generation touches only invoicing's own tables ([[../../../../security/data-ownership]])

## Relations
- Consumes: none
- Feeds: nothing cross-domain directly — generated invoices later fire `InvoicePaid` when paid (see [[payments]])
- In-domain: reuses the manual-send path (number assignment, PDF, queued mail)

> [!warning] UNVERIFIED
> Whether the recurrence day-of-month is pinned or drifts is not enumerated in the spec.

## Test Checklist

### Unit
- [ ] Schedule advance: `next_recurring_at` moves by the correct interval (monthly / quarterly / annually) from the current date
- [ ] Selection predicate matches only rows WHERE `next_recurring_at <= today`

### Feature (Pest)
- [ ] `GenerateRecurringInvoicesCommand` generates one invoice per due schedule and advances `next_recurring_at` in the same transaction (copies lines via brick/money)
- [ ] Idempotent: running the command twice on the same day produces no duplicate invoices (row lock guards double-generation)
- [ ] Tenant isolation: the command only generates within each schedule's own company

<!-- background command — no Livewire surface; schedule fields are configured on InvoiceResource (covered by invoice-lifecycle) -->

See [[../api]], [[../architecture]], [[../../../../architecture/queue-jobs]].
