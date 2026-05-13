---
type: module
domain: Procurement & Spend Management
panel: procurement
module-key: procurement.catalog
status: planned
color: "#4ADE80"
---

# Supplier Catalog

> Approved supplier catalog — companies, contact details, products/services offered, pricing, lead times, and preferred supplier designation.

**Panel:** `procurement`
**Module key:** `procurement.catalog`

---

## What It Does

Supplier Catalog is the master register of approved suppliers. Rather than each department independently finding and paying vendors, procurement teams vet suppliers and add them to the catalog with agreed pricing, standard lead times, and payment terms. When employees raise a requisition, they search the catalog to select from pre-approved suppliers. The catalog also links to ESG assessment results for each supplier, providing a sustainability dimension to supplier selection.

---

## Features

### Core
- Supplier record: company name, registration number, address, primary contact, payment terms, currency
- Product/service lines: items or service categories offered by each supplier with agreed pricing and lead time
- Preferred supplier designation: flag suppliers as preferred for specific categories
- Contact management: multiple contacts per supplier (commercial, logistics, finance)
- Document storage: supplier contracts, NDAs, and insurance certificates attached to the supplier record
- Supplier status: approved, under review, suspended, blacklisted

### Advanced
- Approved supplier list (ASL): the definitive list used to validate supplier choices on requisitions
- Supplier performance rating: manual rating by procurement team based on delivery, quality, and service
- Category mapping: tag suppliers to spend categories for analytics and sourcing strategy
- Spend history: total spend with each supplier over a configurable period
- Supplier portal invitation: invite a supplier to update their own contact and product details

### AI-Powered
- Supplier risk monitoring: AI monitors supplier financial health signals and flags risks
- Duplicate supplier detection: flag when a new supplier record appears to be a duplicate of an existing one
- Category sourcing suggestions: for a given spend category, recommend under-utilised suppliers in the catalog

---

## Data Model

```erDiagram
    suppliers {
        ulid id PK
        ulid company_id FK
        string name
        string registration_number
        string address
        string payment_terms
        string currency
        string status
        boolean is_preferred
        timestamps created_at_updated_at
    }

    supplier_products {
        ulid id PK
        ulid supplier_id FK
        string name
        string category
        decimal agreed_price
        string price_unit
        integer lead_time_days
        timestamps created_at_updated_at
    }

    supplier_contacts {
        ulid id PK
        ulid supplier_id FK
        string name
        string role
        string email
        string phone
    }

    suppliers ||--o{ supplier_products : "offers"
    suppliers ||--o{ supplier_contacts : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `suppliers` | Supplier master records | `id`, `company_id`, `name`, `payment_terms`, `status`, `is_preferred` |
| `supplier_products` | Product/service catalog | `id`, `supplier_id`, `name`, `category`, `agreed_price`, `lead_time_days` |
| `supplier_contacts` | Supplier contacts | `id`, `supplier_id`, `name`, `role`, `email` |

---

## Permissions

```
procurement.catalog.view
procurement.catalog.create
procurement.catalog.update
procurement.catalog.delete
procurement.catalog.manage-preferred
```

---

## Filament

- **Resource:** `App\Filament\Procurement\Resources\SupplierResource`
- **Pages:** `ListSuppliers`, `CreateSupplier`, `EditSupplier`, `ViewSupplier`
- **Custom pages:** `SupplierSpendPage`, `SupplierPerformancePage`
- **Widgets:** `TopSuppliersWidget`, `SuspendedSuppliersWidget`
- **Nav group:** Suppliers

---

## Displaces

| Feature | FlowFlex | Coupa | SAP Ariba | Procurify |
|---|---|---|---|---|
| Approved supplier register | Yes | Yes | Yes | Yes |
| Product/service catalog | Yes | Yes | Yes | No |
| ESG assessment linkage | Yes | No | No | No |
| AI duplicate detection | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[purchase-requisitions]] — employees select suppliers from the catalog
- [[purchase-orders]] — POs reference supplier catalog records
- [[goods-received-notes]] — supplier on GRN linked from catalog
- [[esg/supply-chain]] — ESG assessments linked to supplier records
- [[spend-analytics]] — spend analytics broken down by supplier catalog data
