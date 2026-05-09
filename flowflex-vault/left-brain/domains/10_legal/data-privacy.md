---
type: module
domain: Legal & Compliance
panel: legal
cssclasses: domain-legal
phase: 4
status: planned
migration_range: 560000–564999
last_updated: 2026-05-09
---

# Data Privacy (GDPR)

GDPR compliance tooling — consent management, data inventory (Records of Processing Activities), Data Protection Impact Assessments, and the full DSAR workflow including automated data erasure.

**Panel:** `legal`  
**Phase:** 4  
**Migration range:** `560000–564999`

---

## Features

### Core (MVP)

- Records of Processing Activities (RoPA): inventory of all data processing activities per GDPR Art. 30
- Legal basis register: document lawful basis per processing activity (consent, contract, legitimate interest, etc.)
- Consent management: record and track consent per data subject and purpose
- Data subject request (DSAR) intake: see [[dsar-self-service-portal]] for public portal
- DSAR workflow: assign, investigate, respond within 30-day statutory deadline
- Breach register: log data breaches, assess severity, generate ICO notification if required

### Advanced

- Data Protection Impact Assessment (DPIA): structured risk assessment workflow for new processing activities
- Data inventory: map data flows — what data, where stored, who has access, how long retained
- Retention schedule: automated alerts when data exceeds retention period
- Third-party processor register: DPA status with all data processors
- Cross-border transfer documentation: SCCs, adequacy decisions

### AI-Powered

- DSAR response drafter: AI generates initial response based on data inventory
- DPIA risk scoring: auto-assess risk level based on processing activity inputs
- Breach severity assessment: AI scores breach severity and recommends notification timeline

---

## Data Model

```erDiagram
    processing_activities {
        ulid id PK
        ulid company_id FK
        string name
        string purpose
        string legal_basis
        json data_categories
        json data_subjects
        json recipients
        string retention_period
        boolean involves_international_transfer
    }

    data_breaches {
        ulid id PK
        ulid company_id FK
        string title
        date discovered_at
        string severity
        string status
        boolean reported_to_ico
        timestamp ico_reported_at
        text description
        text remediation_steps
    }

    consent_records {
        ulid id PK
        ulid company_id FK
        string data_subject_email
        string purpose
        boolean consented
        string ip_address
        string source
        timestamp consented_at
        timestamp withdrawn_at
    }
```

---

## Events

### Emitted

| Event | When | Consumed By |
|---|---|---|
| `DSARReceived` | DSAR submitted | Legal (assign investigator), Notifications |
| `DSARDeadlineApproaching` | 5 days before 30-day deadline | Notifications (assignee escalation) |
| `DataBreachLogged` | Breach recorded | Notifications (DPO), Legal (assess ICO notification need) |
| `ConsentWithdrawn` | Data subject withdraws consent | IT (trigger data deletion) |

### Consumed

| Event | From | Action |
|---|---|---|
| `EmployeeOffboarded` | HR | Trigger data retention review for employee data |

---

## Permissions

```
legal.privacy.view-ropa
legal.privacy.manage-ropa
legal.privacy.view-dsars
legal.privacy.manage-dsars
legal.privacy.manage-breaches
legal.privacy.view-consent
legal.privacy.manage-dpias
```

---

## Related

- [[MOC_Legal]]
- [[dsar-self-service-portal]] — public intake portal for DSARs
- [[MOC_IT]] — IT implements data deletions from DSAR erasure requests
- [[entity-company]] — one RoPA per company (tenant)
