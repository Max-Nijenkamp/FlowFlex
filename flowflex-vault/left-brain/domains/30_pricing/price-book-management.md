---
type: module
domain: Pricing Management
panel: pricing
module: Price Book Management
phase: 4
status: complete
cssclasses: domain-pricing
migration_range: 1100000–1100499
last_updated: 2026-05-12
---

# Price Book Management

Multiple price lists per company — segment-based, region-based, currency-based, or customer-specific. Single source of truth for all product and service prices across CRM quotes, E-commerce storefront, and field invoices.

---

## Key Tables

```sql
CREATE TABLE pricing_price_books (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100) NOT NULL,
    code            VARCHAR(20) UNIQUE,
    currency        CHAR(3) DEFAULT 'EUR',
    is_default      BOOLEAN DEFAULT FALSE,
    type            ENUM('standard','segment','volume','promotional','customer_specific'),
    valid_from      DATE NULL,
    valid_to        DATE NULL,
    status          ENUM('active','inactive','draft'),
    description     TEXT NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE pricing_price_book_entries (
    id              ULID PRIMARY KEY,
    price_book_id   ULID NOT NULL REFERENCES pricing_price_books(id),
    product_id      ULID NOT NULL,       -- ref to ec_products or service catalogue
    product_type    ENUM('product','service','bundle'),
    list_price      DECIMAL(12,2) NOT NULL,
    min_price       DECIMAL(12,2) NULL,  -- floor price — discounts cannot go below this
    cost_price      DECIMAL(12,2) NULL,
    markup_pct      DECIMAL(5,2) NULL,   -- calculated: (list - cost) / cost * 100
    margin_pct      DECIMAL(5,2) NULL,   -- calculated: (list - cost) / list * 100
    active          BOOLEAN DEFAULT TRUE,
    effective_from  DATE NULL,
    effective_to    DATE NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(price_book_id, product_id)
);

CREATE TABLE pricing_volume_tiers (
    id              ULID PRIMARY KEY,
    entry_id        ULID NOT NULL REFERENCES pricing_price_book_entries(id),
    min_qty         INT NOT NULL,
    max_qty         INT NULL,           -- NULL = unlimited
    unit_price      DECIMAL(12,2) NOT NULL,
    discount_pct    DECIMAL(5,2) NULL
);

-- Assign specific price books to customers / segments
CREATE TABLE pricing_customer_price_books (
    id              ULID PRIMARY KEY,
    price_book_id   ULID NOT NULL REFERENCES pricing_price_books(id),
    customer_id     ULID NULL REFERENCES contacts(id),
    segment         VARCHAR(100) NULL,  -- OR segment (e.g. 'enterprise', 'reseller')
    company_id      ULID NULL REFERENCES companies(id),
    valid_from      DATE NULL,
    valid_to        DATE NULL
);
```

---

## Price Resolution Logic

When a quote or order is created, price lookup order:
1. Customer-specific price book (if assigned to this contact)
2. Segment price book (if customer segment matches)
3. Currency price book (if customer currency ≠ default)
4. Default price book

Volume tier lookup: find tier where `min_qty <= ordered_qty <= max_qty`.  
`min_price` enforced as absolute floor — discount workflows cannot approve below floor.

---

## Import / Export

Bulk import via CSV: `product_sku, list_price, min_price, cost_price`.  
Export price book to PDF or Excel for offline quoting / broker use.

---

## Related

- [[MOC_Pricing]]
- [[discount-approval-workflows]]
- [[MOC_CRM]] — CPQ quotes reference price books
- [[MOC_Ecommerce]] — storefront prices sync from default price book
