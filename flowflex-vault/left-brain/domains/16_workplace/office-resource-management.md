---
type: module
domain: Workplace & Facility Management
panel: workplace
cssclasses: domain-workplace
phase: 6
status: complete
migration_range: 866000–867999
last_updated: 2026-05-12
---

# Office Resource Management

Manage shared office resources: parking spaces, lockers, equipment loans (laptops, projectors, camera kits). Employees request and return, system tracks utilisation.

---

## Resource Types

### Parking Spaces
- Register parking spaces per building (bays, EV charging spots, motorbike spaces)
- Daily/weekly booking (same booking engine as desks)
- EV charging session tracking (if integrated with charge point management)
- Visitor parking allocation (reserved by Visitor Management module)
- Waiting list when full

### Lockers
- Assign permanent or day-use lockers
- Locker combinations/codes managed centrally — reset on release
- Smart locker integration (electronic locks via API) for keyless access
- Report: lockers unused for 30+ days → prompt permanent holder to release

### Equipment Loans
Equipment catalogue:
- Laptops / tablets (loan units for visitors, contractors, new starters)
- Projectors / presentation screens
- Camera kits (for content creation)
- Conference phones / webcams (for remote meeting rooms)
- Portable monitors

Loan workflow:
1. Employee requests equipment → specify return date
2. IT/Facilities staff approves (or auto-approve for standard items)
3. Serial number scanned out — linked to employee record
4. Employee returns → serial number scanned in → condition check
5. Lost/damaged → HR notified (potential deduction process)

---

## Data Model

### `workplace_resources`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| type | enum | parking/locker/equipment |
| name | varchar(200) | "Parking Bay A12", "Locker 045", "MacBook 13 #3" |
| building_id | ulid | FK |
| serial_number | varchar | nullable |
| attributes | json | e.g. {"ev_charging": true, "floor": "B1"} |
| status | enum | available/on_loan/maintenance/retired |
| assigned_employee_id | ulid | nullable (permanent assignment) |

### `workplace_resource_loans`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| resource_id | ulid | FK |
| employee_id | ulid | FK |
| loaned_at | datetime | |
| due_at | datetime | nullable |
| returned_at | datetime | nullable |
| condition_out | enum | new/good/fair/poor |
| condition_in | enum | nullable |
| notes | text | nullable |
| late_flag | bool | computed: due_at < now() AND returned_at IS NULL |

---

## Business Rules

- Overdue loan alert: notify employee + manager when return date passed
- Lost item: if not returned 14 days past due → auto-flag for HR review
- Equipment self-service: low-risk items (cables, mice, keyboards) → no approval required, just log
- EV charging: if charge point API connected, log kWh per session, charge to employee's department cost centre

---

## Integrations

- **IT Asset Management** — equipment loans link to IT asset registry (same serial number record)
- **HR** — offboarding trigger: check all open loans for departing employee
- **Finance** — equipment replacement costs, department chargebacks for parking

---

## Migration

```
866000_create_workplace_resources_table
866001_create_workplace_resource_loans_table
```

---

## Related

- [[MOC_Workplace]]
- [[hot-desk-space-booking]]
- [[MOC_IT]] — asset management overlap
- [[MOC_HR]] — offboarding: return all resources
