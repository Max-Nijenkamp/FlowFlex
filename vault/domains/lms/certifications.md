---
type: module
domain: Learning & Development
panel: lms
module-key: lms.certifications
status: planned
color: "#4ADE80"
---

# Certifications

> Certification templates that are issued automatically on course or path completion, with expiry dates and renewal workflows.

**Panel:** `lms`
**Module key:** `lms.certifications`

---

## What It Does

Certifications manages the full lifecycle of credentials earned through learning. Administrators define certification templates — including branding, validity periods, and the triggering course or path — and the system automatically issues certificates to learners upon completion. Certificates carry expiry dates where relevant, and approaching expiry triggers automated renewal enrollment. Externally obtained certifications (e.g. vendor or regulatory credentials) can also be logged manually against an employee record.

---

## Features

### Core
- Certification template creation: name, description, issuing authority, logo, validity period
- Auto-issuance: certificate issued when linked course or path is passed
- PDF certificate generation: branded, downloadable, shareable URL
- Expiry date tracking: optional validity window (e.g. 1 year, 2 years)
- Renewal reminders: automated notifications at 60/30/7 days before expiry
- Employee certification wallet: all earned certificates in one place on employee profile

### Advanced
- External certification logging: manually record externally obtained credentials with evidence upload
- Certification levels: tiered credentials (e.g. Associate, Professional, Expert)
- QR code verification: public verification link for issued certificates
- Bulk issuance: issue a certification to a group of employees simultaneously
- Certification catalogue: browse available certifications and associated learning paths

### AI-Powered
- Expiry risk scoring: predict which employees are most at risk of letting certifications lapse
- Renewal path recommendation: suggest the most efficient course sequence for renewal
- Compliance gap detection: cross-reference required certifications vs actual holdings by role

---

## Data Model

```erDiagram
    certification_templates {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string issuing_authority
        string logo_url
        integer validity_days
        ulid trigger_course_id FK
        ulid trigger_path_id FK
        timestamps created_at_updated_at
    }

    issued_certifications {
        ulid id PK
        ulid template_id FK
        ulid employee_id FK
        string certificate_url
        string verification_code
        date issued_at
        date expires_at
        boolean is_external
        string external_source
        timestamps created_at_updated_at
    }

    certification_templates ||--o{ issued_certifications : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `certification_templates` | Credential definitions | `id`, `company_id`, `name`, `validity_days`, `trigger_course_id` |
| `issued_certifications` | Employee credentials | `id`, `template_id`, `employee_id`, `issued_at`, `expires_at`, `is_external` |

---

## Permissions

```
lms.certifications.view-any
lms.certifications.manage-templates
lms.certifications.issue-manually
lms.certifications.view-employee-certifications
lms.certifications.export
```

---

## Filament

- **Resource:** `App\Filament\Lms\Resources\CertificationResource`
- **Pages:** `ListCertifications`, `CreateCertification`, `EditCertification`, `ViewCertification`
- **Custom pages:** `EmployeeCertificationWalletPage`, `CertificationCataloguePage`
- **Widgets:** `ExpiringCertificationsWidget`, `CertificationComplianceWidget`
- **Nav group:** Catalog

---

## Displaces

| Feature | FlowFlex | Cornerstone | Docebo | TalentLMS |
|---|---|---|---|---|
| Auto-issuance on completion | Yes | Yes | Yes | Yes |
| Expiry and renewal workflows | Yes | Yes | Partial | No |
| External certification logging | Yes | Partial | No | No |
| QR code verification | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**PDF certificate generation:** `issued_certifications.certificate_url` references a PDF in S3/R2. The generation pipeline is: `CourseCompleted` or `PathCompleted` event → `IssueCertificationJob` (queued) → `GenerateCertificatePdf` service → `barryvdh/laravel-dompdf` renders `resources/views/certificates/template.blade.php` → upload to S3 via `spatie/laravel-media-library` → store URL in `issued_certifications.certificate_url`.

**PDF package:** `barryvdh/laravel-dompdf` is not currently in the tech stack. Add it to `composer.json`. Alternative: `spatie/browsershot` (Puppeteer-based) produces higher-quality output for complex branded templates but requires Node.js + Puppeteer in the Docker image. For simple certificate layouts, dompdf is sufficient.

**QR code verification:** The `verification_code` column stores a short random token (e.g. `ulid()` first 12 chars). The public verification URL is `https://app.flowflex.com/verify/{verification_code}` — a public route (no auth) that returns the certificate details. Generate QR code at PDF render time using `endroid/qr-code` package (add to `composer.json`).

**Renewal reminders:** Dispatched by a daily scheduled job (`CertificationExpiryReminderJob`) that queries `issued_certifications` where `expires_at` is within 60/30/7 days and `is_external = false`. Fires `CertificationExpiryReminderNotification` routed through the notifications module. The renewal enrollment logic: when expiry is within 30 days, auto-enroll in the linked course or path if it exists.

**Filament:** `EmployeeCertificationWalletPage` and `CertificationCataloguePage` are custom `Page` classes. The wallet page displays an employee's issued certifications as a card grid — not a standard table. The catalogue page shows all available `certification_templates` with enroll/path links. Both are read-only displays driven by Eloquent queries.

**AI features:** Expiry risk scoring and compliance gap detection are PHP-only computations — no LLM required. Risk scoring: days until expiry / average renewal lead time per certification type. Gap detection: join `role_skill_requirements` (from talent module) with `issued_certifications` per employee, grouped by role.

## Related

- [[courses]] — certifications are triggered by course completion
- [[learning-paths]] — paths can also trigger certification issuance
- [[compliance-training]] — compliance certifications tracked here
- [[skills]] — certifications can be linked to skill proficiency levels
- [[analytics]] — certification coverage and expiry reports
