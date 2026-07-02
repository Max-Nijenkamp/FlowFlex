---
domain: lms
module: courses
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Courses — Security

## Permissions

| Permission | Grants |
|---|---|
| `lms.courses.view-any` | View the course list + records |
| `lms.courses.create` | Create courses |
| `lms.courses.update` | Edit courses + reorder modules |
| `lms.courses.publish` | Publish / archive courses |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.courses.view-any')
        && BillingService::hasModule('lms.courses');
}
```

Draft courses are never surfaced to learners — the enrolments portal reads `published` courses only.

## Tenant Isolation

- `lms_courses` + `lms_course_modules` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries.
- Slug uniqueness is per-company, never global.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('lms.courses')` gates panel access. See [[../../../infrastructure/module-catalog]].

## File Uploads

Course thumbnails go through core.files: MIME/type whitelist (images), size cap, `companies/{company_id}/` path. Served via signed URL. See [[../../core/file-storage/_module|File Storage]].

## Encrypted Fields

None.
