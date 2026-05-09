---
type: module
domain: Professional Services Automation
panel: psa
cssclasses: domain-psa
phase: 7
status: planned
migration_range: 870000–873999
last_updated: 2026-05-09
---

# Client Engagement Management

Central record of every client engagement: Statement of Work, scope, deliverables, team, budget. PSA's equivalent of CRM's opportunity — a signed deal in execution.

---

## Core Functionality

### Engagement Record
An engagement ties together:
- Client (CRM Company/Contact)
- Statement of Work (SOW) or contract reference
- Engagement type: Retainer / Fixed Price / Time & Materials / Milestone-based
- Start and end dates
- Agreed budget (total or per period)
- Delivery team (engagement manager + assigned resources)
- Billing contact and billing schedule

### Engagement Stages
```
Proposal → SOW Signed → Active → On Hold → Completed → Closed
```

Stage transitions trigger events (e.g., `EngagementActivated` → create project in Projects module).

### SOW Management
- Upload signed SOW PDF (stored in document vault)
- Extract scope items as deliverables list
- Change order log: amendments to original SOW, each with approval date and budget delta
- SOW version history

### Engagement Health
RAG status computed weekly:
- **Red**: budget > 90% consumed or schedule overrun > 10%
- **Amber**: budget > 75% consumed or scope creep detected (> 10% unapproved change orders)
- **Green**: on track

---

## Data Model

### `psa_engagements`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| client_company_id | ulid | FK `crm_companies` |
| engagement_manager_id | ulid | FK `employees` |
| name | varchar(200) | "Website Redesign Q3 2026" |
| type | enum | retainer/fixed/time_materials/milestone |
| stage | enum | proposal/active/on_hold/completed/closed |
| start_date | date | |
| end_date | date | nullable |
| contracted_value | decimal(14,2) | |
| currency | char(3) | |
| sow_document_id | ulid | nullable FK document vault |
| billing_contact_id | ulid | FK `crm_contacts` |
| health_status | enum | green/amber/red | computed |

### `psa_sow_change_orders`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| engagement_id | ulid | FK |
| title | varchar(200) | |
| description | text | |
| budget_delta | decimal(14,2) | positive = increase, negative = reduction |
| approved_at | date | |
| approved_by | varchar(200) | client signatory name |

---

## Integrations

- **CRM** — client company/contact lookup; engagement created from won opportunity
- **Projects** — engagement → creates one or more projects
- **Finance** — engagement value feeds revenue recognition; invoicing linked to billing schedule
- **PSA Billing** — billing schedule derived from engagement type

---

## Migration

```
870000_create_psa_engagements_table
870001_create_psa_sow_change_orders_table
870002_create_psa_engagement_team_members_table
870003_create_psa_engagement_deliverables_table
```

---

## Related

- [[MOC_PSA]]
- [[retainer-sow-management]]
- [[project-profitability]]
- [[agency-billing-intelligence]]
- [[MOC_CRM]] — client records
- [[MOC_Projects]] — project execution
