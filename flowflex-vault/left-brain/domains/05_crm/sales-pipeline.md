---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
status: in-progress
migration_range: 250004–250006
last_updated: 2026-05-11
right_brain_log: "[[builder-log-crm-phase3]]"
---

# Sales Pipeline

Kanban-style deal pipeline with configurable stages, deal value tracking, and activity management. Replaces Pipedrive, HubSpot Deals, Salesforce Opportunities.

**Panel:** `crm`  
**Phase:** 3  
**Module key:** `crm.pipeline`

---

## Data Model

```erDiagram
    deal_stages {
        ulid id PK
        ulid company_id FK
        string name
        integer sort_order
        integer probability
        boolean is_won
        boolean is_lost
        string color
    }

    crm_deals {
        ulid id PK
        ulid company_id FK
        string title
        ulid contact_id FK
        ulid crm_company_id FK
        ulid stage_id FK
        ulid owner_id FK
        decimal value
        string currency
        string status
        date expected_close_date
        timestamp closed_at
        string lost_reason
        text notes
    }

    crm_activities {
        ulid id PK
        ulid company_id FK
        ulid deal_id FK
        ulid contact_id FK
        ulid created_by FK
        string type
        string subject
        text description
        timestamp due_at
        timestamp completed_at
    }
```

**Deal status:** `open` | `won` | `lost`

**Activity type:** `call` | `email` | `meeting` | `task` | `note` | `demo` | `follow-up`

---

## Service: CrmDealService

```php
createDeal(CreateDealData $data): CrmDeal
moveToPipeline(CrmDeal $deal, DealStage $stage): void
markWon(CrmDeal $deal): void      // triggers DealWon event
markLost(CrmDeal $deal, string $reason): void  // triggers DealLost event
seedDefaultStages(string $companyId): void  // creates standard stages on company setup
```

### Default Stages (seeded on company creation)

| Stage | Sort | Probability |
|---|---|---|
| Lead | 1 | 10% |
| Qualified | 2 | 25% |
| Proposal | 3 | 50% |
| Negotiation | 4 | 75% |
| Won | 5 | 100% (is_won) |
| Lost | 6 | 0% (is_lost) |

---

## Events

| Event | Trigger | Consumed By |
|---|---|---|
| `DealCreated` | createDeal() | Notifications (owner), Analytics |
| `DealWon` | markWon() | Finance (create invoice), Projects (create project), Analytics |
| `DealLost` | markLost() | Analytics, CRM (nurture sequence if sequences built) |

---

## Permissions

```
crm.deals.view
crm.deals.create
crm.deals.edit
crm.deals.delete
crm.pipeline.manage-stages
crm.activities.create
crm.activities.view
```

---

## Related

- [[MOC_CRM]]
- [[contact-company-management]] — deals linked to contacts and companies
- [[quotes-proposals]] — quotes linked to deals
- [[MOC_Finance]] — won deal → auto-create invoice trigger
- [[MOC_Projects]] — won deal → project kickoff
