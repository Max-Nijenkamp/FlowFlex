---
type: module
domain: Real Estate & Property Management
panel: realestate
cssclasses: domain-realestate
phase: 6
status: complete
migration_range: 965000–969999
last_updated: 2026-05-12
---

# Property Maintenance

Manage planned and reactive maintenance across the property portfolio. Compliance certificate tracking, contractor management, and maintenance cost reporting.

---

## Maintenance Types

### Reactive Maintenance
Triggered by:
- Tenant report (via [[tenant-occupancy-management]] tenant portal)
- Property manager site visit observation
- Building management system alert (e.g., BMS temperature alarm)

Workflow:
1. Job raised → assign priority and contractor
2. Contractor acknowledges → provides estimated start date
3. Works completed → contractor uploads completion photos + invoice
4. Property manager approves → invoice to Finance AP
5. Tenant notified of resolution

### Planned Preventive Maintenance (PPM)
Scheduled recurring jobs (same concept as [[facility-maintenance-requests]]):
- Annual: fire alarm test, emergency lighting test, sprinkler inspection
- 5-yearly: electrical installation condition report (EICR)
- Monthly: fire extinguisher check, emergency exit inspection

PPM calendar shows all due tasks for next 12 months. Auto-creates job when due.

### Capital Works
Major refurbishment / improvement projects:
- Budget, scope, contractor tender process
- Stage payments linked to project milestones
- Capitalisation decision: expense vs capitalise (linked to Finance fixed assets)
- Practical completion certificate upload

---

## Compliance Certificates

Legal compliance tracking per property:

| Certificate | Typical Frequency | Statutory? |
|---|---|---|
| Gas Safety Certificate | Annual | Yes (UK: Gas Safety Regs 1998) |
| Electrical Installation Condition Report (EICR) | 5-yearly | Yes (commercial) |
| Energy Performance Certificate (EPC) | 10-yearly or on lease | Yes (UK: EPC for rented commercial) |
| Fire Risk Assessment | Annual review | Yes |
| Asbestos Management Survey | On change | Yes (if building pre-2000) |
| Legionella Risk Assessment | 2-yearly | Yes (HSE ACoP L8) |
| Lift Inspection Certificate | 6-monthly (LOLER) | Yes |

Dashboard: all certificates with expiry dates, RAG status. Red = expired. Amber = expiring within 90 days.

Auto-alert to property manager when certificate within 90-day window.

---

## Contractor Register

Per property or portfolio-wide:
- Contractor company, trade, contact details
- Insurance: public liability (min £2m/£5m), employer's liability, professional indemnity
- Insurance expiry → auto-alert before using contractor after expiry
- DBS check (if required for residential properties)
- Performance history: average response time, quality ratings per job

---

## Tenant Recharge

Some maintenance costs are rechargeable to tenants under lease service charge provisions:
- Flag job as rechargeable at time of raising
- Link to tenant + lease
- On completion: create service charge debit in [[rental-billing-arrears]]

---

## Data Model

### `realestate_maintenance_jobs`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | FlowFlex tenant_id |
| property_id | ulid | FK |
| unit_id | ulid | nullable FK |
| job_type | enum | reactive/ppm/capital |
| category | varchar(100) | Electrical / Plumbing / HVAC / etc |
| priority | enum | p1_emergency/p2_urgent/p3_normal/p4_planned |
| title | varchar(300) | |
| description | text | |
| reported_by | varchar(200) | nullable |
| source | enum | tenant/property_manager/ppm/bms |
| contractor_id | ulid | nullable FK |
| status | enum | open/assigned/in_progress/complete/invoiced/closed |
| rechargeable | bool | |
| rechargeable_tenant_id | ulid | nullable FK `realestate_tenants` |
| estimated_cost | decimal(12,2) | nullable |
| actual_cost | decimal(12,2) | nullable |
| completed_at | timestamp | nullable |

### `realestate_compliance_certificates`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | FlowFlex tenant_id |
| property_id | ulid | FK |
| certificate_type | varchar(100) | |
| issued_date | date | |
| expiry_date | date | nullable |
| document_id | ulid | FK document vault |
| status | enum | valid/expired/expiring_soon |

---

## Migration

```
965000_create_realestate_maintenance_jobs_table
965001_create_realestate_maintenance_contractors_table
965002_create_realestate_compliance_certificates_table
965003_create_realestate_ppm_schedules_table
```

---

## Related

- [[MOC_RealEstate]]
- [[property-register]]
- [[tenant-occupancy-management]] — tenant-reported jobs
- [[rental-billing-arrears]] — rechargeable maintenance → service charge
- [[MOC_Finance]] — contractor invoices → AP
