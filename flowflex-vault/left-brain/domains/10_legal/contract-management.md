---
type: module
domain: Legal & Compliance
panel: legal
cssclasses: domain-legal
phase: 4
status: complete
migration_range: 550000–554999
last_updated: 2026-05-12
---

# Contract Management

Full contract lifecycle — intake, review, negotiation, execution, obligation tracking, and expiry management. Reduces manual email-chain contract management and missed renewal windows.

**Panel:** `legal`  
**Phase:** 4  
**Migration range:** `550000–554999`

---

## Features

### Core (MVP)

- Contract intake: upload or create from template, metadata capture (parties, value, dates, type)
- Contract types: customer, supplier, employment, NDA, partnership, lease
- Status workflow: draft → review → negotiation → executed → active → expired / renewed
- Party management: link to CRM contacts/companies or supplier records
- Expiry tracking: dashboard of contracts expiring in 30/60/90 days
- Document storage: versioned PDF storage with change log
- Search and filter: by type, party, status, expiry date, value

### Advanced

- Obligation tracking: key obligations with due dates and assigned owners
- Renewal reminders: configurable notification schedule before expiry
- Contract value tracking: total committed spend / revenue per contract
- Negotiation redlines: track changes per version with comment thread
- Clause library: standard approved clause variants for negotiation

### AI-Powered

- AI clause extraction: auto-extract parties, dates, payment terms, termination clauses
- Risk scoring: flag non-standard clauses vs approved library
- Obligation extraction: auto-identify obligations and deadlines from contract text
- See [[ai-contract-intelligence]] for full AI contract capabilities

---

## Data Model

```erDiagram
    contracts {
        ulid id PK
        ulid company_id FK
        string title
        string contract_type
        string status
        decimal contract_value
        string currency
        date start_date
        date end_date
        date renewal_notice_date
        boolean auto_renews
        integer renewal_notice_days
        ulid owner_id FK
        softDeletes deleted_at
        timestamps timestamps
    }

    contract_parties {
        ulid id PK
        ulid contract_id FK
        string party_type
        string party_name
        string party_role
    }

    contract_obligations {
        ulid id PK
        ulid contract_id FK
        string description
        date due_date
        ulid assigned_to FK
        boolean completed
    }

    contracts ||--o{ contract_parties : "has"
    contracts ||--o{ contract_obligations : "has"
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `ContractExpiring` | N days before end_date | Notifications (legal team, owner) |
| `ContractExpired` | end_date reached | Notifications (urgent escalation) |
| `ContractExecuted` | Status → executed | CRM (update deal), Finance (create billing schedule) |
| `ObligationDue` | Obligation due date approaching | Notifications (assigned owner) |

### Consumed

| Event | From | Action |
|---|---|---|
| `DocumentSigned` | E-Signature | Mark contract as executed, store signed PDF |
| `DealClosed` | CRM | Auto-create customer contract from deal data |

---

## Permissions

```
legal.contracts.view-any
legal.contracts.view
legal.contracts.create
legal.contracts.update
legal.contracts.delete
legal.contracts.execute
legal.contracts.view-value
```

---

## Related

- [[MOC_Legal]]
- [[ai-contract-intelligence]] — AI-powered clause analysis
- [[esignature-native]] — contract execution
- [[MOC_CRM]] — customer contracts link to deals
- [[MOC_Finance]] — contract value drives revenue forecasting
