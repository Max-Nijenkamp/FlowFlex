---
type: module
domain: Workplace & Facility
domain-key: workplace
panel: workplace
module-key: workplace.visitors
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications, foundation.email]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [pdf, gdpr, custom-pages]
tables: [wp_visitors]
permission-prefix: workplace.visitors
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Visitor Management

Register and track office visitors: pre-registration, check-in/out, host notification, and visitor log for security/compliance.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | hosts are employees |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] + [[domains/foundation/email-setup\|foundation.email]] | gating, permissions, arrival notifications |

---

## Core Features

- Pre-registration: host registers an expected visitor with date/time (confirmation mail to visitor *(assumed)*)
- Check-in: visitor arrives, signs in (kiosk page or reception)
- Host notification: in-app + email on arrival
- Visitor badge generation (PDF, badge number)
- Check-out tracking
- Visitor log: who visited, when, who they saw (compliance/security)
- NDA / declaration sign on check-in (optional toggle; acceptance timestamp stored *(assumed: checkbox + text, no e-sign)*)
- Recurring visitors (contractors) — re-register from history
- GDPR: visitor PII purged after 12 months *(assumed)* ([[architecture/data-lifecycle]])

---

## Data Model

### wp_visitors

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name / company_name / email | string | email nullable |
| host_employee_id | ulid FK | |
| expected_at | timestamp | |
| checked_in_at / checked_out_at | timestamp nullable | |
| badge_number | string nullable | assigned at check-in |
| declaration_accepted_at | timestamp nullable | |
| purpose | string nullable | |

**Indexes:** `(company_id, expected_at)`

---

## DTOs

### PreRegisterVisitorData — name (required), company_name?, email?, host = current user's employee (or chosen with permission), expected_at (future), purpose?
### KioskCheckInData (kiosk page) — visitor lookup (today's expected by name) or walk-in fields; declaration acceptance when enabled

## Services & Actions

- `VisitorService::checkIn(...)` — badge number, host notification, declaration gate
- `CheckOutAction`; `PurgeVisitorsCommand` (12-month guard)
- Kiosk page: authenticated device session *(assumed: dedicated kiosk user role, no public route)*

---

## Filament

**Nav group:** Visitors

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `VisitorResource` | #1 CRUD resource | pre-register, check-in/out actions, log filters |
| `VisitorKioskPage` | #7 custom page | self-service check-in (kiosk role) |

---

## Permissions

`workplace.visitors.pre-register` (all users) · `workplace.visitors.manage` · `workplace.visitors.kiosk` (kiosk role)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Check-in assigns badge + notifies host (in-app + mail)
- [ ] Declaration required when enabled (no check-in without)
- [ ] Walk-in check-in without pre-registration works
- [ ] 12-month purge
- [ ] Kiosk page requires kiosk role

---

## Build Manifest

```
database/migrations/xxxx_create_wp_visitors_table.php
app/Models/Workplace/Visitor.php
app/Data/Workplace/{PreRegisterVisitorData,KioskCheckInData}.php
app/Services/Workplace/VisitorService.php
app/Actions/Workplace/CheckOutAction.php
app/Jobs/Workplace/GenerateVisitorBadgeJob.php
app/Console/Commands/Workplace/PurgeVisitorsCommand.php
app/Mail/Workplace/{VisitorArrivedMail,VisitorConfirmationMail}.php
app/Filament/Workplace/Resources/VisitorResource.php
app/Filament/Workplace/Pages/VisitorKioskPage.php
database/factories/Workplace/VisitorFactory.php
tests/Feature/Workplace/VisitorTest.php
```

---

## Related

- [[domains/hr/employee-profiles]]
- [[architecture/data-lifecycle]]
