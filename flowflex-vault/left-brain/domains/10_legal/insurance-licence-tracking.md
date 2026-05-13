---
type: module
domain: Legal & Compliance
panel: legal
cssclasses: domain-legal
phase: 7
status: complete
migration_range: 565000–569999
last_updated: 2026-05-12
---

# Insurance & Licence Tracking

Centralised register of insurance policies and business licences — with certificate storage, renewal reminders, and coverage gap reporting.

**Panel:** `legal`  
**Phase:** 7  
**Migration range:** `565000–569999`

---

## Features

### Core (MVP)

- Insurance policy register: type, insurer, policy number, coverage amount, premium, start/end dates
- Insurance types: public liability, employer's liability, professional indemnity, cyber, D&O, property
- Certificate storage: upload policy certificates and endorsements
- Renewal reminders: configurable alerts at 60/30/7 days before expiry
- Business licence register: licences, permits, regulatory approvals with expiry dates
- Coverage summary dashboard: all active policies at a glance

### Advanced

- Coverage gap analysis: required vs actual coverage per policy type
- Claim log: record claims against policies with outcome tracking
- Multi-entity support: track policies per subsidiary/legal entity
- Supplier insurance verification: require and track supplier COIs (Certificates of Insurance)

### AI-Powered

- Auto-extract from certificate uploads: insurer, policy number, coverage, dates via OCR
- Renewal priority scoring: flag which renewals need immediate attention

---

## Data Model

```erDiagram
    insurance_policies {
        ulid id PK
        ulid company_id FK
        string policy_type
        string insurer_name
        string policy_number
        decimal coverage_amount
        string currency
        decimal annual_premium
        date start_date
        date end_date
        string certificate_url
        ulid owner_id FK
    }

    business_licences {
        ulid id PK
        ulid company_id FK
        string licence_name
        string issuing_authority
        string licence_number
        date issued_at
        date expires_at
        string document_url
    }
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `InsuranceExpiring` | N days before end_date | Notifications (legal + finance) |
| `LicenceExpiring` | N days before expires_at | Notifications (legal + operations) |

---

## Permissions

```
legal.insurance.view-any
legal.insurance.manage
legal.licences.view-any
legal.licences.manage
```

---

## Related

- [[MOC_Legal]]
- [[MOC_Finance]] — insurance premiums tracked as operating expenses
