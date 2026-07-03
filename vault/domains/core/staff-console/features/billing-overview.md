---
domain: core
module: staff-console
feature: billing-overview
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Billing Overview

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

Cross-company invoice visibility for FlowFlex staff — every invoice across all tenants with status filters, plus a per-company invoice relation. Core Feature 4. Read-only over billing data owned by [[../../billing-engine/_module]].

## UI

- **Kind**: simple-resource — `BillingInvoiceResource` (read-only list) plus `InvoicesRelationManager` under `CompanyResource`, in the `/admin` panel.
- **Page**: `ListBillingInvoices` under `BillingInvoiceResource` (cross-company), and the `InvoicesRelationManager` tab on `EditCompany` (per-company). Routes: Filament resource index for `BillingInvoiceResource`; relation-manager tab under `CompanyResource`.
- **Layout**: a read-only invoice table (company, amount, status, dates) with status filters (paid / open / past-due); the per-company variant scopes to one company's invoices.
- **Key interactions**: staff filters invoices by status → inspects a row (read-only) → or opens a company → Invoices tab for that company's invoices. No mutation from the console.
- **States**: empty (no invoices yet) · loading (table query) · error (query failure → notification) · selected (open invoice row / per-company tab).
- **Gating**: admin guard only — `canAccess() = auth('admin')->check()`. Read-only; cross-tenant, staff-only.

## Data

- Owns / writes: **no tables of its own, and no writes at all** — this feature is strictly read-only.
- Reads: `billing_invoices` cross-company (native, `CompanyScope` no-ops for admin), read-only. Table **owned by [[../../billing-engine/_module]]**. Note `companies` carries an encrypted `stripe_customer_id` (billing-owned) — surfaced but not decrypted/edited here.
- Cross-domain writes: none — read-only surface; effects other domains only via events (none) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events). Invoice state is produced by [[../../billing-engine/_module]] (which may consume Stripe webhooks); staff-console only reads the resulting rows.
- Feeds: none.
- Shared entity: `billing_invoices` (owned by [[../../billing-engine/_module]]) — read-only reference here.

## Test Checklist

### Unit
- [ ] Status filter maps to the correct invoice states (paid / open / past-due)

### Feature (Pest)
- [ ] Cross-company list spans every tenant's invoices (admin, `CompanyScope` no-ops); per-company relation manager scopes to one company
- [ ] Resource is read-only — no create/edit/delete route is exposed (`canCreate(): false`)
- [ ] `stripe_customer_id` is surfaced without being decrypted/edited here

### Livewire
- [ ] `ListBillingInvoices` denied to a non-admin; admin sees the filtered table
- [ ] No mutation action is present on the invoice rows

## Related

- [[../_module]] · [[../architecture]] · [[../security]] · [[platform-dashboard]]
- [[../../billing-engine/_module]] · [[../../billing-engine/data-model]] · [[../../../../security/data-ownership]]
