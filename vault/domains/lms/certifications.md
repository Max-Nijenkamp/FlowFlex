---
type: module
domain: Learning & Development
panel: lms
module-key: lms.certifications
status: planned
color: "#4ADE80"
---

# Certifications

Issue certificates on course completion, track validity/expiry, and manage renewals for compliance training.

## Core Features

- Certificate template: design (logo, text, signature), course association
- Auto-issue on course completion
- Certificate record: learner, course, issue date, expiry date, unique certificate number
- PDF certificate generation (spatie/laravel-pdf)
- Expiry tracking: certifications that need renewal
- Renewal reminders before expiry
- Public verification: certificate number lookup
- Certification report: who holds what, what's expiring

## Data Model

| Table | Key Columns |
|---|---|
| `lms_certificate_templates` | company_id, name, design (json), course_id, validity_months |
| `lms_certificates` | company_id, learner_id, course_id, certificate_number, issued_at, expires_at, pdf_path |

## Filament

**Nav group:** Certifications

- `CertificateTemplateResource` — design templates
- `CertificateResource` — list issued certificates, expiry filter
- `CertificationExpiryWidget` — upcoming expiries

## Cross-Domain / Events

- Consumes `CourseCompleted` → issue certificate
- Fires `CertificationExpiring` → notify learner + HR

## Related

- [[domains/lms/enrolments]]
- [[domains/hr/employee-profiles]]
- `spatie/laravel-pdf`
