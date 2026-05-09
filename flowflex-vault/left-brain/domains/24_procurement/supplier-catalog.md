---
type: module
domain: Procurement & Spend Management
panel: procurement
phase: 3
status: planned
cssclasses: domain-procurement
migration_range: 982000–982499
last_updated: 2026-05-09
---

# Supplier Catalog

Pre-approved products and services at negotiated prices. Staff browse and order directly without manual PO creation for every item — drives compliance and prevents maverick spend.

---

## How It Works

Procurement team negotiates prices with preferred suppliers, then publishes those items to the internal catalog. Staff search the catalog, add items to a cart, and submit — the system auto-generates a requisition or PO.

### Catalog Item Types
- **Physical goods**: office supplies, IT hardware, consumables
- **Software licenses**: predefined SKUs with volume pricing
- **Services**: standard consulting day rates, cleaning, maintenance contracts
- **Framework agreements**: items covered by blanket PO (no new PO per order)

---

## Catalog Management

Procurement admin:
- Add/edit/deactivate catalog items
- Set per-item approval thresholds (items under €50 auto-approve)
- Link items to preferred supplier contracts
- Set category + cost centre tagging (pre-fills requisition form)
- Publish/unpublish items (seasonal, stock-dependent)

Suppliers can self-manage catalog items via supplier portal (with procurement approval before publish).

---

## Order Flow

```
Staff browse catalog → Add to cart → Checkout (cost centre + justification)
→ Under threshold: auto-approve → PO generated
→ Over threshold: routes to standard requisition approval workflow
→ PO sent to supplier → GRN on delivery
```

---

## Punch-Out Integration

For large suppliers (e.g., Amazon Business, Staples, Dell):
- Staff "punch out" to supplier's website via SSO
- Browse supplier's full catalog in their native UI
- Add items to cart → return cart to FlowFlex
- FlowFlex creates requisition/PO from returned cart data
- Supports cXML punch-out standard

---

## Data Model

### `proc_catalog_items`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| supplier_id | ulid | FK |
| name | varchar(300) | |
| description | text | nullable |
| sku | varchar(100) | nullable |
| unit_price | decimal(14,4) | |
| currency | char(3) | |
| unit | varchar(50) | "each", "day", "licence" |
| category | varchar(100) | |
| auto_approve_under | decimal(14,2) | nullable |
| is_active | boolean | |
| punchout_url | varchar(500) | nullable |

### `proc_catalog_orders`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| ordered_by | ulid | FK `employees` |
| requisition_id | ulid | nullable FK |
| po_id | ulid | nullable FK |
| status | enum | draft/submitted/approved/ordered |
| total | decimal(14,2) | |

---

## Migration

```
982000_create_proc_catalog_items_table
982001_create_proc_catalog_orders_table
982002_create_proc_catalog_order_lines_table
```

---

## Related

- [[MOC_Procurement]]
- [[purchase-requisitions]]
- [[purchase-orders]]
- [[spend-analytics]]
