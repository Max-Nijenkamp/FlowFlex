---
domain: lms
module: skills-matrix
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Skills Matrix — Security

## Permissions

| Permission | Grants |
|---|---|
| `lms.skills.view-any` | View catalogue + matrix |
| `lms.skills.manage` | Manage skills catalogue + role requirements + course links |
| `lms.skills.assess-own` | Self-assess (own employee record only) |
| `lms.skills.assess-reports` | Assess direct reports (HR reporting-line scope) |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.skills.view-any')
        && BillingService::hasModule('lms.skills');
}
```

## Assessment Scope

- `assess-own` writes are restricted to the acting user's **own** employee record.
- `assess-reports` writes are restricted to the acting user's **direct reports**, derived from HR's reporting line (read-only).
- Self and manager assessments are stored separately (unique per assessor type); manager is authoritative for gaps *(assumed)*.

## Tenant Isolation

All four tables carry `company_id` (indexed); `CompanyScope` applies. Employee/reporting data is read from HR under the same company context.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('lms.skills')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None. (Proficiency is not sensitive PII, but is scoped to managers/self — see assessment scope.)
