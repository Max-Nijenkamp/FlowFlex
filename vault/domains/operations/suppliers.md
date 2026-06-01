---
type: module
domain: Operations
panel: operations
module-key: operations.suppliers
status: planned
color: "#4ADE80"
---

# Suppliers

Supplier/vendor records with contact details, payment terms, and supplied items catalogue.

## Core Features

- Supplier record: name, contact person, email, phone, address, payment terms, currency
- Supplied items: which inventory items this supplier provides, at what cost, lead time
- Supplier performance: on-time delivery rate, order history
- Preferred supplier flagging per item
- Supplier contact log
- Phone validation via `propaganistas/laravel-phone`

## Data Model

| Table | Key Columns |
|---|---|
| `ops_suppliers` | company_id, name, contact_name, email, phone, address, payment_terms_days, currency, is_active |
| `ops_supplier_items` | supplier_id, item_id, company_id, supplier_sku, cost_cents, lead_time_days, is_preferred |

## Filament

**Nav group:** Purchasing

- `SupplierResource` — list, create, edit; supplied items inline
- Supplier order history on view page

## Related

- [[domains/operations/purchase-orders]]
- [[domains/operations/inventory]]
- [[domains/finance/accounts-payable]]
