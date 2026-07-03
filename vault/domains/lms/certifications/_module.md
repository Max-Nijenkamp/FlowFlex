---
domain: lms
module: certifications
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Certifications

Issue certificates on course completion, track validity/expiry, and manage renewals for compliance training.

## Module-key

| Field | Value |
|---|---|
| key | `lms.certifications` |
| priority | p3 |
| panel | lms |
| permission-prefix | `lms.certifications` |
| tables | `lms_certificate_templates`, `lms_certificates` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../enrolments/_module\|Enrolments]] | Completion triggers issue |
| Hard | [[../../core/billing/_module\|Billing]] + [[../../core/rbac/_module\|RBAC]] + [[../../core/notifications/_module\|Notifications]] | Gating, permissions, expiry reminders |
| Hard | [[../../foundation/queue-workers/_module\|Queue Workers]] | PDF generation jobs |

## Core Features

- **Certificate template** — design (logo, text), course association, validity months.
- **Auto-issue** on course completion (when the course has a template).
- **Certificate record** — learner, course, issue/expiry dates, unique certificate number.
- **PDF generation** — `spatie/laravel-pdf`, queued.
- **Expiry tracking + renewal reminders** — 60/14d *(assumed)*, once each; re-enrol = renewal path.
- **Public verification** — certificate-number lookup, no auth, rate-limited, minimal payload.
- **Certification report** — who holds what, what's expiring.

## See features/

- [[features/certificate-issuance|Certificate Issuance]] — auto-issue + template management (background + resource).
- [[features/public-verification|Public Verification]] — `/verify/{number}` lookup (public-vue).
- [[features/expiry-renewal|Expiry & Renewal]] — expiry alerts + renewal path (background).

## Build Manifest

```
database/migrations/xxxx_create_lms_certificate_templates_table.php
database/migrations/xxxx_create_lms_certificates_table.php
app/Models/LMS/{CertificateTemplate,Certificate}.php
app/Data/LMS/{VerifyCertificateData,CreateCertificateTemplateData}.php
app/Services/LMS/CertificateService.php
app/Jobs/LMS/GenerateCertificatePdfJob.php
app/Console/Commands/LMS/CertificateExpiryCommand.php
app/Http/Controllers/CertificateVerifyController.php + resources/js/Pages/Verify.vue
app/Filament/LMS/Resources/{CertificateTemplateResource,CertificateResource}.php
app/Filament/LMS/Widgets/CertificationExpiryWidget.php
database/factories/LMS/CertificateFactory.php
tests/Feature/LMS/CertificateTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's certifications data
- [ ] Module gating: artifacts hidden when `lms.certifications` inactive
- [ ] Completion auto-issues when template set; no template = no-op.
- [ ] Certificate numbers unique; verification returns minimal data; expired shows expired.
- [ ] Expiry alerts once per level.
- [ ] Verify endpoint rate-limited; cross-company numbers not enumerable.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Commanded by | `CertificateService::issue(Enrolment)` | lms.enrolments | Enrolments calls this on completion (same-domain) |
| Reads | `NotificationService` | core.notifications | Expiry reminders |
| Reads | file-storage / PDF | core.files + foundation.queues | Generated PDF stored + served |
| Fires | *(none)* | — | Public verify is a read endpoint, not an event |

**Data ownership:** `lms.certifications` writes only `lms_certificate_templates` + `lms_certificates`. It is invoked by enrolments' service (never the reverse write), and stores PDFs via core.files ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../enrolments/_module|Enrolments]] · [[../../../architecture/packages|packages (spatie/laravel-pdf)]]
- [[../_index|LMS index]]
