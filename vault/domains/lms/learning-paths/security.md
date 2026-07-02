---
domain: lms
module: learning-paths
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Learning Paths — Security

## Permissions

| Permission | Grants |
|---|---|
| `lms.paths.view-any` | View paths + progress |
| `lms.paths.manage` | Create / edit paths |
| `lms.paths.enrol` | Enrol / bulk-assign learners to paths |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.paths.view-any')
        && BillingService::hasModule('lms.paths');
}
```

## Tenant Isolation

- All three tables carry `company_id` (indexed); `CompanyScope` applies.
- Path enrolment always routes through `EnrolmentService`, which runs under `CompanyContext` — no side-door into `lms_enrolments`.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('lms.paths')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
