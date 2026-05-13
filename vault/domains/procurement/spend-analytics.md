---
type: module
domain: Procurement & Spend Management
panel: procurement
module-key: procurement.analytics
status: planned
color: "#4ADE80"
---

# Spend Analytics

> Read-only spend analytics — total spend by category, supplier, department, and period with savings tracking.

**Panel:** `procurement`
**Module key:** `procurement.analytics`

---

## What It Does

Spend Analytics aggregates all procurement data — purchase orders, GRNs, and supplier invoices — into a read-only intelligence layer for procurement and finance leaders. The module breaks down total spend by category, supplier, department, cost centre, and time period, enabling data-driven sourcing decisions. It also tracks savings realised from negotiated price reductions and consolidation actions against a savings target, giving procurement teams a measurable return on their activity.

---

## Features

### Core
- Spend by category: total spend broken down by spend category for the selected period
- Spend by supplier: top suppliers by spend volume with trend vs prior period
- Spend by department: spend allocated to each cost centre or department
- Period analysis: month, quarter, and year views with prior period comparison
- Maverick spend detection: identify spend that bypassed the approved requisition and PO process
- Export: export spend data tables to CSV or Excel

### Advanced
- Spend concentration: identify supplier or category concentration risk (e.g. >30% with one supplier)
- Savings tracking: log negotiated savings against a category or supplier and track actuals vs target
- Preferred supplier adoption: percentage of spend going through preferred vs non-preferred suppliers
- Contract coverage: percentage of spend covered by active contracts vs ad-hoc purchases
- Tail spend analysis: the long tail of low-value, infrequent suppliers as consolidation candidates

### AI-Powered
- Savings opportunity identification: AI highlights spend categories with above-benchmark unit prices
- Consolidation recommendation: suggest suppliers to consolidate based on category overlap and spend volume
- Anomalous spend detection: flag transactions significantly above the category average

---

## Data Model

```erDiagram
    spend_analytics_snapshots {
        ulid id PK
        ulid company_id FK
        string dimension_type
        string dimension_value
        date period_start
        date period_end
        decimal total_spend
        decimal po_covered_spend
        decimal preferred_supplier_spend
        integer transaction_count
        timestamps created_at_updated_at
    }

    savings_records {
        ulid id PK
        ulid company_id FK
        string category
        ulid supplier_id FK
        decimal target_savings
        decimal actual_savings
        string savings_type
        date period_start
        date period_end
        timestamps created_at_updated_at
    }
```

| Table | Purpose | Key Columns |
|---|---|---|
| `spend_analytics_snapshots` | Aggregated spend data | `id`, `company_id`, `dimension_type`, `dimension_value`, `period_start`, `total_spend` |
| `savings_records` | Savings tracking | `id`, `company_id`, `category`, `supplier_id`, `target_savings`, `actual_savings` |

---

## Permissions

```
procurement.analytics.view
procurement.analytics.view-all-departments
procurement.analytics.view-savings
procurement.analytics.export
procurement.analytics.manage-savings-records
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `SpendDashboardPage`, `SpendByCategoryPage`, `SupplierSpendPage`, `SavingsTrackerPage`
- **Widgets:** `TotalSpendWidget`, `TopSuppliersWidget`, `SavingsWidget`, `MaverickSpendWidget`
- **Nav group:** Analytics

---

## Displaces

| Feature | FlowFlex | Coupa | Jaggaer | Sievo |
|---|---|---|---|---|
| Spend by category/supplier | Yes | Yes | Yes | Yes |
| Maverick spend detection | Yes | Yes | Yes | No |
| Savings tracking | Yes | Yes | Yes | Yes |
| AI savings opportunity | Yes | No | No | Yes |
| Included in platform | Yes | No | No | No |

---

## Related

- [[purchase-orders]] — PO data is the primary spend source
- [[goods-received-notes]] — confirmed receipt validates spend commitment
- [[supplier-catalog]] — preferred supplier flag used in adoption metrics
- [[fpa/budgets]] — spend analytics compared against FPA department budgets
