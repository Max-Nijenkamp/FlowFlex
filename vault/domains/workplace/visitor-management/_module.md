---
domain: workplace
module: visitor-management
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Visitor Management

Register and track office visitors: pre-registration, check-in/out, host notification, and a visitor log for security / compliance.

## Module-key

| Field | Value |
|---|---|
| key | `workplace.visitors` |
| priority | p3 |
| panel | workplace |
| permission-prefix | `workplace.visitors` |
| tables | `wp_visitors` |
| encrypted-fields | `wp_visitors.name`, `wp_visitors.email` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|hr.profiles]] | hosts are employees |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions, `canAccess()` |
| Hard | [[../../core/notifications/_module\|core.notifications]] | in-app arrival notifications |
| Hard | [[../../foundation/email-setup/_module\|foundation.email]] | visitor confirmation + host arrival mail |

## Core Features

- **Pre-registration** — host registers an expected visitor with date/time; confirmation mail to visitor *(assumed)*. See [[features/pre-registration|Pre-registration]].
- **Check-in / check-out (kiosk)** — visitor signs in at a kiosk or reception; badge + host notification + optional NDA. See [[features/check-in|Check-in & Kiosk]].
- **Host notification** — in-app + email on arrival (part of check-in).
- **Visitor log** — who visited, when, who they saw (compliance / security). See [[features/visitor-log|Visitor Log]].
- **GDPR purge** — external-visitor PII purged after 12 months *(assumed)*. See [[features/gdpr-purge|GDPR Purge]].

## See features/

- [[features/pre-registration|Pre-registration]] · [[features/check-in|Check-in & Kiosk]] · [[features/visitor-log|Visitor Log]] · [[features/gdpr-purge|GDPR Purge]]

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Check-in assigns badge + notifies host (in-app + mail).
- [ ] Declaration required when enabled (no check-in without).
- [ ] Walk-in check-in without pre-registration works.
- [ ] 12-month PII purge.
- [ ] Kiosk page requires kiosk role; rate-limited.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none confirmed)* | — | No cross-domain event specified *(assumed)*; a `VisitorArrived` event could feed comms/security — undecided ([[unknowns]]). |
| Reads | host directory | hr.profiles | hosts resolved from HR employees (read-only) |
| Commands | in-app arrival ping | core.notifications | host notified on arrival |
| Commands | confirmation + arrival mail | foundation.email | via Mailables |

**Data ownership:** `workplace.visitors` writes only `wp_visitors`. Hosts are read from `hr.profiles`; notifications + mail are dispatched through `core.notifications` / `foundation.email`. No other domain's tables are written ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../hr/employee-profiles/_module|hr.profiles]] · [[../../../architecture/data-lifecycle]] · [[../../../security/encryption]]
