---
domain: lms
module: certifications
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Certifications — Security

## Permissions

| Permission | Grants |
|---|---|
| `lms.certifications.view-any` | View certificates + templates |
| `lms.certifications.manage-templates` | Create / edit certificate templates |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.certifications.view-any')
        && BillingService::hasModule('lms.certifications');
}
```

## Public Verification Endpoint

- `GET /verify/{number}` is **unauthenticated** and **rate-limited** (throttle) to prevent enumeration.
- Returns a **minimal payload** (`status` + `course_title`) — never learner PII.
- Certificate numbers are globally-unique `FF-{ulid26}` *(assumed)* — **not sequential**, so cross-company numbers are not enumerable.

## Tenant Isolation

- `lms_certificate_templates` + `lms_certificates` carry `company_id` (indexed); `CompanyScope` applies to all admin queries.
- The public verify path deliberately bypasses `CompanyScope` (global number lookup) but exposes only non-tenant-identifying data.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('lms.certifications')`. See [[../../../infrastructure/module-catalog]].

## File / PDF

Generated PDFs stored via core.files under `companies/{company_id}/`, served by signed URL. `GenerateCertificatePdfJob` runs on the `exports` queue.

## Encrypted Fields

None.
