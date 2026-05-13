---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.contacts
status: planned
color: "#4ADE80"
---

# Contacts

> Contact and company records, relationship mapping, activity timeline, and communication history — the central people database for all CRM activity.

**Panel:** `crm`
**Module key:** `crm.contacts`

## What It Does

Contacts is the foundational module of the CRM domain. It maintains a unified database of individual contacts (people) and the companies they work for. Every other CRM module — deals, activities, quotes, sequences — links back to a contact or company record. Each contact has a full activity timeline showing all interactions: emails, calls, meetings, deals, quotes, and notes in chronological order. Duplicate detection warns when a contact with the same email is added. The module feeds into Email Integration (auto-link emails), Customer Segments (filter cohorts), and Marketing (form submissions → auto-create contact).

## Features

### Core
- Contact record: first name, last name, email, phone, mobile, job title, source, status, owner, linked company, notes, custom fields, last contacted date
- Company record: name, domain, industry, company size, website, phone, address, notes, owner
- Contact-company relationship: many-to-many with role (e.g. CEO, Decision Maker, Champion)
- Status tracking: lead / prospect / customer / lost / churned
- Source tracking: website / referral / cold-outreach / social / event / partner / other

### Advanced
- Activity timeline: all interactions (emails, calls, meetings, deals, quotes, notes) shown chronologically on contact profile
- Duplicate detection: warn on create if a contact with the same email exists — merge tool for duplicates
- CSV import: bulk import contacts with duplicate handling (skip/overwrite/create-new configurable)
- Tags and custom fields: per-company taxonomy and extensible JSON custom fields
- Company aggregated stats: total deal value, open opportunities count, invoice history, support tickets

### AI-Powered
- Contact enrichment: AI enriches contact records with publicly available information (LinkedIn title, company size, industry) to fill blank fields
- Relationship scoring: AI scores the strength of the relationship with a contact based on interaction frequency, recency, and sentiment of email communications

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
        timestamps created_at/updated_at
    }

    crm_companies {
        ulid id PK
        ulid company_id FK
        string name
        string domain
        string industry
        string size
        string website
        ulid owner_id FK
        text notes
        timestamps created_at/updated_at
    }

    crm_contact_company {
        ulid crm_contact_id FK
        ulid crm_company_id FK
        string role
    }
```

| Column | Notes |
|---|---|
| `status` | lead / prospect / customer / lost / churned |
| `source` | website / referral / cold-outreach / social / event / partner / other |
| `crm_company_id` | FK to `crm_companies` — separate from tenant `companies` table |

## Permissions

- `crm.contacts.view`
- `crm.contacts.create`
- `crm.contacts.edit`
- `crm.contacts.delete`
- `crm.contacts.import`

## Filament

- **Resource:** `ContactResource`, `CrmCompanyResource`
- **Pages:** `ListContacts`, `CreateContact`, `EditContact`, `ViewContact` (with activity timeline tab), `ListCrmCompanies`, `ViewCrmCompany`
- **Custom pages:** None
- **Widgets:** `NewContactsThisMonthWidget` — new contact count on CRM dashboard
- **Nav group:** Contacts (crm panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| HubSpot CRM | Contact and company management |
| Salesforce | Account and contact records |
| Pipedrive | Contact database |
| Close | Contact management |

## Related

- [[deals]]
- [[activities]]
- [[email-integration]]
- [[customer-segments]]
- [[sales-sequences]]
