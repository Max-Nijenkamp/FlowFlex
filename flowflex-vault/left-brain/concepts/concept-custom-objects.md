---
type: concept
category: architecture
phase: 1
last_updated: 2026-05-09
---

# Custom Objects

**MUST BE PHASE 1–2.** Custom Objects let admins define their own data entities without writing code — like Salesforce Custom Objects. This is a platform-level architecture feature, not a domain module.

---

## Why This Must Be Phase 1-2

If Custom Objects are added in Phase 5+, all previous domain modules will have already established fixed table structures that admins cannot extend. Adding Custom Objects late means:
- Customers have already built workarounds in spreadsheets
- Migration path from fixed to extensible schema is complex
- Competitors (Salesforce, HubSpot) have this in their lowest tier

Custom Objects are a **retention and expansion driver** — customers who define custom entities become deeply embedded in the platform.

---

## What Custom Objects Enables

Admins can:
1. Define a new object type (e.g. "Equipment", "Grant Application", "Insurance Policy")
2. Add custom fields (text, number, date, boolean, select, file, relation)
3. Set permissions per role
4. View records in Filament admin (auto-generated list + detail views)
5. Link custom objects to built-in entities (Company, Contact, Project, etc.)
6. Use custom object records in workflows, email templates, and reports

---

## Architecture

### Option A: Entity-Attribute-Value (EAV)
```sql
custom_object_types     -- define the object type
custom_object_fields    -- define fields per type
custom_object_records   -- one row per record
custom_object_values    -- one row per field per record
```
**Pros**: totally flexible, easy to add fields  
**Cons**: very hard to query across fields, no type safety, terrible for reporting

### Option B: JSON Column (PostgreSQL JSONB)
```sql
custom_object_types     -- define the object type + field schema
custom_object_records   -- one row per record, `data JSONB`
```
**Pros**: flexible, queryable with JSONB operators, single row per record  
**Cons**: field-level indexing needed, no foreign key constraints on custom fields  
**Recommended for FlowFlex** (MySQL JSON also supported)

### Option C: Dynamic DDL (create actual tables)
Create real MySQL table for each custom object.  
**Pros**: full SQL, proper indexes, joins, foreign keys  
**Cons**: schema migrations in runtime, table explosion, security risks  
**Not recommended**

---

## Recommended Implementation (JSONB approach)

```sql
CREATE TABLE custom_object_types (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    name            VARCHAR(100) NOT NULL,      -- "Equipment"
    plural_name     VARCHAR(100) NOT NULL,      -- "Equipment" (or "Applications")
    icon            VARCHAR(100) NULL,          -- heroicon name
    color           CHAR(7) NULL,
    slug            VARCHAR(100) NOT NULL,      -- "equipment"
    schema          JSON NOT NULL,              -- field definitions
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE custom_object_records (
    id              ULID PRIMARY KEY,
    company_id      ULID NOT NULL REFERENCES companies(id),
    type_id         ULID NOT NULL REFERENCES custom_object_types(id),
    data            JSON NOT NULL DEFAULT '{}',
    -- Standard relations
    related_company_id  ULID NULL REFERENCES companies(id),
    related_contact_id  ULID NULL REFERENCES contacts(id),
    related_project_id  ULID NULL REFERENCES projects(id),
    owner_id            ULID NULL REFERENCES users(id),
    created_by      ULID NOT NULL REFERENCES users(id),
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW(),
    deleted_at      TIMESTAMP NULL
);
```

---

## Field Types

| Type | Storage | Example |
|---|---|---|
| `text` | `data->>"field_key"` | Name, notes |
| `number` | `CAST(data->>"field_key" AS DECIMAL)` | Quantity, value |
| `date` | `DATE(data->>"field_key")` | Due date, warranty |
| `boolean` | `data->>"field_key" = 'true'` | Active, billable |
| `select` | `data->>"field_key"` | Status, category |
| `multi_select` | `JSON_CONTAINS(data->"field_key", ...)` | Tags |
| `relation` | Separate `custom_object_relations` table | Linked entity |
| `file` | Storage path in `data` | Documents |

---

## Filament Auto-Generation

Custom objects get auto-generated Filament resources:
- `CustomObjectListPage` — dynamic table columns from schema
- `CustomObjectCreatePage` — dynamic form from schema
- `CustomObjectViewPage` — dynamic detail view

Schema stored as JSON → Filament `make:filament-resource` generates from JSON schema at runtime (or via cache-compiled PHP).

---

## Phase Priority

**Phase 1–2.** Delay is expensive. Salesforce was built on Custom Objects from day one. HubSpot added them and it became a moat. This is foundational.

---

## Related

- [[concept-formula-engine]] — calculated fields reference custom object fields
- [[concept-workflow-rules]] — custom object events trigger workflows
- [[module-system]]
- [[data-architecture]]
