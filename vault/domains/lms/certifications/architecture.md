---
domain: lms
module: certifications
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Certifications — Architecture

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\LMS\CertificateService` | service | `issue(Enrolment): Certificate` (called by `EnrolmentService` on completion; number + expiry from template; dispatches PDF job). `verify(string $number): VerificationResult` (minimal public payload). |
| `App\Console\Commands\LMS\CertificateExpiryCommand` | command | Daily; alerts on 60/14d guards. |

### issue flow

1. Enrolment completes → `EnrolmentService` calls `CertificateService::issue(enrolment)`.
2. If the course has no `certificate_template_id` → no-op (return null).
3. Generate a globally-unique `certificate_number` (`FF-{ulid26}` *(assumed)*).
4. Compute `expires_at` from `template.validity_months` (null = no expiry).
5. Insert `lms_certificates` row; dispatch `GenerateCertificatePdfJob`.

### verify flow

- Look up by `certificate_number`; return `VerificationResult` = `{valid|expired, course_title}` only — never learner PII or cross-company enumeration.

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `GenerateCertificatePdfJob` | exports | on issue | overwrites `pdf_path` |
| `CertificateExpiryCommand` | notifications | daily | `alerted_levels` guards (60/14) |

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `CertificateTemplateResource` | Certifications | #1 CRUD resource | Design fields (logo, text, validity). |
| `CertificateResource` | Certifications | #1 (read-only) | Expiry filter, PDF download. |
| `CertificationExpiryWidget` | Certifications | #6 widget | Expiring within 60d. |

Public verify page: Vue + Inertia `/verify/{number}` — ui-strategy row #16.

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.certifications.view-any')
        && BillingService::hasModule('lms.certifications');
}
```

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| `CertificateService::issue` | n-a | Invoked once per enrolment completion (completion transition is locked in enrolments); unique `certificate_number` guards duplicates |
| Certificate template CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |
| `CertificateExpiryCommand` alerts | n-a | Single scheduled writer with 60/14d guards |
| `verify` | n-a | Read-only public lookup |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Events

None fired or consumed cross-domain. `CertificateService::issue` is a same-domain call from enrolments. v1 `CertificationExpiring` event dropped — expiry handled by the daily command.
