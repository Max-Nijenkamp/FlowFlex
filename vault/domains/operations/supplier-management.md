---
type: module
domain: Operations
panel: operations
module-key: operations.suppliers
status: planned
color: "#4ADE80"
---

# Supplier Management

> Maintain supplier records, track performance ratings, store contract terms, and manage qualification assessments in one place.

**Panel:** `operations`
**Module key:** `operations.suppliers`

## What It Does

Supplier Management is the master record for every supplier the company works with. Each supplier profile stores contact details, payment terms, lead times, and product catalogue prices used to auto-fill purchase orders. Performance data flows in automatically from the quality module (inspection pass rate, NCR count) and the purchase orders module (on-time delivery rate, invoice accuracy). Procurement managers can run structured assessments to qualify new suppliers or re-evaluate existing ones.

## Features

### Core
- Supplier record: company name, contact persons, address, payment terms, currency, lead time days
- Supplier status: active, preferred, conditional, disqualified, prospect
- Product catalogue: list of products and agreed unit prices per supplier; used to auto-populate PO line items
- Document storage: supplier contracts, certificates (ISO, CE, quality), and insurance documents with expiry tracking
- Expiry alerts: notification when certificates or contracts are approaching renewal date
- Supplier contacts: multiple contacts per supplier (account manager, logistics, finance)

### Advanced
- Performance scorecard: auto-computed from on-time delivery rate, invoice accuracy, inspection pass rate, NCR count
- Performance trend: monthly scorecard history to track improvement or decline
- Supplier assessment: structured qualification questionnaire (financial stability, capacity, quality processes, ESG)
- Assessment workflow: draft → submitted by supplier → reviewed by procurement → approved/rejected
- Preferred supplier lists: tag preferred suppliers per product category for buyer guidance
- Spend analysis: total spend per supplier over any date range; top 10 suppliers by spend

### AI-Powered
- Risk flag: highlight suppliers with declining performance trends, expiring certificates, or high NCR frequency
- Alternative supplier suggestion: when a supplier is disqualified, suggest alternatives from the active supplier pool

## Data Model

```erDiagram
    ops_suppliers {
        ulid id PK
        ulid company_id FK
        string name
        string status
        string payment_terms
        string currency
        integer lead_time_days
        decimal performance_score
        timestamps timestamps
        softDeletes deleted_at
    }

    ops_supplier_contacts {
        ulid id PK
        ulid supplier_id FK
        string name
        string role
        string email
        string phone
        boolean is_primary
        timestamps timestamps
    }

    ops_supplier_catalogue {
        ulid id PK
        ulid supplier_id FK
        ulid product_id FK
        string supplier_sku
        decimal unit_price
        string currency
        integer min_order_qty
        integer lead_time_days
        date price_valid_until
        timestamps timestamps
    }

    ops_supplier_assessments {
        ulid id PK
        ulid supplier_id FK
        string assessment_type
        string status
        json responses
        integer score
        ulid reviewed_by FK
        timestamp completed_at
        timestamps timestamps
    }

    ops_suppliers ||--o{ ops_supplier_contacts : "has"
    ops_suppliers ||--o{ ops_supplier_catalogue : "offers"
    ops_suppliers ||--o{ ops_supplier_assessments : "assessed by"
```

| Table | Purpose |
|---|---|
| `ops_suppliers` | Supplier master records |
| `ops_supplier_contacts` | Contact persons per supplier |
| `ops_supplier_catalogue` | Agreed prices and lead times per product |
| `ops_supplier_assessments` | Qualification and re-assessment records |

## Permissions

```
operations.suppliers.view-any
operations.suppliers.create
operations.suppliers.update
operations.suppliers.manage-catalogue
operations.suppliers.manage-assessments
```

## Filament

**Resource class:** `SupplierResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `SupplierPerformancePage` (scorecard history and KPIs), `AssessmentWorkflowPage`
**Widgets:** `SupplierAlertWidget` (expiring certificates, declining scores)
**Nav group:** Inventory

## Displaces

| Competitor | Feature Replaced |
|---|---|
| SAP Ariba Supplier Management | Supplier qualification and performance |
| Jaggaer | Supplier assessment and scorecard |
| Tradogram | Supplier catalogues and PO pricing |
| Gatekeeper | Supplier contract and risk management |

## Related

- [[purchase-orders]] — catalogue prices auto-fill PO lines
- [[quality-control]] — NCR data feeds performance scorecard
- [[inventory]] — supplier SKUs linked to internal product records
- [[../legal/contracts]] — supplier contracts stored and linked
