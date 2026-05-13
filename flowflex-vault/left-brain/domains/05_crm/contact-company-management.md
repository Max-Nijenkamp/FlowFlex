---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
status: in-progress
migration_range: 250001–250003
last_updated: 2026-05-11
right_brain_log: "[[builder-log-crm-phase3]]"
---

# Contact & Company Management

Central people and company database for all CRM activity. Tracks leads, prospects, and customers with full interaction history. Replaces HubSpot CRM contacts, Salesforce Accounts & Contacts, Pipedrive.

**Panel:** `crm`  
**Phase:** 3 — foundation for all other CRM modules  
**Module key:** `crm.contacts`

---

## Data Model

```erDiagram
    crm_contacts {
        ulid id PK
        ulid company_id FK
        string first_name
        string last_name
        string email
        string phone
        string mobile
        string job_title
        string source
        string status
        ulid owner_id FK
        ulid crm_company_id FK
        text notes
        json custom_fields
        timestamp last_contacted_at
    }

    crm_companies {
        ulid id PK
        ulid company_id FK
        string name
        string domain
        string industry
        string size
        string website
        string phone
        string address
        text notes
        ulid owner_id FK
    }

    crm_contact_company {
        ulid crm_contact_id FK
        ulid crm_company_id FK
        string role
    }
```

**Contact status:** `lead` | `prospect` | `customer` | `lost` | `churned`

**Source:** `website` | `referral` | `cold-outreach` | `social` | `event` | `partner` | `other`

---

## Features

- Unified contact profile: personal info, company, deal history, ticket history, email activity, notes
- Duplicate detection on email address: warn on create, merge tool for duplicates
- Activity timeline: all interactions (emails, calls, meetings, notes, deals, tickets) in chronological order
- Tags and custom fields (flexible per company)
- Import contacts from CSV (with duplicate handling)
- Export contacts (with permission)
- Company record: aggregated stats — total deal value, open tickets, invoice history

---

## Permissions

```
crm.contacts.view
crm.contacts.create
crm.contacts.edit
crm.contacts.delete
crm.contacts.import
crm.contacts.export
crm.companies.view
crm.companies.create
crm.companies.edit
```

---

## Related

- [[MOC_CRM]]
- [[sales-pipeline]] — contacts linked to deals
- [[customer-support-helpdesk]] — contacts linked to tickets
- [[quotes-proposals]] — quotes sent to contacts
- [[MOC_Marketing]] — form submissions → auto-create contact
