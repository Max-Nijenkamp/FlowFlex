---
type: module
domain: Workplace & Facility Management
panel: workplace
cssclasses: domain-workplace
phase: 6
status: complete
migration_range: 863000–865999
last_updated: 2026-05-12
---

# Facility Maintenance Requests

Staff raise maintenance tickets for office issues. Facilities manager triages, assigns to contractors or in-house maintenance team, tracks SLA compliance.

---

## Core Functionality

### Request Submission
- Staff submit via: Filament panel, mobile app, or QR code on equipment/area
- Categories: Electrical, HVAC, Plumbing, Cleaning, Furniture, IT Infrastructure (physical), Security (door access), Other
- Required fields: location (building/floor/room), description, urgency
- Photo attachment (max 5 photos)
- Anonymous reporting option (for sensitive issues)

### Triage & Assignment
- Facilities manager inbox: new requests queue
- Assign to: in-house maintenance staff, preferred contractor, or FM company
- Set priority: P1 Emergency (same day), P2 Urgent (48h), P3 Normal (5 days), P4 Planned (scheduled)
- Estimated cost entry (for budget tracking)

### SLA Tracking
| Priority | Target Response | Target Resolution |
|---|---|---|
| P1 Emergency | 1 hour | 4 hours |
| P2 Urgent | 4 hours | 48 hours |
| P3 Normal | 1 business day | 5 business days |
| P4 Planned | 5 business days | Scheduled date |

Breached SLAs flag amber/red in FM dashboard. Weekly SLA compliance report.

### Contractor Management
- Contractor company register: name, trade, insurance expiry, certifications
- Contractor portal: accept jobs, update status, upload completion photos, submit invoice
- Rating system: FM rates contractor performance per job

### Preventive Maintenance
- Schedule recurring maintenance tasks (e.g., HVAC filter check every 3 months, fire extinguisher inspection annually)
- Auto-creates maintenance request when due date arrives
- Compliance calendar: all certification expiry dates (electrical safety cert, boiler certificate, PAT testing, fire risk assessment)

---

## Data Model

### `workplace_maintenance_requests`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| building_id | ulid | FK |
| floor_id | ulid | nullable FK |
| room_id | ulid | nullable FK |
| reported_by | ulid | FK `employees`, nullable if anonymous |
| category | enum | electrical/hvac/plumbing/cleaning/furniture/it/security/other |
| priority | enum | p1/p2/p3/p4 |
| title | varchar(200) | |
| description | text | |
| status | enum | open/in_progress/pending_parts/resolved/closed |
| assigned_to_employee_id | ulid | nullable |
| assigned_to_contractor_id | ulid | nullable |
| due_at | datetime | computed from priority SLA |
| resolved_at | datetime | nullable |
| estimated_cost | decimal(10,2) | nullable |
| actual_cost | decimal(10,2) | nullable |

### `workplace_contractors`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| trade | varchar(100) | |
| email | varchar | |
| insurance_expiry | date | |
| certifications | json | |
| rating | decimal(3,2) | 0.00–5.00, avg of job ratings |

---

## Integrations

- **Operations** — shares contractor patterns with equipment maintenance
- **Finance** — contractor invoice approval linked to maintenance job
- **Notifications** — SLA breach alerts, completion notifications to reporter

---

## Migration

```
863000_create_workplace_maintenance_requests_table
863001_create_workplace_contractors_table
863002_create_workplace_maintenance_schedules_table
863003_create_workplace_maintenance_attachments_table
```

---

## Related

- [[MOC_Workplace]]
- [[MOC_Operations]] — shared maintenance patterns
- [[workplace-analytics]] — maintenance cost analytics
