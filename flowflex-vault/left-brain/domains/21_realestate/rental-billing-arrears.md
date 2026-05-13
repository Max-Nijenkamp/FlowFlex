---
type: module
domain: Real Estate & Property Management
panel: realestate
cssclasses: domain-realestate
phase: 6
status: complete
migration_range: 960000–962999
last_updated: 2026-05-12
---

# Rental Billing & Arrears

Recurring rent invoices, service charge billing, arrears tracking, and rent recovery workflow. Revenue engine for the property portfolio.

---

## Recurring Rent Invoices

Rent invoices auto-generated from lease terms:

- Scheduled job creates invoice N days before payment due date (configurable: 14 days default)
- Invoice amount: derived from current lease rent (after last rent review)
- Invoice lines: rent / service charge / insurance contribution / car parking (if separate)
- VAT: applied per invoice line according to elected option to tax (UK) or local VAT rules
- Sent: email PDF to tenant's accounts payable contact + stored on tenant record

Quarterly rent in advance (English commercial leases): invoiced for quarter starting on next quarter day (25 March / 24 June / 29 September / 25 December).

### Service Charge Billing
- Annual service charge budget distributed to tenants by lease % or m² apportionment
- Interim quarterly billings (on-account)
- Year-end reconciliation: actual vs estimated → credit or additional demand issued

---

## Arrears Management

### Arrears Ageing
Standard ageing buckets:
- Current (not yet due)
- 1–30 days overdue
- 31–60 days overdue
- 61–90 days overdue
- 90+ days overdue (critical)

Dashboard: total arrears per property, per tenant, aged split. RAG traffic light per tenant.

### Collection Workflow
Automated escalation ladder (configurable):
1. **Day 1 overdue**: automatic reminder email to AP contact
2. **Day 7**: formal letter PDF (generated from template) + email
3. **Day 14**: phone call prompt to property manager (manual task created)
4. **Day 30**: legal notice recommendation + escalate to director

Letters generated from templates with: tenant name, property, amount, reference, legal boilerplate (UK S146 / NL ingebrekestelling).

### Forfeiture (UK)
Commercial landlord right to forfeit lease for unpaid rent (UK law: Commercial Rent (Coronavirus) Act 2022 expired). Module tracks:
- Pre-action protocol compliance
- CRAR (Commercial Rent Arrears Recovery) notice log
- Legal counsel instructed flag

### Payment Allocation
When payment received (from Finance AR):
- Allocate oldest invoice first (or specific invoice if reference quoted)
- Partial payment: allocate proportionally or flag for manual review
- Payment plan: if agreed, record instalments schedule

---

## Data Model

### `realestate_rent_demands`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | FlowFlex tenant_id |
| lease_id | ulid | FK |
| demand_type | enum | rent/service_charge/insurance/other |
| period_start | date | |
| period_end | date | |
| due_date | date | |
| amount | decimal(14,2) | |
| vat_amount | decimal(14,2) | |
| currency | char(3) | |
| status | enum | draft/issued/partially_paid/paid/overdue/written_off |
| invoice_id | ulid | nullable FK Finance AR |

### `realestate_arrears_actions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | FlowFlex tenant_id |
| realestate_tenant_id | ulid | FK |
| action_type | enum | reminder/formal_letter/phone_prompt/legal_notice/payment_plan |
| action_date | date | |
| amount_outstanding | decimal(14,2) | |
| notes | text | nullable |
| created_by | ulid | nullable FK employees |

---

## Migration

```
960000_create_realestate_rent_demands_table
960001_create_realestate_service_charge_budgets_table
960002_create_realestate_arrears_actions_table
960003_create_realestate_payment_plans_table
```

---

## Related

- [[MOC_RealEstate]]
- [[lease-management]]
- [[tenant-occupancy-management]]
- [[MOC_Finance]] — rent demand → AR invoice; payment → receipt allocation
