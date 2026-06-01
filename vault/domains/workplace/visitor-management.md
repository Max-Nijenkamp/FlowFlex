---
type: module
domain: Workplace & Facility
panel: workplace
module-key: workplace.visitors
status: planned
color: "#4ADE80"
---

# Visitor Management

Register and track office visitors: pre-registration, check-in/out, host notification, and visitor log for security/compliance.

## Core Features

- Pre-registration: host registers an expected visitor with date/time
- Check-in: visitor arrives, signs in (kiosk or reception)
- Host notification: host alerted when their visitor arrives (in-app + email)
- Visitor badge generation (PDF)
- Check-out tracking
- Visitor log: who visited, when, who they saw (compliance/security)
- NDA / health declaration sign on check-in (optional)
- Recurring visitors (contractors)

## Data Model

| Table | Key Columns |
|---|---|
| `wp_visitors` | company_id, name, company_name, email, host_employee_id, expected_at, checked_in_at, checked_out_at, badge_number |

## Filament

**Nav group:** Visitors

- `VisitorResource` — pre-register, check-in/out, visitor log
- `VisitorKioskPage` (custom page) — self-service check-in kiosk view

## Cross-Domain / Events

- Host notification via Core Notifications + email

## Related

- [[domains/hr/employee-profiles]]
- [[architecture/email]]
