---
type: moc
domain: Procurement & Spend Management
panel: procurement
phase: 3
color: "#F97316"
cssclasses: domain-procurement
last_updated: 2026-05-09
---

# Procurement & Spend Management — Map of Content

End-to-end procurement: purchase requisitions, purchase orders, supplier catalogs, 3-way matching, and spend analytics. Replaces Coupa, Jaggaer, SAP Ariba, Procurify, and Airbase for mid-market companies.

**Panel:** `procurement`  
**Phase:** 3  
**Migration Range:** `980000–984999`  
**Colour:** Orange `#F97316` / Light: `#FFF7ED`  
**Icon:** `heroicon-o-shopping-cart`

---

## Why This Domain Exists

Every company >20 people needs controlled spending:
- Without procurement: employees buy whatever they want, expenses explode
- With procurement: every purchase starts with a requisition, gets approved, creates a PO, and matches to the invoice before payment

Coupa = enterprise only (€50k+/yr). Procurify = €1,500+/mo. Airbase = VC-funded, expensive. FlowFlex includes procurement, removing the need for a separate tool.

Operations has supplier qualification. Procurement handles the actual buying process — the two work together but are distinct.

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Purchase Requisitions | 3 | planned | Staff request purchases; approval routing by value/category |
| Purchase Orders | 3 | planned | Approved POs sent to suppliers; PO status tracking |
| Supplier Catalog | 3 | planned | Pre-approved products/services at negotiated prices |
| Goods Received Notes (GRN) | 3 | planned | Confirm delivery; triggers 3-way match |
| 3-Way Match & Invoice Approval | 3 | planned | Match PO → GRN → supplier invoice before payment |
| Spend Analytics | 4 | planned | Spend by category, supplier, department, budget vs actual |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `PurchaseOrderRaised` | PO Management | Notifications (supplier), Finance (committed spend) |
| `GoodsReceived` | GRN | 3-Way Match (trigger check), Inventory (if goods) |
| `InvoiceMatchedAndApproved` | 3-Way Match | Finance AP (schedule for payment), Notifications (approver) |
| `SpendBudgetExceeded` | Spend Analytics | Notifications (budget owner, finance) |

---

## Filament Panel Structure

**Navigation Groups:**
- `Requests` — My Requisitions, Team Requests, Pending Approval
- `Orders` — Purchase Orders, Draft POs, PO History
- `Catalog` — Approved Products, Suppliers, Price Lists
- `Receiving` — GRNs, Delivery Confirmation, Discrepancies
- `Invoices` — Match Queue, Approved Invoices, Exceptions
- `Analytics` — Spend Dashboard, Supplier Spend, Category Analysis

---

## 3-Way Match Logic

```
PO quantity/price
  vs GRN quantity received
  vs Supplier invoice quantity/price

All three match within tolerance → auto-approve for payment
Any mismatch → flag for manual review
```

Tolerance thresholds configurable: ±2% price, ±0% quantity (default).

---

## Permissions Prefix

`procurement.requisitions.*` · `procurement.orders.*` · `procurement.catalog.*`  
`procurement.receiving.*` · `procurement.invoices.*`

---

## Competitors Displaced

Coupa · Jaggaer · SAP Ariba · Procurify · Airbase · Spendesk · Pleo (spend side)

---

## Related

- [[MOC_Domains]]
- [[MOC_Finance]] — approved invoices → AP payment
- [[MOC_Operations]] — supplier management, inventory receiving
- [[MOC_Analytics]] — spend analytics feeds BI
