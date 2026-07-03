---
domain: lms
module: lessons
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Lessons — Security

## Permissions

| Permission | Grants |
|---|---|
| `lms.lessons.manage` | Create / edit / reorder lessons + quizzes (under course permissions) |
| `lms.lessons.view-any` | View lessons in the admin panel |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.lessons.view-any')
        && BillingService::hasModule('lms.lessons');
}
```

## Quiz Answer Confidentiality

- `lms_quizzes.questions[].correct` is **never serialized to the learner client**. Grading is server-side (`QuizService::grade`). The learner submits answers; only `QuizResult {score, passed, best}` returns.
- This is the module's headline security test.

## Upload Contract

Video / file lesson content goes through core.files:
- **Allowed MIME/type whitelist** (video, common docs), **max file size**, **`companies/{company_id}/` storage path**.
- Video served via **signed URL** only — no public object URLs.
- Embed URLs whitelisted to youtube/vimeo; any other host rejected at write.

(Per [[../../../_archive/build-history/security-audit-2026-06-11|security audit]] medium finding — upload constraints on the video/file content section.)

## Tenant Isolation

All three tables carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` applies. `lms_lesson_progress` is additionally scoped to the learner's enrolment.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('lms.lessons')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None.
