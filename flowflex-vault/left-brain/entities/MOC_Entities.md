---
type: moc
section: left-brain/entities
last_updated: 2026-05-08
---

# Entities — Map of Content

Core data models. These are the anchor records that multiple domains reference and extend.

---

## Master ERD

```mermaid
erDiagram
    companies ||--o{ users : "has many"
    companies ||--o{ employees : "has many"
    companies ||--o{ contacts : "has many"
    companies ||--o{ projects : "has many"
    companies ||--o{ invoices : "has many"
    companies ||--o{ products : "has many"
    companies ||--o{ module_subscriptions : "subscribes to"

    users ||--o{ employees : "may be linked to"
    contacts }o--|| companies : "belongs to (CRM company)"
    invoices }o--|| contacts : "billed to"
    projects }o--o{ contacts : "client"
```

---

## Entity Index

| Entity | Table | Domain | Purpose |
|---|---|---|---|
| [[entity-company]] | `companies` | Core Platform | Tenant anchor. Every row on every table references this. |
| [[entity-user]] | `users` | Core Platform | Platform user — admin panel access, authentication. |
| [[entity-employee]] | `employees` | HR & People | Employed person. Source of truth for HR data. |
| [[entity-contact]] | `contacts` | CRM & Sales | External person (customer, prospect, lead). |
| [[entity-project]] | `projects` | Projects & Work | Work container — tasks, time, documents live here. |
| [[entity-invoice]] | `invoices` | Finance | Financial document — sale, service, or subscription. |
| [[entity-product]] | `products` | E-commerce / Operations | Sellable or physical item. Used by ecommerce + inventory. |
| [[entity-admin]] | `admins` | Foundation | FlowFlex internal staff — Layer 1 RBAC, `/admin` panel only. |
| [[entity-module-subscription]] | `company_module_subscriptions` | Core Platform | Which modules a company has enabled. |
| [[entity-module-catalog]] | `module_catalog` | Core Platform | Master pricing table — per-user monthly price per module key. |

---

## Unified Record Principle

```mermaid
graph TD
    USER["User (auth identity)"]
    EMP["Employee (HR record)"]
    CONTACT["Contact (CRM record)"]

    USER -->|"may be linked to"| EMP
    USER -->|"may be linked to"| CONTACT
    EMP -.->|"shared email"| CONTACT

    note["One physical person can have:\n- a User record (platform access)\n- an Employee record (HR profile)\n- a Contact record (customer history)\nAll three linked via email + company_id"]
```

A person is not duplicated — records are linked. The `users` table controls auth, `employees` controls HR, `contacts` controls CRM.

---

## Cross-Entity Rules

1. `company_id` is on every entity — the multi-tenancy anchor
2. ULID primary keys on all entities
3. Soft deletes on all entities
4. `LogsActivity` trait on all entities
5. Deleting a `Company` cascades soft-deletes to all child records

---

## Related

- [[00_MOC_LeftBrain]]
- [[multi-tenancy]]
- [[data-architecture]]
- [[concept-ulid-keys]]
