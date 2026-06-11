---
type: module
domain: Learning & Development
domain-key: lms
panel: lms
module-key: lms.certifications
status: planned
priority: p3
depends-on: [lms.enrolments, core.billing, core.rbac, core.notifications, foundation.queues]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [pdf, queues]
tables: [lms_certificate_templates, lms_certificates]
permission-prefix: lms.certifications
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Certifications

Issue certificates on course completion, track validity/expiry, and manage renewals for compliance training. (Issued via direct call from enrolment completion — the v1 `CourseCompleted`/`CertificationExpiring` events dropped, same-domain *(assumed)*.)

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/lms/enrolments\|lms.enrolments]] | completion triggers issue |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, expiry reminders, PDF jobs |

---

## Core Features

- Certificate template: design (logo, text), course association, validity months
- Auto-issue on course completion (when course has template)
- Certificate record: learner, course, issue date, expiry date, unique certificate number
- PDF certificate generation (spatie/laravel-pdf, queued)
- Expiry tracking: certifications that need renewal
- Renewal reminders before expiry (60/14d *(assumed)*, once each) — re-enrol = renewal path
- Public verification: certificate number lookup (no auth, rate-limited, returns valid/expired + course title only)
- Certification report: who holds what, what's expiring

---

## Data Model

### lms_certificate_templates — id, company_id (indexed), name, design (jsonb), course_id nullable, validity_months nullable (null = no expiry)
### lms_certificates

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| learner_type / learner_id | string / ulid | |
| course_id | ulid FK | |
| certificate_number | string | globally unique format `FF-{ulid26}` *(assumed)* |
| issued_at / expires_at | timestamp / nullable | |
| alerted_levels | jsonb default `[]` | 60/14 guards |
| pdf_path | string nullable | |

---

## DTOs

### VerifyCertificateData (public) — certificate_number — rate-limited

## Services & Actions

- `CertificateService::issue(Enrolment $e): Certificate` — called by EnrolmentService on completion; number + expiry from template; PDF job
- `CertificateService::verify(string $number): VerificationResult` — minimal public payload
- `CertificateExpiryCommand`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `CertificateExpiryCommand` | notifications | daily | alerted_levels guards |
| `GenerateCertificatePdfJob` | exports | on issue | overwrites |

---

## Filament

**Nav group:** Certifications

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CertificateTemplateResource` | #1 CRUD resource | design fields |
| `CertificateResource` | #1 (read-only) | expiry filter, PDF download |
| `CertificationExpiryWidget` | #6 widget | expiring 60d |

Public verify page: Vue + Inertia `/verify/{number}` — ui-strategy row #16.


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('lms.certifications.view-any') && BillingService::hasModule('lms.certifications')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Data class** (medium): Add a CreateCertificateTemplateData (spatie/laravel-data) DTO covering name, design, course_id, validity_months for the template write path.

---

## Permissions

`lms.certifications.view-any` · `lms.certifications.manage-templates`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Completion auto-issues when template set; no template = no-op
- [ ] Certificate numbers unique; verification returns minimal data; expired shows expired
- [ ] Expiry alerts once per level
- [ ] Verify endpoint rate-limited; cross-company numbers not enumerable

---

## Build Manifest

```
database/migrations/xxxx_create_lms_certificate_templates_table.php
database/migrations/xxxx_create_lms_certificates_table.php
app/Models/LMS/{CertificateTemplate,Certificate}.php
app/Data/LMS/VerifyCertificateData.php
app/Services/LMS/CertificateService.php
app/Jobs/LMS/GenerateCertificatePdfJob.php
app/Console/Commands/LMS/CertificateExpiryCommand.php
app/Http/Controllers/CertificateVerifyController.php + resources/js/Pages/Verify.vue
app/Filament/LMS/Resources/{CertificateTemplateResource,CertificateResource}.php
app/Filament/LMS/Widgets/CertificationExpiryWidget.php
database/factories/LMS/CertificateFactory.php
tests/Feature/LMS/CertificateTest.php
```

---

## Related

- [[domains/lms/enrolments]]
- [[domains/hr/employee-profiles]]
- [[architecture/packages]] (`spatie/laravel-pdf`)
