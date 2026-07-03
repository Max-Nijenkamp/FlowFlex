---
domain: core
module: billing-engine
feature: monthly-invoicing
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Monthly Invoicing

Parent: [[../_module]] · See [[../architecture]] · [[../data-model]]

Monthly invoice calculation: `sum(module_price_per_user) × active_user_count`, snapshotted into `billing_invoices` + `billing_invoice_lines`.

- `GenerateMonthlyInvoicesCommand` — finance queue, monthly 1st 01:00. Idempotent via unique `(company_id, period_start)`; re-run skips existing.
- `BillingService::generateMonthlyInvoice($companyId, $period): BillingInvoiceData` — creates a `draft` invoice, then a Stripe invoice (→ `open`).
- Each line snapshots `module_key, module_name, user_count, unit_price_cents, line_total_cents` at billing time.
- Recurring invoice PDF generation + email delivery via `InvoiceMail` (notifications queue, on invoice open).
- Money math via brick/money; amounts are integer minor units.

## UI

- **Kind**: background (generation) + simple-resource (viewing)
- **Page**: generation is `GenerateMonthlyInvoicesCommand` (background, no page); invoices are viewed on `BillingResource` list at `/app/billing` (Pages/ListBillingInvoices).
- **Layout**: invoice list table — period, total (formatted currency), status badge (draft/open/paid/past_due/uncollectible), paid_at. Row → invoice detail with line breakdown (module name, user count, unit price, line total) and a download-PDF action.
- **Key interactions**: viewer opens the billing list, filters by status/period, opens an invoice, downloads the PDF. Generation itself is unattended (cron, 1st of month 01:00).
- **States**: empty = "No invoices yet" before the first billing run · loading = table skeleton · error = failed generation logged to the finance queue (surfaced in /admin, not this list) · selected = invoice detail row expanded with its lines.
- **Gating**: `core.billing.view` (+ `BillingService::hasModule('core.billing')`).

## Data

- Owns / writes: `billing_invoices`, `billing_invoice_lines` (this module's tables). Line rows snapshot `module_key`, `module_name`, `user_count`, `unit_price_cents`, `line_total_cents` at billing time.
- Reads: `module_catalog` (Sushi, owned by this module) for prices; active-user count and company currency read-only via `core.settings` / tenancy — never written here.
- Cross-domain writes: none. Email delivery (`InvoiceMail`) is this module's own mailable; it does not write other domains' tables. See [[../../../../security/data-ownership]].

## Relations

- Consumes: none directly for generation (cron-triggered).
- Feeds: an `open` invoice transition triggers `InvoiceMail` (notifications queue) — delivery infrastructure only, no cross-domain event.
- Shared entity: `module_catalog` pricing (owned here, read by [[../../module-marketplace/_module]]).

## Test Checklist

### Unit
- [ ] Invoice total = `sum(module_price_per_user) × active_user_count` via brick/money, correct to the cent
- [ ] Each line snapshots `module_key` / `module_name` / `user_count` / `unit_price_cents` / `line_total_cents` at billing time

### Feature (Pest)
- [ ] `GenerateMonthlyInvoicesCommand` idempotent — running twice produces one invoice per `(company_id, period_start)`
- [ ] Tenant isolation: company A's invoices invisible to company B

### Livewire
- [ ] `BillingResource` list is read-only (no create/edit/delete); invoice-PDF download action present
- [ ] `canAccess()` denied without `core.billing.view-any` or when `core.billing` inactive
