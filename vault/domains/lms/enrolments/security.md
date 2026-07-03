---
domain: lms
module: enrolments
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Enrolments — Security

## Permissions

| Permission | Grants |
|---|---|
| `lms.enrolments.view-any` | View the enrolments list + compliance |
| `lms.enrolments.enrol` | Enrol / bulk-enrol learners |
| `lms.enrolments.manage` | Edit / drop enrolments |
| *(self)* | Learner self-access via portal token or user link |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.enrolments.view-any')
        && BillingService::hasModule('lms.enrolments');
}
```

## Learner Portal Guard (HIGH — per [[../../../_archive/build-history/security-audit-2026-06-11|security audit]])

- The `/learn` portal authenticates **external learners** via a **Sanctum scoped portal (learner) guard**.
- `lms_learners.portal_token` **issuance and rotation flow through that guard** — never ad-hoc token comparison in a controller.
- Employees reach the portal through their normal user session (learner = user link).
- **Own-data scope** is the headline test: a learner (token path or user path) can read only their own enrolments/progress; cross-learner ids 403/404.

## Rate Limiting (medium)

- The **bulk-enrol** action carries a per-user throttle to prevent enrolment storms.

## Tenant Isolation

- `lms_enrolments` + `lms_learners` carry `company_id` (indexed); `CompanyScope` applies. `lms_learners.email` unique per company.
- The `AutoEnrolOnHireListener` runs under `WithCompanyContext` — the event's `company_id` scalar scopes the enrol.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('lms.enrolments')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None. Learner email/name stored plaintext *(assumed)* — see [[unknowns]].
