---
type: entity
domain: E-commerce & Operations
table: products
primary_key: ulid
soft_deletes: true
last_updated: 2026-05-08
---

# Entity: Product

A sellable or physical item. Used by E-commerce (catalogue, orders), Operations (inventory), and Finance (purchase lines).

**Table:** `products`  
**Multi-Tenant:** Yes — `company_id`.

---

## Schema

```erDiagram
    products {
        ulid id PK
        ulid company_id FK
        string sku
        string name
        string type
        string status
        decimal base_price
        string currency
        decimal cost_price
        integer stock_quantity
        integer reorder_point
        boolean track_inventory
        json attributes
        json seo
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    products ||--o{ product_variants : "has variants"
    products ||--o{ stock_movements : "tracked via"
    products ||--o{ order_lines : "sold on"
```

---

## Key Columns

| Column | Type | Notes |
|---|---|---|
| `sku` | string | Stock keeping unit, unique per company |
| `type` | enum | `physical`, `digital`, `service`, `subscription` |
| `status` | enum | `active`, `draft`, `archived` |
| `base_price` | decimal(12,2) | Default price |
| `cost_price` | decimal(12,2) | COGS — used for margin calculation |
| `stock_quantity` | integer | Current available quantity |
| `reorder_point` | integer | Trigger for `StockBelowReorderPoint` event |
| `track_inventory` | boolean | False for services/digital |
| `attributes` | JSON | Variant attributes schema (e.g. `{color: [], size: []}`) |
| `seo` | JSON | `{title, description, slug}` for storefront |

---

## Relationships

| Relationship | Type | Description |
|---|---|---|
| `company()` | belongsTo | Tenant |
| `variants()` | hasMany | Colour/size variants with own stock |
| `stockMovements()` | hasMany | All stock in/out records |
| `media()` | morphMany | Product images via Spatie Media Library |
| `categories()` | belongsToMany | Product taxonomy |

---

## Events

- `StockBelowReorderPoint` → Operations (create draft purchase order)
- `StockReachedZero` → Notifications, E-commerce (mark out-of-stock)

---

## Business Rules

1. `sku` unique per company
2. `stock_quantity` is derived from `stock_movements` sum (or cached column, kept in sync by service)
3. `type = service` → `track_inventory = false` always
4. Digital products don't decrement stock

---

## Related

- [[MOC_Entities]]
- [[entity-company]]
- [[MOC_Ecommerce]]
- [[MOC_Operations]]
