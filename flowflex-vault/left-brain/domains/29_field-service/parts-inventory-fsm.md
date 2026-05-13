---
type: module
domain: Field Service Management
panel: fsm
module: Parts & Inventory (Field)
phase: 5
status: complete
cssclasses: domain-fsm
migration_range: 1052500–1052999
last_updated: 2026-05-12
---

# Parts & Inventory (Field)

Manage parts carried in technician vans, track usage per job, trigger replenishment from Operations warehouse, and maintain a parts catalogue with sale prices. Separate from Operations internal inventory.

---

## Key Tables

```sql
-- Parts catalogue (shared with Operations / Ecommerce product catalogue)
CREATE TABLE fsm_parts (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    product_id      ULID NULL,              -- links to ec_products if also sold online
    sku             VARCHAR(100) UNIQUE,
    name            VARCHAR(255),
    description     TEXT NULL,
    unit            VARCHAR(20) DEFAULT 'each',
    cost_price      DECIMAL(12,2) NULL,
    sale_price      DECIMAL(12,2) NULL,
    tax_rate        DECIMAL(5,2) DEFAULT 21,
    is_active       BOOLEAN DEFAULT TRUE
);

-- Per-technician stock levels (van inventory)
CREATE TABLE fsm_technician_stock (
    id              ULID PRIMARY KEY,
    technician_id   ULID NOT NULL REFERENCES fsm_technicians(id),
    part_id         ULID NOT NULL REFERENCES fsm_parts(id),
    quantity        INT DEFAULT 0,
    min_quantity    INT DEFAULT 0,          -- reorder point
    updated_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(technician_id, part_id)
);

-- Parts used on a job
CREATE TABLE fsm_job_parts_used (
    id              ULID PRIMARY KEY,
    job_id          ULID NOT NULL REFERENCES fsm_jobs(id),
    technician_id   ULID NOT NULL REFERENCES fsm_technicians(id),
    part_id         ULID NOT NULL REFERENCES fsm_parts(id),
    quantity        DECIMAL(8,2) NOT NULL,
    unit_price      DECIMAL(12,2) NOT NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);

-- Replenishment requests from van to warehouse
CREATE TABLE fsm_parts_replenishment_requests (
    id              ULID PRIMARY KEY,
    technician_id   ULID NOT NULL REFERENCES fsm_technicians(id),
    status          ENUM('pending','approved','dispatched','received','cancelled'),
    requested_at    TIMESTAMP DEFAULT NOW(),
    fulfilled_at    TIMESTAMP NULL
);

CREATE TABLE fsm_replenishment_items (
    id                  ULID PRIMARY KEY,
    replenishment_id    ULID NOT NULL REFERENCES fsm_parts_replenishment_requests(id),
    part_id             ULID NOT NULL REFERENCES fsm_parts(id),
    quantity_requested  INT,
    quantity_fulfilled  INT NULL
);
```

---

## Van Stock Management

Technician opens app → Parts → My Van Stock.  
Shows all parts with current quantity, min quantity, reorder badge when below min.  
On job: select parts used → deducts from van stock → creates `fsm_job_parts_used`.

---

## Replenishment Flow

1. Van stock drops below `min_quantity` → auto-flag or technician manually requests
2. Replenishment request sent to warehouse manager (Operations domain)
3. Warehouse picks, packs, ships to technician's base / next morning drop
4. Technician confirms receipt → van stock updated

---

## Integration with Operations

`PartOutOfStock` event → Operations `[[warehouse-management]]` (if Operations domain active).  
Fall-through: email notification to purchasing if no warehouse module.

---

## Related

- [[MOC_FieldService]]
- [[job-dispatch-scheduling]]
- [[field-invoicing]]
- [[MOC_Operations]]
