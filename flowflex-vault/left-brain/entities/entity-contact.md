---
type: entity
domain: CRM & Sales
table: crm_contacts
primary_key: ulid
soft_deletes: true
last_updated: 2026-05-08
---

# Entity: Contact

An external person in the CRM — customer, prospect, lead, or partner. Source of truth for all customer-facing interactions.

**Table:** `crm_contacts`  
**Multi-Tenant:** Yes — `company_id`.

---

## Schema

```erDiagram
    crm_contacts {
        ulid id PK
        ulid company_id FK
        ulid crm_company_id FK "nullable — CRM company account"
        string first_name
        string last_name
        string email
        string phone
        string job_title
        string status
        string lead_source
        string lifecycle_stage
        decimal lifetime_value
        json custom_fields
        timestamp last_contacted_at
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    companies ||--o{ crm_contacts : "owns"
    crm_companies ||--o{ crm_contacts : "has contacts"
```

---

## Key Columns

| Column | Type | Notes |
|---|---|---|
| `crm_company_id` | ULID FK nullable | Links to CRM company (B2B account) |
| `status` | enum | `lead`, `prospect`, `customer`, `churned`, `partner` |
| `lifecycle_stage` | enum | `awareness`, `consideration`, `decision`, `onboarding`, `retention` |
| `lead_source` | string | `website`, `referral`, `ad`, `event`, `cold_outreach`, etc. |
| `lifetime_value` | decimal(12,2) | Sum of all paid invoices |
| `custom_fields` | JSON | Company-defined extra fields |

---

## Relationships

| Relationship | Type | Description |
|---|---|---|
| `company()` | belongsTo | Tenant |
| `crmCompany()` | belongsTo | B2B account this contact belongs to |
| `deals()` | hasMany | Sales pipeline deals |
| `tickets()` | hasMany | Support tickets |
| `invoices()` | hasMany | Financial invoices |
| `emailThreads()` | hasMany | Email conversations |
| `activities()` | hasMany | Calls, meetings, notes log |

---

## Auto-Creation

Contacts are auto-created from:
- Form submission (`FormSubmissionReceived` event)
- Checkout completion (`CheckoutCompleted` event)
- Email reply to shared inbox (new sender = new contact)
- Marketing event registration
- Manual import (CSV)

Deduplication: check `email` + `company_id` before creating.

---

## Related

- [[MOC_Entities]]
- [[entity-company]]
- [[entity-invoice]]
- [[MOC_CRM]]
