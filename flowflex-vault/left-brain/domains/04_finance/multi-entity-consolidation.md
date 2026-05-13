---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 6
status: complete
migration_range: 200000–249999
last_updated: 2026-05-12
---

# Multi-Entity & Financial Consolidation

Manage multiple legal entities (subsidiaries, holding companies, international branches) under one FlowFlex account. Consolidated P&L, balance sheet, and intercompany elimination. Replaces Sage Intacct and NetSuite for multi-entity SMEs.

---

## Features

### Entity Management
- Multiple legal entities per FlowFlex account (parent + children)
- Each entity has own chart of accounts, currency, tax settings
- Shared employees / contacts across entities (or entity-isolated)
- Per-entity Filament panel or unified multi-entity view

### Intercompany Transactions
- Intercompany invoice creation (entity A bills entity B)
- Automatic reciprocal entry creation
- Intercompany loan tracking (with interest)
- Transfer pricing documentation

### Consolidation
- Consolidated P&L report (all entities, same currency)
- Consolidated balance sheet
- Minority interest handling
- Intercompany elimination engine (removes double-counting)
- Entity-level vs consolidated drill-down

### Multi-Currency
- Each entity in its own functional currency
- Consolidated reports in parent entity currency
- FX translation at average rate (P&L) and closing rate (balance sheet)
- Translation reserve / OCI tracking

### Reporting
- Consolidation report builder (select which entities)
- Management vs statutory view
- Export to Excel for auditors
- Audit trail on all consolidation adjustments

---

## Data Model

```erDiagram
    entity_groups {
        ulid id PK
        ulid master_company_id FK
        string name
        string reporting_currency
    }

    entity_group_members {
        ulid id PK
        ulid group_id FK
        ulid company_id FK
        decimal ownership_percent
        string role
    }

    intercompany_transactions {
        ulid id PK
        ulid from_company_id FK
        ulid to_company_id FK
        ulid from_invoice_id FK
        ulid to_invoice_id FK
        decimal amount
        string currency
        string status
    }
```

---

## Permissions

```
finance.entities.manage
finance.consolidation.view
finance.consolidation.run
finance.intercompany.create
```

---

## Competitors Displaced

Sage Intacct · NetSuite (multi-entity) · Xero HQ · Silverfin

---

## Related

- [[MOC_Finance]]
- [[entity-company]]
