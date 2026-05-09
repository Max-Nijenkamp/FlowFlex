---
type: concept
category: architecture
phase: 2
last_updated: 2026-05-09
---

# Formula Engine

Calculated fields that derive their value from other fields in the same record, related records, or aggregations. Like Salesforce Formula Fields or Airtable Formulas. Users define formulas in a spreadsheet-like syntax — no code required.

---

## Why This Matters

Without a formula engine:
- Finance cannot auto-calculate margin % on deals
- HR cannot auto-calculate total comp (salary + benefits + equity)
- Projects cannot auto-derive budget utilisation %
- Every derived metric requires hard-coded backend logic

With a formula engine, admins add calculated fields to any entity without developer involvement.

---

## Formula Syntax

Excel/Google Sheets-style:

```
// Simple arithmetic
{unit_price} * {quantity}

// Conditional
IF({status} = "won", {deal_value} * 0.1, 0)

// Cross-object
{contact.company.industry}

// Aggregation
SUM({line_items.amount})
AVG({tasks.estimated_hours})

// Date
DATEDIFF({close_date}, TODAY())
DAYS_UNTIL({renewal_date})

// Text
CONCAT({first_name}, " ", {last_name})
UPPER({company_name})
```

---

## Implementation Architecture

```
User defines formula string
         │
         ▼
    Formula Parser (AST)
         │
    ┌────┴────┐
    │  Types  │  -- field resolution, type checking
    └────┬────┘
         │
    ┌────┴────────────┐
    │ Evaluation Mode │
    ├─────────────────┤
    │ Runtime         │  -- evaluate on record read (cached)
    │ Trigger-based   │  -- re-evaluate on field change
    │ Scheduled       │  -- aggregate formulas, recalc nightly
    └─────────────────┘
```

---

## Key Tables

```sql
CREATE TABLE formula_fields (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    entity_type     VARCHAR(100) NOT NULL,  -- 'deal', 'contact', custom_object type
    field_name      VARCHAR(100) NOT NULL,
    label           VARCHAR(100) NOT NULL,
    formula         TEXT NOT NULL,
    return_type     ENUM('text','number','boolean','date','currency'),
    evaluation_mode ENUM('runtime','trigger','scheduled'),
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(company_id, entity_type, field_name)
);

CREATE TABLE formula_field_cache (
    id              ULID PRIMARY KEY,
    formula_field_id ULID NOT NULL REFERENCES formula_fields(id),
    entity_id       ULID NOT NULL,
    value           TEXT NULL,
    calculated_at   TIMESTAMP DEFAULT NOW(),
    UNIQUE(formula_field_id, entity_id)
);
```

---

## Formula Validation

On save, formula is:
1. Parsed → AST (syntax check)
2. Field references resolved against entity schema
3. Type-checked (cannot multiply text × text)
4. Test-evaluated against a sample record (if available)
5. Circular dependency check

Errors shown inline with field reference highlighted.

---

## Performance Considerations

- `runtime` formulas: evaluated at query time, cached in `formula_field_cache`
- `trigger` formulas: recalculated when dependency fields change (via events)
- `scheduled` formulas (aggregates like SUM): recalculated nightly

Cross-object aggregations (SUM across child records) are expensive — always `scheduled` mode, materialized.

---

## Phase Priority

**Phase 2.** Foundational capability. Custom Objects without formulas lose ~40% of their value. Required before CPQ (phase 3-4) can do auto-calculated line item totals.

---

## Related

- [[concept-custom-objects]] — formula fields extend custom object records
- [[data-architecture]]
