---
type: module
domain: Whistleblowing & Ethics Hotline
panel: whistleblowing
module: Anonymous Intake Portal
phase: 4
status: planned
cssclasses: domain-whistleblowing
migration_range: 1000000–1000499
last_updated: 2026-05-09
---

# Anonymous Intake Portal

Public-facing report submission form that preserves full reporter anonymity. No login required. No IP logging. Generates a one-time `report_token` the reporter uses to track their case.

---

## Why This Exists

EU Directive 2019/1937 requires a **confidential reporting channel** with identity protection. Email or a named form does not qualify. The intake portal must strip all identifying metadata before storing the report.

---

## Key Tables

```sql
-- Report container (anonymised at creation)
CREATE TABLE ethics_reports (
    id          ULID PRIMARY KEY,
    company_id  ULID NOT NULL REFERENCES companies(id),
    report_token VARCHAR(64) UNIQUE NOT NULL,  -- shown to reporter once
    category    ENUM('fraud','harassment','safety','data_breach','competition','other'),
    description TEXT NOT NULL,
    severity    ENUM('low','medium','high','critical') DEFAULT 'medium',
    channel     ENUM('web','phone','email','in_person') DEFAULT 'web',
    anonymous   BOOLEAN DEFAULT TRUE,
    reporter_alias VARCHAR(100) NULL,          -- optional: "Concerned Employee"
    submitted_at TIMESTAMP NOT NULL,
    ip_stripped  BOOLEAN DEFAULT TRUE,         -- always true for web
    created_at  TIMESTAMP DEFAULT NOW()
);

-- Attachments stored with report (evidence)
CREATE TABLE ethics_report_attachments (
    id          ULID PRIMARY KEY,
    report_id   ULID NOT NULL REFERENCES ethics_reports(id),
    filename    VARCHAR(255),
    storage_path VARCHAR(500),
    file_size   INT,
    mime_type   VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT NOW()
);
```

---

## Anonymity Implementation

1. Web form served on separate subdomain (`report.{company}.flowflex.io`) or custom domain
2. No cookies, no session tracking on intake form
3. IP address stripped before writing to DB (middleware strips `REMOTE_ADDR` before controller fires)
4. User agent not stored
5. `report_token` = `bin2hex(random_bytes(32))` — shown once at submission, never derivable
6. Attachments run through EXIF metadata stripping before storage

---

## Report Categories

| Category | Example |
|---|---|
| Fraud & Financial Misconduct | Expense fraud, false invoices, embezzlement |
| Harassment & Discrimination | Sexual harassment, bullying, racial discrimination |
| Health & Safety | Unsafe conditions, accident cover-ups |
| Data & Privacy | GDPR violations, data leaks |
| Competition Law | Cartel behaviour, bid rigging |
| Environmental | Unreported pollution, waste violations |
| Other | General misconduct not covered above |

---

## Public Form Fields

- Category (required)
- Description of concern (required, min 50 chars)
- Date(s) of incident (optional)
- Names involved (optional — can be job titles only)
- Frequency (one-time / recurring)
- Evidence upload (optional, max 3 files, 10MB each)
- Preferred language for follow-up
- Reporter name/alias (optional — auto-assigns "Anonymous Witness" if blank)

---

## Filament Admin

**Panel:** `whistleblowing` — intake form is a public Blade/Vue page, not Filament  
Filament used for: case manager view of incoming reports, assignment, status updates

---

## Service / Interface

```php
interface ReportIntakeInterface
{
    public function submit(SubmitReportDTO $dto): ReportTokenDTO;
    public function attachFile(string $reportToken, UploadedFile $file): void;
    public function getPublicStatus(string $reportToken): ReportStatusDTO;
}
```

---

## Related

- [[MOC_Whistleblowing]]
- [[case-management-investigation]]
- [[reporter-communication-portal]]
