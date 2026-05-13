---
type: module
domain: Real Estate & Property Management
panel: realestate
cssclasses: domain-realestate
phase: 6
status: complete
migration_range: 954000–956999
last_updated: 2026-05-12
---

# Lease Management

Manage all aspects of property leases — both as landlord (leases to tenants) and as lessee (leases from landlords). Tracks terms, rent reviews, break clauses, and expiry alerting.

---

## Two Perspectives

### Landlord Perspective (outgoing leases)
Company owns property and leases units to tenants:
- Lease terms per tenant per unit
- Rent receivable
- Renewal and break clause management
- Links to [[tenant-occupancy-management]] and [[rental-billing-arrears]]

### Lessee Perspective (incoming leases)
Company leases property from a landlord:
- Lease terms per property
- Rent payable
- Feeds [[ifrs-16-lease-accounting]] for balance sheet treatment
- Linked to [[property-register]] for the property

---

## Lease Terms

Each lease record contains:
- **Parties**: landlord / tenant / guarantor
- **Property + unit**: which unit(s) covered
- **Commencement date, expiry date**
- **Lease length**: expressed in years and months
- **Break clause(s)**: date, type (mutual/landlord/tenant), notice period required
- **Rent review dates**: scheduled rent review dates and review type (open market / fixed increase / CPI-linked)
- **Rent**: annual rent, payment frequency (monthly/quarterly), rent-free period
- **Security deposit**: amount, bank guarantee or cash
- **Service charge**: amount or % of building service charge

### Rent Review Types
| Type | Behaviour |
|---|---|
| Open market | Agreed at review date based on market evidence |
| Fixed increase | % increase defined in lease (e.g., +3% p.a.) |
| CPI-linked | Tracked to consumer price index |
| RPI-linked | Tracked to retail price index |
| Fixed rent | No review (fixed rent for lease term) |

---

## Alerts & Diary

Critical lease diary events with advance alerts:
| Event | Default Alert Lead Time |
|---|---|
| Lease expiry | 24 months, 12 months, 6 months, 3 months |
| Break clause window opens | 6 months, 3 months, 1 month |
| Break clause notice deadline | 60 days, 30 days |
| Rent review due | 3 months, 1 month |
| Rent-free period expiry | 3 months |

All alerts: email + in-app notification to property manager.

---

## Data Model

### `realestate_leases`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | tenant_id (FlowFlex tenant) |
| property_id | ulid | FK `realestate_properties` |
| unit_id | ulid | nullable FK `realestate_units` |
| lease_type | enum | outgoing/incoming |
| tenant_company_id | ulid | nullable FK `crm_companies` (for outgoing) |
| landlord_name | varchar(300) | for incoming leases |
| commencement_date | date | |
| expiry_date | date | nullable (rolling leases) |
| rent_annual | decimal(14,2) | |
| rent_frequency | enum | monthly/quarterly/annually |
| rent_free_until | date | nullable |
| deposit_amount | decimal(14,2) | nullable |
| deposit_type | enum | cash/bank_guarantee |
| status | enum | draft/active/expired/surrendered/renewed |

### `realestate_lease_reviews`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| lease_id | ulid | FK |
| review_type | enum | open_market/fixed_pct/cpi/rpi/fixed |
| review_date | date | |
| previous_rent | decimal(14,2) | |
| new_rent | decimal(14,2) | nullable |
| fixed_increase_pct | decimal(5,2) | nullable |
| settled_at | date | nullable |

### `realestate_lease_breaks`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| lease_id | ulid | FK |
| break_date | date | |
| break_type | enum | mutual/landlord_only/tenant_only |
| notice_period_days | int | |
| conditions | text | nullable |
| exercised | bool | default false |
| exercised_at | date | nullable |

---

## Migration

```
954000_create_realestate_leases_table
954001_create_realestate_lease_reviews_table
954002_create_realestate_lease_breaks_table
954003_create_realestate_lease_diary_events_table
```

---

## Related

- [[MOC_RealEstate]]
- [[property-register]]
- [[tenant-occupancy-management]]
- [[rental-billing-arrears]]
- [[ifrs-16-lease-accounting]]
