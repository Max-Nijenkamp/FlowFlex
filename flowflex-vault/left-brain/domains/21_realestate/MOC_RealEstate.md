---
type: moc
domain: Real Estate & Property Management
panel: realestate
cssclasses: domain-realestate
phase: 6
color: "#57534E"
last_updated: 2026-05-09
---

# Real Estate & Property Management — Map of Content

Manage owned or leased property portfolio. Lease accounting (IFRS 16), tenant billing, property maintenance, rental income, occupancy analytics. For retail chains, property companies, co-working operators, franchise operators. Replaces Yardi, Re-Leased, AppFolio, MRI Software.

**Panel:** `realestate`  
**Phase:** 6  
**Migration Range:** `950000–969999`  
**Colour:** Stone `#57534E` / Light: `#F5F5F4`  
**Icon:** `heroicon-o-building-office`

---

## Modules

| Module | Phase | Status | Description |
|---|---|---|---|
| Property Register | 6 | planned | Portfolio of owned/leased properties with metadata |
| Lease Management | 6 | planned | Lease terms, rent reviews, break clauses, expiry alerts |
| Tenant & Occupancy Management | 6 | planned | Tenant records, occupancy rates, tenant portal |
| Rental Billing & Arrears | 6 | planned | Recurring rent invoices, service charges, arrears management |
| IFRS 16 Lease Accounting | 6 | planned | Right-of-use asset, lease liability, amortisation schedule |
| Property Maintenance | 6 | planned | Maintenance requests, contractor dispatch, compliance certs |

---

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `LeaseExpiring` | Lease Management | Notifications (property manager), Legal (renewal) |
| `RentOverdue` | Rental Billing | Notifications (property manager), Finance (AP/AR) |
| `MaintenanceJobCompleted` | Property Maintenance | Billing (recharge to tenant if applicable) |
| `OccupancyChanged` | Tenancy Management | Analytics (occupancy rate), Finance (revenue impact) |

---

## Permissions Prefix

`realestate.properties.*` · `realestate.leases.*`  
`realestate.tenants.*` · `realestate.billing.*` · `realestate.maintenance.*`

---

## Competitors Displaced

Yardi Voyager · Re-Leased · AppFolio · MRI Software · CoStar Real Estate Manager · Planon

---

## Related

- [[MOC_Domains]]
- [[MOC_Finance]] — rent revenue, IFRS 16 journal entries
- [[MOC_Operations]] — property maintenance overlaps with Equipment Maintenance
- [[MOC_Workplace]] — space booking for own offices (different: Workplace = employee desk booking; Real Estate = landlord managing tenants)
