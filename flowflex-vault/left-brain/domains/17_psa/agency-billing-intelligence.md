---
type: module
domain: Professional Services Automation
panel: psa
cssclasses: domain-psa
phase: 7
status: planned
migration_range: 884000–889999
last_updated: 2026-05-09
---

# Agency Billing Intelligence

Generate client-ready billing reports, manage WIP (Work In Progress), and push approved billing to Finance for invoicing. Bridges PSA time/delivery tracking to AR.

---

## Billing Methods

### Time & Materials (T&M)
- Collects all approved billable time entries for billing period
- Groups by: person → role → engagement → client
- Applies sell rate per person or role (from sell rate card)
- Deducts any capped hours (if T&M has a cap per period)
- Produces itemised billing schedule: date, person, hours, description, amount

### Fixed Price / Milestone
- Billing triggered by milestone completion (from [[retainer-sow-management]])
- Milestone confirmed → draft invoice line created at agreed milestone value
- Progress billing option: bill X% at kick-off, X% at delivery, X% at sign-off

### Retainer
- Monthly flat fee invoice created automatically at billing date
- Overage hours (if any) added as separate line item
- Credit note created if client is owed credit (unused hours in credit policy)

---

## WIP Register

Work in Progress = time/value delivered but not yet invoiced.

- All unbilled approved time entries have WIP value = hours × sell rate
- WIP by client and by engagement
- Aged WIP: how long work has been sitting unbilled (flag if > 30/60/90 days)
- WIP → Invoice conversion: select period + client + engagement → generate invoice draft in Finance

Finance use: WIP is a balance sheet asset (accrued revenue). Monthly close includes WIP reconciliation.

---

## Client Billing Reports

Sent alongside invoices to give clients full transparency:

### Detailed Timesheet Report
- Per day, per person: description, hours worked, billable/non-billable split
- Branded PDF with client logo + agency logo
- Optionally redact internal notes

### Project Status Summary
- % complete vs budget consumed
- Milestones achieved this period
- Upcoming milestones next period
- Outstanding decisions/blockers

### Retainer Burndown Report
- Hours used this period, hours remaining, rollover (if applicable)
- Burndown chart (hours over time)
- Overage explanation if applicable

---

## Data Model

### `psa_billing_periods`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| engagement_id | ulid | FK |
| period_start | date | |
| period_end | date | |
| status | enum | draft/approved/invoiced |
| billing_method | enum | t_and_m/fixed/retainer/milestone |
| total_hours | decimal(7,2) | |
| total_value | decimal(14,2) | |
| invoice_id | ulid | nullable, FK Finance AR |

### `psa_billing_line_items`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| billing_period_id | ulid | FK |
| time_entry_ids | json | array of source time entry ULIDs |
| employee_id | ulid | FK |
| description | varchar(500) | |
| hours | decimal(6,2) | |
| sell_rate | decimal(10,2) | |
| amount | decimal(14,2) | computed |

---

## Approval Workflow
1. Billing period closes → system auto-collects all billable time entries
2. Account manager reviews draft billing report
3. Account manager approves → status = approved
4. Finance confirms → invoice created in AR with billing report attached
5. Invoice sent to client

---

## Integrations

- **Projects** — source: time entries with billable flag
- **Finance** — pushes approved billing as invoice draft to AR
- **Retainer module** — reads retainer period data for retainer invoicing
- **CRM** — client billing contact for invoice delivery

---

## Migration

```
884000_create_psa_billing_periods_table
884001_create_psa_billing_line_items_table
884002_create_psa_sell_rate_cards_table
884003_create_psa_wip_register_view
```

---

## Related

- [[MOC_PSA]]
- [[client-engagement-management]]
- [[retainer-sow-management]]
- [[project-profitability]]
- [[MOC_Finance]] — AR invoicing
