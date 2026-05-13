---
type: module
domain: Real Estate & Property Management
panel: realestate
cssclasses: domain-realestate
phase: 6
status: complete
migration_range: 957000–959999
last_updated: 2026-05-12
---

# Tenant & Occupancy Management

Tenant records, occupancy tracking per unit, tenant portal for document exchange, and occupancy rate analytics.

---

## Tenant Records

### Tenant Profile
- Company or individual tenant
- If company: linked to CRM company record
- Contact persons: primary contact, accounts payable contact, emergency contact
- Credit information: credit check date, credit rating, credit limit
- Guarantor information (if personal guarantee given)
- Tenant category: retail / office / industrial / residential / co-working

### Communication Log
All tenant correspondence stored:
- Emails sent/received (via CRM email sync if active)
- Notices issued (rent review notices, break clause notices, S146 notices)
- Meeting notes

---

## Unit Management

Buildings can be subdivided into leasable units:
- Unit number/reference (e.g., "Suite 4A", "Unit 12")
- Floor, area (m²), use class
- Fit-out specification (shell & core / fitted / fully-serviced)
- Unit status: occupied / vacant / under-refurbishment / marketed

### Occupancy Status
| Status | Meaning |
|---|---|
| Occupied | Active tenant with current lease |
| Lease expired, in holdover | Tenant staying past expiry (periodic tenancy) |
| Vacant | No tenant, available to lease |
| Under refurbishment | Between tenants, works in progress |
| Under offer | Heads of terms agreed, lease not yet signed |
| Owner-occupied | Company occupying own property |

---

## Tenant Portal

Lightweight portal accessible to tenant contacts:
- View their lease terms and key dates
- Download: current lease, insurance certificate, service charge schedules
- Raise maintenance requests (forwarded to [[property-maintenance]])
- View rent statements and outstanding balance
- Upload: their own insurance certificate (required annually)

Portal requires no FlowFlex account — access via magic link email.

---

## Occupancy Analytics

Key metrics for portfolio management:

| Metric | Formula |
|---|---|
| Occupancy rate | Occupied m² / Total lettable m² × 100 |
| Void rate | Vacant m² / Total lettable m² × 100 |
| WAULT (Weighted Average Unexpired Lease Term) | Σ (rent × unexpired term) / Σ rent |
| Tenant concentration | Largest tenant's rent / Total passing rent × 100 |

WAULT is a critical investor/lender metric — longer WAULT = more secure income stream.

---

## Data Model

### `realestate_units`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| property_id | ulid | FK |
| unit_ref | varchar(50) | |
| floor | varchar(50) | nullable |
| area_sqm | decimal(10,2) | |
| use_class | varchar(50) | |
| status | enum | occupied/vacant/holdover/refurbishment/under_offer/owner_occupied |
| current_lease_id | ulid | nullable FK |

### `realestate_tenants`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | FlowFlex tenant_id |
| crm_company_id | ulid | nullable FK |
| name | varchar(300) | |
| category | enum | retail/office/industrial/residential/coworking/other |
| credit_rating | varchar(10) | nullable |
| credit_checked_at | date | nullable |
| guarantor_name | varchar(300) | nullable |
| portal_access_email | varchar | nullable |

---

## Migration

```
957000_create_realestate_units_table
957001_create_realestate_tenants_table
957002_create_realestate_tenant_contacts_table
957003_create_realestate_occupancy_snapshots_table
```

---

## Related

- [[MOC_RealEstate]]
- [[property-register]]
- [[lease-management]]
- [[rental-billing-arrears]]
- [[property-maintenance]]
- [[MOC_CRM]] — tenant company linked to CRM
