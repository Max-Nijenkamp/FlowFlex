---
type: module
domain: CRM & Sales
panel: crm
phase: 3
status: planned
cssclasses: domain-crm
migration_range: 308000–308499
last_updated: 2026-05-09
---

# Contract Lifecycle Management (CLM)

Manage customer contracts from negotiation through signature to renewal. Built into the sales process — no external CLM tool needed. Links to [[contract-repository]] in DMS for centralised storage.

---

## CLM in the Sales Process

```
Opportunity Won
→ Generate contract (from DMS template)
→ Legal review (internal workflow)
→ Send for signature (eSign via DMS)
→ Signed → Contract record created
→ Linked to Account in CRM
→ Renewal reminder set
→ Renewal opportunity auto-created 90 days before expiry
```

---

## Contract Negotiation

Redlining workflow:
- Customer sends back redlined version → upload to CRM
- Internal legal reviews → accepts/rejects clauses
- Version tracking (v1, v2-customer, v2-internal, final)
- Approval required before each send to customer

---

## Contract Terms on Account

Key terms visible directly on the CRM account:
- Contract value (ACV / TCV)
- Start date, end date
- Renewal type: auto / manual / co-term
- Payment terms, payment schedule
- SLA tier (drives CS team obligations)
- Special pricing / custom discounts
- Non-standard clauses (flagged for attention)

---

## Renewal Management

Renewal workflow:
- T-90 days: renewal opportunity auto-created in pipeline
- AE assigned (or CS manager for existing accounts)
- Renewal pricing calculated: CPI increase, usage tier, expansion
- New contract generated from renewal template
- Same signature flow as new contract

---

## Contract Analytics

Portfolio view:
- ARR by contract tier
- Upcoming renewals by month (next 12 months)
- Average contract length
- Discount depth analysis (% of deals with custom pricing)
- Contract cycle time: draft → signed (average days)

---

## Data Model

### `crm_contracts`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| account_id | ulid | FK |
| opportunity_id | ulid | nullable FK |
| dms_contract_id | ulid | nullable FK → DMS |
| status | enum | draft/negotiation/signed/active/expired/cancelled |
| acv | decimal(14,2) | annual contract value |
| tcv | decimal(14,2) | total contract value |
| start_date | date | nullable |
| end_date | date | nullable |
| renewal_type | enum | auto/manual/co_term |
| sla_tier | varchar(50) | nullable |

---

## Migration

```
308000_create_crm_contracts_table
308001_create_crm_contract_versions_table
```

---

## Related

- [[MOC_CRM]]
- [[MOC_DMS]] — e-signature + storage
- [[MOC_CustomerSuccess]] — SLA enforcement
- [[sales-forecasting]]
