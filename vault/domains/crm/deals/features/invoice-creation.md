---
domain: crm
module: deals
type: feature
feature: invoice-creation
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Invoice Creation from Won Deal

## Purpose

Allow a sales rep to generate a Finance invoice stub directly from a won deal, pre-populated with the deal's line items. Reduces manual data re-entry between CRM and Finance.

---

## Trigger

`CreateInvoiceAction` — a modal action on the Deal view page.

**Visibility rules:**
- Only visible when `deal.status == won`
- Only visible when `BillingService::hasModule('finance.invoicing')` is true
- Hidden entirely when the finance.invoicing module is inactive (soft dependency)

---

## Mechanism

The `DealWon` event (fired on close-as-won) is consumed by `finance.invoicing`'s `CreateInvoiceStubListener`:

- Creates a **draft invoice** in Finance
- Line items populated from `crm_deal_products`
- Due date = company default payment terms
- Invoice is NOT auto-sent — stays as draft for the finance team to review

The `CreateInvoiceAction` in the UI is an additional **manual trigger** for the same outcome — it calls the Finance service (or dispatches an equivalent event) to create the stub on demand.

---

## Degraded Behaviour

When `finance.invoicing` is inactive:
- `DealWon` fires but is unconsumed (no listener registered)
- `CreateInvoiceAction` is hidden
- The rep sees no invoice option — no error, no broken UI

---

## UI

- **Kind**: simple-resource — a row/page modal action on `DealResource` that fires cross-domain to finance.
- **Page**: `CreateInvoiceAction` on the Deal view page at `/crm/deals`.
- **Layout**: modal confirming the invoice draft (deal line items preview, due date from company terms); confirm button dispatches to Finance.
- **Key interactions**: manual action → confirm modal → Finance creates a draft invoice (deep-link to `/finance/invoices` on success). Also fires automatically via `DealWon` on close.
- **States**: empty (deal has no products → warn) · loading (creating stub) · error (finance module inactive → action hidden, not errored) · selected (n/a).
- **Gating**: `crm.deals.update` + `BillingService::hasModule('finance.invoicing')`.

## Data

- Owns / writes: `crm_deals`, `crm_deal_products` (read to populate line items).
- Reads: own `crm_deal_products`; company default payment terms.
- Cross-domain writes: NONE — Finance's `CreateInvoiceStubListener` (or command API) writes `finance_invoices`; deals never write finance tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `DealWon` → finance.invoicing `CreateInvoiceStubListener` creates the draft invoice; manual `CreateInvoiceAction` triggers the same finance path on demand.
- Shared entity: invoice records owned by finance.invoicing; line items sourced from this module's `crm_deal_products`.

## Test Checklist

### Unit
- [ ] Line-item preview maps `crm_deal_products` → invoice-stub lines; empty-products deal surfaces a warning

### Feature (Pest)
- [ ] `CreateInvoiceAction` creates exactly one Finance draft invoice (not auto-sent) via the owning finance path
- [ ] Action hidden when `finance.invoicing` inactive; `DealWon` fires unconsumed with no error
- [ ] Tenant isolation: action on a company A deal never writes a company B invoice

### Livewire
- [ ] Action visible only when `status=won` && `hasModule('finance.invoicing')`; denied without `crm.deals.update`
